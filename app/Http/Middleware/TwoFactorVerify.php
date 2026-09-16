<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class TwoFactorVerify
{
    public function handle($request, Closure $next)
    {
        if ($request->session()->get('hub_sso_login')) {
            return $next($request);
        }

        $logg = Auth::guard('admin')->user();
        if (!$logg) {
            return redirect()->route('validate_admin');
        }
        $user = Admin::where('email', $logg->email)->first();
        if (!$user) {
            return redirect()->route('validate_admin');
        }

        if ($user->enable_2fa == "enabled" && $user->token_2fa_expiry < \Carbon\Carbon::now() && ($user->pass_2fa == "false" || $user->pass_2fa == NULL)) {
            return redirect('/admin/2fa');
        }

        return $next($request);
    }
}