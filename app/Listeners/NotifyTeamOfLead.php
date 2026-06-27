<?php

namespace App\Listeners;

use App\Events\LeadReceived;
use App\Mail\NewLeadMail;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class NotifyTeamOfLead
{
    public function handle(LeadReceived $event): void
    {
        $lead = $event->lead;

        // 1) Email the studio inbox (configured mailer; logs in dev, SMTP on cPanel).
        try {
            Mail::to(config('mail.from.address'))->send(new NewLeadMail($lead));
        } catch (\Throwable $e) {
            report($e);
        }

        // 2) Filament bell notification for every admin.
        //    notifyNow() writes synchronously, so the bell updates with NO queue worker
        //    required (cPanel-safe). With Reverb enabled it also pushes in real time.
        try {
            $notification = Notification::make()
                ->title('New project brief')
                ->icon('heroicon-o-inbox-arrow-down')
                ->body(trim(($lead->name ?? '').' · '.($lead->company ?: 'Independent')).' — '.($lead->service_interest ?: 'General'))
                ->toDatabase();

            User::query()->each(fn (User $admin) => $admin->notifyNow($notification));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
