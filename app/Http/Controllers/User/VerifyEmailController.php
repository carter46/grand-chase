<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Completes email verification from the signed link in the registration email.
 * Does not require the user to already be logged in — the signature + hash are the proof.
 */
class VerifyEmailController extends Controller
{
    public function __invoke(Request $request, $id, $hash)
    {
        $user = User::find($id);

        if (!$user || !hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            return redirect()->route('login')
                ->with('status', 'This verification link is invalid or has expired. Please log in and request a new one.');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        if (Auth::guard('web')->check() && (int) Auth::guard('web')->id() === (int) $user->id) {
            return redirect()->route('dashboard')
                ->with('success', 'Your email address has been verified.');
        }

        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login')
            ->with('status', 'Your email address has been verified. Please log in to continue.');
    }
}
