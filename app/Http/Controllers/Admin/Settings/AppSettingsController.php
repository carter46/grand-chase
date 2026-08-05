<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\SettingsCont;

class AppSettingsController extends Controller
{

    // Return view
    public function appsettingshow()
    {
        $live_timezones = timezone_identifiers_list();
        include 'currencies.php';
        return view('admin.Settings.AppSettings.show', [
            'title' => 'Website information settings',
            'timezones' => $live_timezones,
            'currencies' => $currencies,
            'timezone' => config('app.timezone'),
            'settings' => Settings::where('id', '=', '1')->first(),
        ]);
    }

    // for front end content management
    function RandomStringGenerator($n)
    {
        $generated_string = "";
        $domain = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $len = strlen($domain);
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, $len - 1);
            $generated_string = $generated_string . $domain[$index];
        }
        // Return the random generated string 
        return $generated_string;
    }

    // updatertransfercodes
    public function updatertransfercodes(Request $request){
        Settings::where('id', '1')
            ->update([
                'code1'=> $request->code1,
                'code2'=> $request->code2,
                'code3'=> $request->code3,
                'code1status' => $request->code1status,
                'code2status' => $request->code2status,
                'code3status' => $request->code3status,
                'code1message'=> $request->code1message,
                'code2message'=> $request->code2message,
                'code3message'=> $request->code3message,
                'otp'=>$request->otp,
            ]);

            return redirect()->back()->with('success', 'Settings Saved successfully');
    }
    // update wensite information
    public function updatewebinfo(Request $request)
    {
        $this->validate($request, [
            'logo' => 'nullable|mimes:jpg,jpeg,png|max:2048|image',
            'favicon' => 'nullable|mimes:jpg,jpeg,png,ico|max:1024',
        ]);

        $settings = Settings::where('id', '=', '1')->first();

        if ($request->hasfile('logo')) {
            delete_public_upload($settings->logo);
            $path = store_public_upload($request->file('logo'), 'branding', 'logo');
        } else {
            $path = $settings->logo;
        }

        if ($request->hasfile('favicon')) {
            delete_public_upload($settings->favicon);
            $pathfav = store_public_upload($request->file('favicon'), 'branding', 'favicon');
        } else {
            $pathfav = $settings->favicon;
        }
        
        $provider = strtolower(trim((string) $request->input('livechat_provider', 'none')));
        if (!in_array($provider, ['none', 'tidio', 'smartsupp', 'chatway'], true)) {
            $provider = 'none';
        }

        $payload = [
            'newupdate' => $request['update'],
            'site_name' => $request['site_name'],
            'description' => $request['description'],
            'keywords' => $request['keywords'],
            'timezone' => $request['timezone'],
            'site_title' => $request['site_title'],
            'install_type' => $request['install_type'],
            'logo' => $path,
            'merchant_key' => $request->merchant_key,
            'favicon' => $pathfav,
            'tawk_to' => strip_tags($request['tawk_to']),
            'site_address' => $request['site_address'],
            'welcome_message' => $request->welcome_message,
            'whatsapp'=> $request->whatsapp,
            'tido'=> $request->tido,
            'address'=> $request->address,
            'sms'=> $request->sms,
        ];

        // Live-chat columns (added by migration / add_livechat_settings.php)
        if (\Illuminate\Support\Facades\Schema::hasColumn('settings', 'livechat_provider')) {
            $payload['livechat_provider'] = $provider;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('settings', 'smartsupp_key')) {
            $payload['smartsupp_key'] = trim((string) $request->input('smartsupp_key', ''));
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('settings', 'chatway_widget_id')) {
            $payload['chatway_widget_id'] = trim((string) $request->input('chatway_widget_id', ''));
        }

        Settings::where('id', '1')->update($payload);

        $moreset = SettingsCont::find(1);
        $moreset->purchase_code = $request->purchase_code;
        $moreset->save();

        return redirect()->back()->with('success', 'Settings Saved successfully');
    }



    public function updatepreference(Request $request)
    {

        if ($request->return_capital == 'true') {
            $return_capital = true;
        } else {
            $return_capital = false;
        }

        Settings::where('id', 1)->update([
            'contact_email' => $request['contact_email'],
            'currency' => $request['currency'],
            's_currency' => $request['s_currency'],
            'weekend_trade' => $request['weekend_trade'],
            'location' => $request['location'],
            'trade_mode' => $request['trade_mode'],
            'enable_verification' => $request['enail_verify'],
            'google_translate' => $request['googlet'],
            'enable_kyc' => $request['enable_kyc'],
            'enable_kyc_registration' => $request['enable_kyc_registration'],
            'captcha' => $request['captcha'],
            'enable_with' => $request['withdraw'],
            'return_capital' => $return_capital,
            'enable_social_login' => $request['social'],
            'enable_annoc' => $request['annouc'],
            'redirect_url' => $request->redirect_url,
            'should_cancel_plan' => $request->should_cancel_plan,
        ]);
        return response()->json(['status' => 200, 'success' => 'Settings Saved successfully']);
    }

    // Update email preference
    public function updateemail(Request $request)
    {
        Settings::where('id', 1)
            ->update([
                'mail_server' => $request['server'],
                'emailfrom' => $request['emailfrom'],
                'emailfromname' => $request['emailfromname'],
                'smtp_host' => $request['smtp_host'],
                'smtp_port' => $request['smtp_port'],
                'smtp_encrypt' => $request['smtp_encrypt'],
                'smtp_user' => $request['smtp_user'],
                'smtp_password' => $request['smtp_password'],
                'google_id' => $request['google_id'],
                'google_secret' => $request['google_secret'],
                'google_redirect' => $request['google_redirect'],
                'capt_secret' => $request['capt_secret'],
                'capt_sitekey' => $request['capt_sitekey'],
            ]);
        return response()->json(['status' => 200, 'success' => 'Settings Saved successfully']);
        //return redirect()->back()->with('message', 'Action Sucessful');
    }

    /**
     * Send a one-off test email using form values (or saved settings).
     * Always returns JSON — never throws a 500 for SMTP auth failures.
     */
    public function sendTestEmail(Request $request)
    {
        $to = trim((string) $request->input('test_to', ''));
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'status' => 422,
                'message' => 'Enter a valid recipient email address.',
            ], 422);
        }

        $settings = Settings::where('id', 1)->first();
        $mailer = $request->input('server', $settings->mail_server ?: 'smtp');
        $fromAddress = $request->input('emailfrom', $settings->emailfrom);
        $fromName = $request->input('emailfromname', $settings->emailfromname);

        config([
            'mail.default' => $mailer,
            'mail.from.address' => $fromAddress,
            'mail.from.name' => $fromName,
            'mail.mailers.smtp.host' => $request->input('smtp_host', $settings->smtp_host),
            'mail.mailers.smtp.port' => $request->input('smtp_port', $settings->smtp_port),
            'mail.mailers.smtp.encryption' => $request->input('smtp_encrypt', $settings->smtp_encrypt),
            'mail.mailers.smtp.username' => $request->input('smtp_user', $settings->smtp_user),
            'mail.mailers.smtp.password' => $request->input('smtp_password', $settings->smtp_password),
        ]);

        // Force mailer to rebuild with new runtime config
        try {
            if (app()->bound('mail.manager')) {
                $manager = app('mail.manager');
                if (method_exists($manager, 'purge')) {
                    $manager->purge($mailer);
                }
            }
            app()->forgetInstance('swift.mailer');
            app()->forgetInstance('mailer');
        } catch (\Throwable $e) {
            // ignore — Mail::raw will still use config() values when possible
        }

        $site = $settings->site_name ?: config('app.name');
        $subject = 'SMTP Test Email from ' . $site;
        $body = "This is a test email from {$site}.\n\n"
            . "If you received this, your mail configuration is working.\n\n"
            . 'Sent at: ' . now()->toDateTimeString() . "\n"
            . 'Mailer: ' . $mailer . "\n"
            . 'SMTP host: ' . (string) config('mail.mailers.smtp.host') . "\n";

        try {
            \Illuminate\Support\Facades\Mail::mailer($mailer)->raw($body, function ($message) use ($to, $subject, $fromAddress, $fromName) {
                $message->to($to)->subject($subject);
                if ($fromAddress) {
                    $message->from($fromAddress, $fromName ?: $fromAddress);
                }
            });

            return response()->json([
                'status' => 200,
                'message' => 'Test email sent successfully to ' . $to . '. Check the inbox (and spam).',
            ]);
        } catch (\Throwable $e) {
            report($e);
            $hint = $e->getMessage();
            if (stripos($hint, '535') !== false || stripos($hint, 'authenticat') !== false) {
                $hint = 'SMTP authentication failed. Check SMTP username and mailbox password (Hostinger email password, not hPanel login), host, port, and encryption.';
            }

            return response()->json([
                'status' => 500,
                'message' => $hint,
            ]);
        }
    }
}