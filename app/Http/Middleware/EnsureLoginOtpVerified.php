<?php

namespace App\Http\Middleware;

use App\Http\Controllers\User\LoginOtpController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Keeps a customer who has signed in with their password, but not yet entered the emailed
 * login code, out of every customer area (dashboard, Jetstream profile, Livewire) until they do.
 */
class EnsureLoginOtpVerified
{
    private const ALLOWED_ROUTES = ['pin', 'pinstatus', 'pin.resend', 'logout', 'session.check'];

    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasSession() || !$request->session()->get(LoginOtpController::PENDING_KEY)) {
            return $next($request);
        }

        if (!Auth::guard('web')->check() || $request->session()->get('hub_sso_login')) {
            $request->session()->forget(LoginOtpController::PENDING_KEY);
            return $next($request);
        }

        $routeName = optional($request->route())->getName();
        $customerArea = $request->is('dashboard') || $request->is('dashboard/*')
            || $request->is('user/*') || $request->is('livewire/*');

        if (!$customerArea || in_array($routeName, self::ALLOWED_ROUTES, true)) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('livewire/*')) {
            return response()->json(['message' => 'Please enter the login code sent to your email.'], 403);
        }

        if ($request->isMethod('GET')) {
            $request->session()->put('url.intended', $request->fullUrl());
        }

        return redirect()->route('pin');
    }
}
