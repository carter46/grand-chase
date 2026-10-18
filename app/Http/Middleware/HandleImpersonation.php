<?php

namespace App\Http\Middleware;

use App\Support\Impersonation;
use Closure;
use Illuminate\Http\Request;

/**
 * While an admin is viewing a customer's account, the customer "Logout" buttons end the
 * impersonation instead of destroying the whole session (which would sign the admin out too).
 */
class HandleImpersonation
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasSession() || !$request->session()->has(Impersonation::ADMIN_KEY)) {
            return $next($request);
        }

        if (!Impersonation::active()) {
            $request->session()->forget([Impersonation::ADMIN_KEY, Impersonation::USER_KEY]);
            return $next($request);
        }

        if (optional($request->route())->getName() === 'logout' && $request->isMethod('POST')) {
            return app(\App\Http\Controllers\Admin\ImpersonationController::class)->leave($request);
        }

        return $next($request);
    }
}
