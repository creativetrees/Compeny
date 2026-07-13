<?php

namespace Tests\Feature;

use App\Filament\Resources\Leads\Pages\EditLead;
use App\Models\Lead;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LeadFormMessageFieldTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_editing_a_lead_message_saves_plain_text_not_wysiwyg_html(): void
    {
        $admin = User::factory()->admin()->create();
        $lead = Lead::create([
            'name' => 'Jane', 'email' => 'jane@example.com', 'message' => 'Original',
            'status' => 'new', 'source' => 'website',
        ]);

        Livewire::actingAs($admin)
            ->test(EditLead::class, ['record' => $lead->id])
            ->fillForm(['message' => 'Plain typed reply, no formatting.'])
            ->call('save')
            ->assertHasNoFormErrors();

        // A RichEditor always wraps saved content in a <p> tag; a plain textarea
        // stores exactly what was typed. This proves the field is plain text now,
        // matching how the message is actually rendered everywhere else.
        $this->assertSame('Plain typed reply, no formatting.', $lead->fresh()->message);
    }
}
