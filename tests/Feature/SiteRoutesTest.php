<?php

namespace Tests\Feature;

use App\Mail\NewLeadMail;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SiteRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_public_pages_render(): void
    {
        $this->seed();

        $paths = ['/', '/work', '/services', '/process', '/pricing', '/products', '/team', '/about', '/start', '/contact'];
        foreach ($paths as $path) {
            $this->get($path)->assertOk();
        }

        $slug = Project::query()->published()->value('slug');
        $this->get("/work/{$slug}")->assertOk();
    }

    public function test_lead_submission_stores_emails_and_notifies(): void
    {
        $this->seed();
        Mail::fake();

        $this->post('/start', [
            'name' => 'Test Person',
            'email' => 'test@example.com',
            'company' => 'Acme',
            'budget' => '$25k–$50k',
            'service_interest' => 'Web Engineering',
            'message' => 'We want to build something that scales.',
        ])->assertRedirect();

        $this->assertDatabaseHas('leads', ['email' => 'test@example.com', 'status' => 'new']);
        Mail::assertSent(NewLeadMail::class);
        $this->assertDatabaseHas('notifications', ['notifiable_type' => User::class]);
    }

    public function test_lead_requires_name_email_message(): void
    {
        $this->post('/start', [])->assertSessionHasErrors(['name', 'email', 'message']);
    }
}
