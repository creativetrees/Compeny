<?php

namespace Tests\Feature;

use App\Filament\Resources\SiteSettings\Pages\EditSiteSetting;
use App\Models\SiteSetting;
use App\Support\MailAccounts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MailAccountsTest extends TestCase
{
    use RefreshDatabase;

    private function settings(): SiteSetting
    {
        return SiteSetting::query()->firstOrCreate(['id' => 1]);
    }

    public function test_smtp_password_is_stored_encrypted_not_plaintext(): void
    {
        $setting = $this->settings();
        $setting->email_secrets = ['support@creativetreesgroup.com' => 'S3cret-Pass!'];
        $setting->save();

        // The raw DB column must be ciphertext — never the plaintext password.
        $raw = (string) DB::table('site_settings')->where('id', $setting->id)->value('email_secrets');
        $this->assertStringNotContainsString('S3cret-Pass!', $raw);
        $this->assertNotSame('', $raw);

        // …but a fresh read decrypts it transparently.
        $fresh = SiteSetting::query()->find($setting->id);
        $this->assertSame('S3cret-Pass!', $fresh->email_secrets['support@creativetreesgroup.com']);
    }

    public function test_password_prefers_cms_secret_then_falls_back_to_env(): void
    {
        $setting = $this->settings();
        $setting->emails = [
            ['role' => 'support', 'address' => 'support@creativetreesgroup.com'],
            ['role' => 'sales', 'address' => 'sales@creativetreesgroup.com'],
        ];
        $setting->email_secrets = ['support@creativetreesgroup.com' => 'cms-pw'];
        $setting->save();

        // CMS secret (keyed by address) wins.
        $this->assertSame('cms-pw', MailAccounts::password(['role' => 'support', 'address' => 'support@creativetreesgroup.com']));

        // No CMS secret → .env fallback keyed by role.
        config()->set('mail_accounts.passwords.sales', 'env-pw');
        $this->assertSame('env-pw', MailAccounts::password(['role' => 'sales', 'address' => 'sales@creativetreesgroup.com']));

        // Neither set → null.
        $this->assertNull(MailAccounts::password(['role' => 'press', 'address' => 'press@creativetreesgroup.com']));
    }

    public function test_register_builds_an_smtp_mailer_from_account_and_password(): void
    {
        $name = MailAccounts::register([
            'role' => 'support',
            'address' => 'support@creativetreesgroup.com',
            'mailer' => 'smtp',
            'host' => 'mail.creativetreesgroup.com',
            'port' => 465,
            'encryption' => 'ssl',
            'username' => 'support@creativetreesgroup.com',
        ], 'the-password');

        $this->assertNotNull($name);
        $cfg = config('mail.mailers.'.$name);
        $this->assertSame('smtp', $cfg['transport']);
        $this->assertSame('smtps', $cfg['scheme']);                 // ssl → implicit TLS
        $this->assertSame('mail.creativetreesgroup.com', $cfg['host']);
        $this->assertSame(465, $cfg['port']);
        $this->assertSame('support@creativetreesgroup.com', $cfg['username']);
        $this->assertSame('the-password', $cfg['password']);
    }

    public function test_register_falls_back_username_to_address_and_tls_scheme(): void
    {
        $name = MailAccounts::register([
            'address' => 'no-reply@creativetreesgroup.com',
            'host' => 'mail.creativetreesgroup.com',
            'port' => 587,
            'encryption' => 'tls',
        ], 'pw');

        $cfg = config('mail.mailers.'.$name);
        $this->assertSame('smtp', $cfg['scheme']);                  // tls/STARTTLS
        $this->assertSame('no-reply@creativetreesgroup.com', $cfg['username']);  // falls back to address
    }

    public function test_register_returns_null_without_host_or_password(): void
    {
        $this->assertNull(MailAccounts::register(['mailer' => 'smtp', 'host' => 'mail.x.com'], null));
        $this->assertNull(MailAccounts::register(['mailer' => 'smtp', 'address' => 'x@x.com'], 'pw'));
    }

    public function test_mailer_uses_the_cms_account_when_fully_configured(): void
    {
        $setting = $this->settings();
        $setting->emails = [[
            'role' => 'no_reply',
            'address' => 'no-reply@creativetreesgroup.com',
            'mailer' => 'smtp',
            'host' => 'mail.creativetreesgroup.com',
            'port' => 465,
            'encryption' => 'ssl',
            'username' => 'no-reply@creativetreesgroup.com',
        ]];
        $setting->email_secrets = ['no-reply@creativetreesgroup.com' => 'pw'];
        $setting->save();

        $name = MailAccounts::mailer('no_reply');
        $this->assertNotNull($name);
        $this->assertSame('pw', config('mail.mailers.'.$name.'.password'));

        // Unknown role → null (caller falls back to the default mailer).
        $this->assertNull(MailAccounts::mailer('nope'));
    }

    public function test_edit_page_moves_typed_password_into_encrypted_secrets_and_strips_plaintext(): void
    {
        $setting = $this->settings();

        $page = new EditSiteSetting;
        (new \ReflectionProperty($page, 'record'))->setValue($page, $setting);
        $method = new \ReflectionMethod($page, 'mutateFormDataBeforeSave');

        $out = $method->invoke($page, [
            'page_content' => [],
            'emails' => [
                ['role' => 'support', 'address' => 'Support@creativetreesgroup.com', 'password' => 'typed-pw'],
                ['role' => 'sales', 'address' => 'sales@creativetreesgroup.com'],
            ],
        ]);

        // Plaintext password never lands in the non-secret `emails` column.
        $this->assertArrayNotHasKey('password', $out['emails'][0]);
        $this->assertArrayNotHasKey('password', $out['emails'][1]);

        // Secret captured into email_secrets, keyed by lowercased address.
        $this->assertSame('typed-pw', $out['email_secrets']['support@creativetreesgroup.com'] ?? null);
        // Account without a typed password has no orphan secret.
        $this->assertArrayNotHasKey('sales@creativetreesgroup.com', $out['email_secrets']);
    }
}
