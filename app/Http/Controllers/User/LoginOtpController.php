<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\NewNotification;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Emailed one-time code required after password login (replaces the old login PIN screen).
 * The pending state lives in the session, so it only affects the device that just logged in.
 */
class LoginOtpController extends Controller
{
    public const PENDING_KEY = 'login_otp_pending';
    private const CODE_KEY = 'login_otp';
    private const TTL_SECONDS = 600;
    private const MAX_ATTEMPTS = 5;
    private const RESEND_COOLDOWN_SECONDS = 60;

    /** Mark the current session as needing a login code, if the user has it turned on. */
    public static function requireFor(Request $request, User $user)
    {
        $request->session()->forget([self::PENDING_KEY, self::CODE_KEY]);
        if ((int) ($user->login_otp_required ?? 1) === 1) {
            $request->session()->put(self::PENDING_KEY, true);
        }
    }

    public function show(Request $request)
    {
        if (!$request->session()->get(self::PENDING_KEY)) {
            return redirect()->route('dashboard');
        }

        $user = Auth::user();
        if ((int) ($user->login_otp_required ?? 1) !== 1) {
            $request->session()->forget([self::PENDING_KEY, self::CODE_KEY]);
            return $this->continueToApp($request);
        }

        $sendFailed = false;
        $sent = $request->session()->get(self::CODE_KEY);
        if (!$sent || $sent['expires'] < time()) {
            $sendFailed = !$this->sendCode($request, $user);
        }

        return view('user.pin', [
            'title' => 'Verify Login',
            'maskedEmail' => $this->maskEmail($user->email),
            'sendFailed' => $sendFailed,
        ]);
    }

    public function verify(Request $request)
    {
        if (!$request->session()->get(self::PENDING_KEY)) {
            return redirect()->route('dashboard');
        }

        $sent = $request->session()->get(self::CODE_KEY);
        if (!$sent || $sent['expires'] < time()) {
            return redirect()->route('pin')->with('message', 'Your code has expired. We have sent you a new one.');
        }

        $input = preg_replace('/\D/', '', (string) $request->input('otp'));
        if ($input === '' || !Hash::check($input, $sent['hash'])) {
            $sent['attempts'] = ($sent['attempts'] ?? 0) + 1;
            if ($sent['attempts'] >= self::MAX_ATTEMPTS) {
                $request->session()->forget(self::CODE_KEY);
                return redirect()->route('pin')->with('message', 'Too many incorrect attempts. We have sent you a new code.');
            }
            $request->session()->put(self::CODE_KEY, $sent);
            return redirect()->route('pin')->with('message', 'Incorrect code. Please check your email and try again.');
        }

        $request->session()->forget([self::PENDING_KEY, self::CODE_KEY]);

        return $this->continueToApp($request);
    }

    /** Go to the customer page the user was heading for, never an admin page left in url.intended. */
    private function continueToApp(Request $request)
    {
        $intended = (string) $request->session()->pull('url.intended', '');
        $path = '/' . ltrim((string) parse_url($intended, PHP_URL_PATH), '/');
        $sameHost = parse_url($intended, PHP_URL_HOST) === null || parse_url($intended, PHP_URL_HOST) === $request->getHost();

        if ($intended !== '' && $sameHost && !preg_match('#^/(admin|dashboard/pin)(/|$)#', $path)) {
            return redirect()->to($intended);
        }

        return redirect()->route('dashboard');
    }

    public function resend(Request $request)
    {
        if (!$request->session()->get(self::PENDING_KEY)) {
            return redirect()->route('dashboard');
        }

        $sent = $request->session()->get(self::CODE_KEY);
        if ($sent && isset($sent['sent_at']) && time() - $sent['sent_at'] < self::RESEND_COOLDOWN_SECONDS) {
            $wait = self::RESEND_COOLDOWN_SECONDS - (time() - $sent['sent_at']);
            return redirect()->route('pin')->with('message', "Please wait {$wait} seconds before requesting a new code.");
        }

        $delivered = $this->sendCode($request, Auth::user());

        return redirect()->route('pin')->with(
            $delivered ? 'success' : 'message',
            $delivered ? 'A new code has been sent to your email.' : 'We could not send the code right now. Please try again or contact support.'
        );
    }

    private function sendCode(Request $request, User $user)
    {
        $code = (string) random_int(100000, 999999);
        $request->session()->put(self::CODE_KEY, [
            'hash' => Hash::make($code),
            'expires' => time() + self::TTL_SECONDS,
            'sent_at' => time(),
            'attempts' => 0,
        ]);

        $settings = Settings::where('id', '1')->first();
        $siteName = $settings->site_name ?? config('app.name');
        $minutes = (int) (self::TTL_SECONDS / 60);
        $message = "A sign-in to your $siteName account was just requested.\n\n"
            . "Use the code below to finish logging in:\n\n"
            . "# $code\n\n"
            . "This code expires in $minutes minutes. If you did not try to log in, change your password immediately and contact support.";

        try {
            Mail::to($user->email)->send(new NewNotification($message, 'Your login verification code', $user->name));
            return true;
        } catch (\Throwable $exception) {
            Log::error('Failed to send login OTP email.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);
            return false;
        }
    }

    private function maskEmail($email)
    {
        $parts = explode('@', (string) $email, 2);
        if (count($parts) !== 2) {
            return (string) $email;
        }
        $name = $parts[0];
        $visible = mb_substr($name, 0, min(2, mb_strlen($name)));

        return $visible . str_repeat('*', max(1, mb_strlen($name) - mb_strlen($visible))) . '@' . $parts[1];
    }
}
