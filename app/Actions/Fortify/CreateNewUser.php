<?php

namespace App\Actions\Fortify;

use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Models\Settings;
use App\Models\Agent;
use App\Models\CryptoAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        $request = request();

        Log::info('Registration flow started.', [
            'email' => $input['email'] ?? null,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            $settings = Settings::find(1);
        } catch (\Throwable $exception) {
            $settings = null;

            Log::error('Failed to load settings during user registration.', [
                'error' => $exception->getMessage(),
            ]);
        }

        $captchaEnabled = $settings && $settings->captcha === "true";

        if ($captchaEnabled) {
            Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'unique:users,username'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules(),
                'g-recaptcha-response' => 'required|captcha',
                'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            ])->validate();
        } else {
            Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'unique:users,username'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules(),
                'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            ])->validate();
        }

        Log::info('Registration validation passed.', [
            'email' => $input['email'] ?? null,
            'captcha_enabled' => $captchaEnabled,
        ]);


        if (session('ref_by')) {
            $ref_by = session('ref_by');
            $user = User::where('username', $ref_by)->first();
            $ref_by_id = $user ? $user->id : null;
        } else {
            if (!empty($input['ref_by'])) {
                $sponsor = User::where('username', $input['ref_by'])->first();
                $ref_by_id = $sponsor ? $sponsor->id : null;
            } else {
                $ref_by_id = NULL;
            }
        }

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'lastname' => $input['lastname'],
            'middlename'=>$input['middlename'],
            'phone' => $input['phone'],
            'username' => $input['username'],
            'country' => $input['country'],
            'accounttype' => $input['accounttype'],
            'ref_by' => $ref_by_id,
            'password' => Hash::make($input['password']),
        ]);

        $user->forceFill([
            'pin'=> $input['pin'],
            'status' => 'active',
            'usernumber'=> $this->RandomStringGenerator(11),
            'code1'  => $this->RandomStringGenerator(7),
            'code2'=>$this->RandomStringGenerator(7),
            'code3' => $this->RandomStringGenerator(7),
        ])->save();

        Log::info('Registration user record created.', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        $cryptoaccnt = new CryptoAccount();
        $cryptoaccnt->user_id = $user->id;
        $cryptoaccnt->save();
        Log::info('Registration crypto account created.', [
            'user_id' => $user->id,
            'crypto_account_id' => $cryptoaccnt->id,
        ]);

        $request->session()->forget('ref_by');

        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
            Log::info('Registration welcome email dispatched.', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Failed to send welcome email during registration.', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $exception->getMessage(),
            ]);
        }

        Log::info('Registration flow completed.', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return $user;
    }


    function RandomStringGenerator($n)
    {
        $generated_string = "";
        $domain = "12345678900123456789023456789034567890456789056789067890890";
        $len = strlen($domain);
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, $len - 1);
            $generated_string = $generated_string . $domain[$index];
        }
        // Return the random generated string 
        return $generated_string;
    }
}