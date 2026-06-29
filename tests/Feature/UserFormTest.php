<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_create_user_auto_normalizes_nik_and_phone(): void
    {
        $this->actingAs(User::factory()->admin()->create(['email' => 'root@ctg.test']));

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Budi Santoso',
                'username' => 'budi',
                'email' => 'budi@example.com',
                'nik' => '1234-5678-9012-3456',
                'phone' => '081212350164',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('username', 'budi')->firstOrFail();
        $this->assertSame('1234567890123456', $user->nik);        // stored raw 16 digits
        $this->assertSame('+62 812 1235 0164', $user->phone);     // stored pretty +62 form
    }

    public function test_create_user_accepts_already_plus62_phone(): void
    {
        $this->actingAs(User::factory()->admin()->create(['email' => 'root2@ctg.test']));

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Siti',
                'username' => 'siti',
                'email' => 'siti@example.com',
                'nik' => '3201234567890002',
                'phone' => '+6281212350164',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame('+62 812 1235 0164', User::where('username', 'siti')->firstOrFail()->phone);
    }

    public function test_invalid_nik_and_phone_are_rejected(): void
    {
        $this->actingAs(User::factory()->admin()->create(['email' => 'root3@ctg.test']));

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Salah Data',
                'username' => 'salah',
                'email' => 'salah@example.com',
                'nik' => '123',     // not 16 digits
                'phone' => '12345',  // not a valid ID mobile
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])
            ->call('create')
            ->assertHasFormErrors(['nik', 'phone']);
    }

    public function test_profile_saves_identity_without_password_and_normalizes(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'me@ctg.test',
            'username' => 'meadmin',
            'name' => 'Old Name',
            'nik' => '1111222233334444',
        ]);
        $this->actingAs($admin);

        Livewire::test(\App\Filament\Auth\EditProfile::class)
            ->fillForm([
                'name' => 'New Name',
                'username' => 'meadmin',          // unchanged — must ignore self on unique
                'email' => 'me@ctg.test',         // unchanged — must ignore self on unique
                'phone' => '081299998888',
                'nik' => '3201234567890009',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $admin->refresh();
        $this->assertSame('New Name', $admin->name);
        $this->assertSame('3201234567890009', $admin->nik);          // stored 16 raw digits
        $this->assertSame('+62 812 9999 8888', $admin->phone);       // normalized
    }
}
