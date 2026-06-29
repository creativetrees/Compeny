<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force an admin to fill their KTP (NIK) before using the panel. On the first
 * full-page navigation where the NIK is empty, redirect to the profile page so
 * they complete it. Livewire/AJAX requests (incl. the profile form save) pass
 * through untouched, so they can actually submit the NIK.
 */
class RequireNik
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User
            && blank($user->nik)
            && $request->isMethod('GET')
            && ! $request->ajax()
            && ! $request->wantsJson()
        ) {
            $profileUrl = Filament::getCurrentPanel()?->getProfileUrl();

            if ($profileUrl && rtrim($request->url(), '/') !== rtrim($profileUrl, '/')) {
                Notification::make()
                    ->title('Lengkapi data KTP (NIK) Anda')
                    ->body('Isi NIK (No. KTP) terlebih dahulu untuk dapat menggunakan panel.')
                    ->warning()
                    ->send();

                return redirect($profileUrl);
            }
        }

        return $next($request);
    }
}
