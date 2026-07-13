<?php

namespace Tests\Feature;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadMessageRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_malicious_lead_message_cannot_inject_html_into_the_admin_view(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'leadview@creativetrees.group']);

        $lead = Lead::create([
            'name' => 'Attacker',
            'email' => 'attacker@example.test',
            'message' => 'Hi <img src="https://evil.example/pixel.gif"> please <a href="https://phishing.example">click here</a>',
            'status' => 'new',
            'source' => 'start_form',
        ]);

        $response = $this->actingAs($admin)
            ->get(LeadResource::getUrl('view', ['record' => $lead]))
            ->assertSuccessful();

        // Filament's own chrome legitimately has <img> tags (avatar) elsewhere on the
        // page, so assert against the specific injected payload, not any <img>/<a>.
        $response->assertDontSee('src="https://evil.example/pixel.gif"', false);
        $response->assertDontSee('href="https://phishing.example"', false);
        $response->assertSee('&lt;img', false);
        $response->assertSee('please', false);
        $response->assertSee('click here', false);
    }
}
