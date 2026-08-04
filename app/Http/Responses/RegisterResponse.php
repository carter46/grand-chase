<?php

namespace App\Http\Responses;

use App\Models\Settings;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $verificationEnabled = true;

        try {
            $settings = Settings::find(1);
            if ($settings && $settings->enable_verification === 'false') {
                $verificationEnabled = false;
            }
        } catch (\Throwable $exception) {
            // If we can't read the settings row, fall back to requiring verification
        }

        if ($verificationEnabled) {
            return redirect()->route('verification.notice');
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}

