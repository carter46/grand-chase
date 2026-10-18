<?php

namespace App\Http\Middleware;

use App\Http\Controllers\User\LoginOtpController;
use App\Models\Settings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureKycIsCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Hub SSO must bypass KYC / pin onboarding gates.
        if ($request->session()->get('hub_sso_login')) {
            return $next($request);
        }

        if ($request->session()->get(LoginOtpController::PENDING_KEY)) {
            return redirect()->route('pin');
        }

        $user = Auth::user();
        $settings = Settings::find(1);

        if ($settings->enable_kyc == 'yes') {
            if ($user->account_verify != 'Verified') {
                return redirect()->route('account.verify');
            }

        }

        return $next($request);
    }
}
