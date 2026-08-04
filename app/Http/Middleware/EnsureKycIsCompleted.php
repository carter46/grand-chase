<?php

namespace App\Http\Middleware;

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
        $user = Auth::user();
        $settings = Settings::find(1);

        if ($settings->enable_kyc == 'yes') {
            if ($user->account_verify != 'Verified') {
                return redirect()->route('account.verify');
            }

        }
        
        if ($user->pinstatus != 0) {
                return redirect()->route('pin');
            }

        return $next($request);
    }
}
