<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Config;

/**
 * Resolves the role-based mail accounts managed in Site Settings → Email addresses.
 *
 * The non-secret transport config (mailer, host, port, encryption, username) is
 * stored in the database (site_settings.emails). The PASSWORD is never stored
 * there — it is read from config/mail_accounts.php (sourced from .env), keyed by
 * role. mailer() assembles a runtime Laravel mailer from the two halves so an
 * account can actually send, without the secret ever touching the database.
 */
class MailAccounts
{
    /** All configured accounts (each: role, address, mailer, host, port, encryption, username). */
    public static function all(): array
    {
        return SiteSetting::current()->emails ?? [];
    }

    /** The first account matching a role, or null. */
    public static function byRole(string $role): ?array
    {
        foreach (static::all() as $account) {
            if (is_array($account) && ($account['role'] ?? null) === $role) {
                return $account;
            }
        }

        return null;
    }

    /** The .env variable name that holds a role's SMTP password (fallback / docs). */
    public static function passwordEnvKey(string $role): string
    {
        return 'MAIL_'.strtoupper($role).'_PASSWORD';
    }

    /**
     * The account's SMTP password: the CMS-managed secret first (stored encrypted,
     * keyed by address), then the .env fallback keyed by role. Returns null when
     * neither is set. The decrypted value is used only to build the transport — it
     * is never returned to the browser or logged.
     */
    public static function password(array $account): ?string
    {
        $address = strtolower(trim($account['address'] ?? ''));

        if ($address !== '') {
            $secrets = SiteSetting::current()->email_secrets ?? [];

            if (filled($secrets[$address] ?? null)) {
                return $secrets[$address];
            }
        }

        $role = $account['role'] ?? '';

        return $role !== '' ? config('mail_accounts.passwords.'.$role) : null;
    }

    /**
     * Register a runtime mailer for the given role and return its name — or null
     * when the account is missing, has no SMTP host, or has no server-side
     * password (the caller then falls back to the default mailer). The password
     * comes from config (sourced from .env), never from the database.
     */
    public static function mailer(string $role): ?string
    {
        $account = static::byRole($role);

        if (! $account) {
            return null;
        }

        $transport = $account['mailer'] ?? 'smtp';

        // Non-SMTP transports map to the built-in mailers (sendmail/log) — no secret needed.
        if ($transport !== 'smtp') {
            return array_key_exists($transport, (array) config('mail.mailers')) ? $transport : null;
        }

        if (blank($account['host'] ?? null)) {
            return null;
        }

        $password = static::password($account);

        if (blank($password)) {
            return null;
        }

        $encryption = $account['encryption'] ?? 'tls';
        $name = 'cms_'.$role;

        Config::set('mail.mailers.'.$name, [
            'transport' => 'smtp',
            'scheme' => $encryption === 'ssl' ? 'smtps' : 'smtp',
            'host' => $account['host'],
            'port' => (int) ($account['port'] ?? ($encryption === 'ssl' ? 465 : 587)),
            'username' => ($account['username'] ?? null) ?: ($account['address'] ?? null),
            'password' => $password,
            'timeout' => null,
        ]);

        return $name;
    }
}
