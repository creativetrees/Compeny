<?php

namespace App\Listeners;

use App\Events\LeadReceived;
use App\Mail\NewLeadMail;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\MailAccounts;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NotifyTeamOfLead
{
    public function handle(LeadReceived $event): void
    {
        $lead = $event->lead;

        // 1) Email the studio inbox (configured mailer; logs in dev, SMTP on cPanel).
        //    Recipient is CMS-driven: Site Settings notification email → public
        //    contact email → the mailer's from address (last-resort fallback).
        $to = null;
        $mailError = null;

        try {
            $settings = SiteSetting::current();
            $to = data_get($settings->page_content, 'system.notify_email')
                ?: ($settings->contact_email ?: config('mail.from.address'));

            // Prefer the CMS "no-reply" account's SMTP (its password lives in .env, never
            // the DB); fall back to the default mailer when it isn't fully configured.
            // When used, align the visible From to that account so SMTP auth matches.
            $mailerName = MailAccounts::mailer('no_reply');
            $sender = $mailerName ? MailAccounts::byRole('no_reply') : null;
            $originalFrom = config('mail.from.address');

            try {
                if ($sender && filled($sender['address'] ?? null)) {
                    config(['mail.from.address' => $sender['address']]);
                }

                Mail::mailer($mailerName ?? config('mail.default'))
                    ->to($to)
                    ->send(new NewLeadMail($lead));
            } finally {
                // Restore global from so this account's address can't bleed into other mail.
                config(['mail.from.address' => $originalFrom]);
            }
        } catch (\Throwable $e) {
            $mailError = $e->getMessage();
            report(new \RuntimeException(
                "Lead #{$lead->id} notification email failed".($to ? " to {$to}" : '').': '.$e->getMessage(),
                0,
                $e
            ));
        }

        // Record the send outcome on the lead so a failure is visible and recoverable
        // in the admin (NULL notified_at + an error message), instead of the visitor
        // seeing "sent" while nobody is actually emailed. saveQuietly() skips model
        // events — the message is already sanitized.
        $lead->forceFill([
            'notified_at' => $mailError === null ? now() : null,
            'notification_error' => $mailError !== null ? Str::limit($mailError, 2000, '') : null,
        ])->saveQuietly();

        // 2) Filament bell notification for every admin.
        //    notifyNow() writes synchronously, so the bell updates with NO queue worker
        //    required (cPanel-safe). With Reverb enabled it also pushes in real time.
        try {
            $notification = Notification::make()
                ->title('New project brief')
                ->icon('heroicon-o-inbox-arrow-down')
                ->body(trim(($lead->name ?? '').' · '.($lead->company ?: 'Independent')).' — '.($lead->service_interest ?: 'General'))
                ->toDatabase();

            User::query()->each(function (User $admin) use ($notification): void {
                try {
                    $admin->notifyNow($notification);
                } catch (\Throwable $e) {
                    report(new \RuntimeException("Lead bell notification failed for admin #{$admin->id}: ".$e->getMessage(), 0, $e));
                }
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
