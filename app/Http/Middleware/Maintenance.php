<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Maintenance
{
    /**
     * CMS-driven maintenance mode. When Site Settings →
     * page_content.system.maintenance is on, guests on the public site get the
     * maintenance page (503). Authenticated users (admins) and /admin always pass
     * through, so the toggle can be flipped back off. DB-guarded via current().
     */
    public function handle(Request $request, Closure $next): Response
    {
        $on = (bool) data_get(SiteSetting::current()->page_content, 'system.maintenance');

        if ($on && ! $request->user() && ! $request->is('admin*')) {
            return response()->view('errors.maintenance', [], 503)
                ->header('Retry-After', '3600');
        }

        return $next($request);
    }
}
