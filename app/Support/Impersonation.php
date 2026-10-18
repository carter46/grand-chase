<?php

namespace App\Support;

use App\Http\Controllers\User\LoginOtpController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Lets a signed-in admin view the customer dashboard as a user without touching the admin
 * session: the admin guard stays signed in and only the web guard is swapped to the user.
 */
class Impersonation
{
    public const ADMIN_KEY = 'impersonator_admin_id';
    public const USER_KEY = 'impersonated_user_id';

    public static function active(): bool
    {
        $session = session();

        return $session->has(self::ADMIN_KEY)
            && (int) $session->get(self::USER_KEY) === (int) Auth::guard('web')->id();
    }

    public static function start(User $user): void
    {
        $admin = Auth::guard('admin')->user();

        Auth::guard('web')->login($user, false);

        session()->forget(LoginOtpController::PENDING_KEY);
        session()->put(self::ADMIN_KEY, $admin ? $admin->id : null);
        session()->put(self::USER_KEY, $user->id);

        Log::info('Admin impersonation started', [
            'admin_id' => $admin ? $admin->id : null,
            'admin_email' => $admin ? $admin->email : null,
            'user_id' => $user->id,
            'user_email' => $user->email,
        ]);
    }

    /**
     * Signs the web guard out and drops the impersonation markers. Returns the id of the user
     * that was being viewed, or null when no impersonation was in progress.
     */
    public static function stop(Request $request): ?int
    {
        if (!$request->session()->has(self::ADMIN_KEY)) {
            return null;
        }

        $userId = (int) $request->session()->get(self::USER_KEY) ?: null;

        // logoutCurrentDevice() leaves the customer's remember token alone, so the admin
        // leaving does not sign the real customer out of their own devices.
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logoutCurrentDevice();
        }

        $request->session()->forget([self::ADMIN_KEY, self::USER_KEY, LoginOtpController::PENDING_KEY]);
        $request->session()->regenerateToken();

        Log::info('Admin impersonation stopped', [
            'admin_id' => Auth::guard('admin')->id(),
            'user_id' => $userId,
        ]);

        return $userId;
    }
}
