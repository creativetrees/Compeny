<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class MailAccountSeeder extends Seeder
{
    /**
     * A professional set of role-based mailboxes for the studio domain.
     * Non-secret transport config only — SMTP passwords are entered in the CMS
     * (stored encrypted) or via .env, never seeded. Idempotent: skips when
     * accounts already exist so it never clobbers what an admin configured.
     */
    public function run(): void
    {
        $setting = SiteSetting::query()->firstOrCreate(['id' => 1]);

        if (filled($setting->emails)) {
            return;
        }

        $domain = 'creativetreesgroup.com';
        $host = 'mail.'.$domain;

        // [role, local-part]
        $accounts = [
            ['general', 'hello'],
            ['support', 'support'],
            ['sales', 'sales'],
            ['no_reply', 'no-reply'],
            ['careers', 'careers'],
            ['billing', 'billing'],
        ];

        $setting->emails = array_map(fn (array $a): array => [
            'role' => $a[0],
            'address' => $a[1].'@'.$domain,
            'mailer' => 'smtp',
            'host' => $host,
            'port' => 465,
            'encryption' => 'ssl',
            'username' => $a[1].'@'.$domain,
        ], $accounts);

        if (blank($setting->contact_email)) {
            $setting->contact_email = 'hello@'.$domain;
        }

        $setting->save();
    }
}
