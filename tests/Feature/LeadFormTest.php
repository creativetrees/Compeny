<?php

namespace Tests\Feature;

use App\Events\LeadReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LeadFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_honeypot_silently_drops_bot_submissions(): void
    {
        Event::fake();

        $this->post('/start', [
            'name' => 'Spam Bot',
            'email' => 'bot@spam.test',
            'message' => 'buy cheap things',
            'company_url' => 'http://spam.example', // hidden field — only bots fill it
        ])->assertRedirect(route('start'));

        $this->assertDatabaseCount('leads', 0);          // no row created
        Event::assertNotDispatched(LeadReceived::class);  // no mail / notification fan-out
    }

    public function test_invalid_email_is_rejected(): void
    {
        $this->post('/start', [
            'name' => 'Real Person',
            'email' => 'not-an-email',
            'message' => 'A genuine enquiry that is long enough.',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('leads', 0);
    }

    public function test_overlong_fields_are_rejected(): void
    {
        $this->post('/start', [
            'name' => str_repeat('a', 121),      // max:120
            'email' => 'real@example.com',
            'message' => str_repeat('m', 4001),  // max:4000
        ])->assertSessionHasErrors(['name', 'message']);
    }

    public function test_valid_submission_creates_lead_and_fires_event(): void
    {
        Event::fake();

        $this->post('/start', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'message' => 'We would like to build a product that scales.',
        ])->assertRedirect(route('start'));

        $this->assertDatabaseHas('leads', [
            'email' => 'jane@example.com',
            'status' => 'new',
            'source' => 'website',
        ]);
        Event::assertDispatched(LeadReceived::class);
    }

    public function test_throttle_blocks_rapid_submissions(): void
    {
        Event::fake();

        for ($i = 0; $i < 6; $i++) {
            $this->post('/start', [
                'name' => "P{$i}",
                'email' => "p{$i}@example.com",
                'message' => 'A valid enquiry message that is long enough.',
            ])->assertRedirect();
        }

        $this->post('/start', [
            'name' => 'P7',
            'email' => 'p7@example.com',
            'message' => 'A valid enquiry message that is long enough.',
        ])->assertStatus(429);
    }
}
