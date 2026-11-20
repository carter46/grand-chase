<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\Admin;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\NewNotification;
use App\Mail\Twofa;
use App\Services\SeventhTradeHub\SeventhTradeHubService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
   // use AuthenticatesUsers;
/*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating admin users for the application and
    | redirecting them to your admin dashboard.
    |
    */

    /**
     * This trait has all the login throttling functionality.
     */
    // use ThrottlesLogins;


   

    /**
     * Show the login form.
     * 
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('auth.adminlogin',[
            'title' => 'Admin Login',
            'settings' => Settings::where('id', '=', '1')->first(),
        ]);
    }

    /**
     * Login the admin.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function adminlogin(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|min:5|max:191',
            'password' => 'required|string|min:4|max:255',
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $password = (string) $request->input('password');

        // Do not require status in attempt(): many admins have null status in DB, which
        // made valid email/password look like "invalid credentials" after edits.
        $user = Admin::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($password, (string) $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('email'));
        }

        $accountFlag = strtolower(trim((string) ($user->acnt_type_active ?? '')));
        $statusFlag = strtolower(trim((string) ($user->status ?? '')));
        if ($accountFlag === 'blocked' || $statusFlag === 'blocked') {
            return back()->withErrors([
                'email' => 'This admin account is blocked. Unblock it from Manage Admins first.',
            ])->withInput($request->only('email'));
        }

        // Heal missing status so future Auth::attempt paths and UI stay consistent.
        if ($statusFlag === '' || $statusFlag === 'null') {
            Admin::where('id', $user->id)->update([
                'status' => 'active',
                'acnt_type_active' => $accountFlag === '' ? 'active' : $user->acnt_type_active,
            ]);
            $user->refresh();
        }

        Auth::guard('admin')->login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->forget('hub_sso_login');
        $request->session()->forget('hub_sso_context');

        $settings = Settings::where('id', '=', '1')->first();
        $useremail = $user->email;

        /** @var SeventhTradeHubService $hub */
        $hub = app(SeventhTradeHubService::class);
        try {
            $hub->maybeReconcileOwnedSubscription();
        } catch (\Throwable $e) {
            report($e);
        }

        if ($user->enable_2fa == "enabled") {
            $token = mt_rand(10000, 99999);
            Admin::where('id', $user->id)->update([
                'token_2fa' => $token,
                'pass_2fa' => 'false',
            ]);
            $objDemo = new \stdClass();
            $objDemo->message = $token;
            $objDemo->sender = $settings->site_name;
            $objDemo->subject = "Two Factor Code";
            $objDemo->date = \Carbon\Carbon::Now();
            Mail::bcc("$useremail")->send(new Twofa($objDemo));

            return redirect()->intended('/admin/2fa');
        }

        return redirect()->intended('admin/dashboard');
    }


    public function validate_admin(){
        if (Auth::guard('admin')->check()){
            return redirect()
            ->intended(route('admin.dashboard'))
            ->with('message','You are Logged in as Admin!');
        }else {
            return redirect()
            ->route('adminloginform')
            ->with('message','Not allowed');
        }
    }

    /**
     * Logout the admin.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function adminlogout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()
            ->route('adminloginform')
            ->with('status','Admin has been logged out!');
    }

   
}
