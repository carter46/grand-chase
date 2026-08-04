<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Settings;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        //
        $settings = null;

        try {
            $settings = Settings::find(1);
        } catch (\Throwable $exception) {
            \Log::error('Failed to load settings in UserObserver created handler.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);
        }

        if ($settings && $settings->enable_verification == 'false') {
            $user->email_verified_at = \Carbon\Carbon::now();
            $user->save();
            \Log::info('User auto-verified after registration.', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        } else {
            if (session()->has('success')) {
                session()->forget('success');
            }
            session()->flash('success', 'Registration successful — check your email for the verification link.');
            \Log::info('User pending email verification after registration.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'settings_found' => (bool) $settings,
            ]);
        }

    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
