<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use App\Events\RoiReceived;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Log;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(Registered::class, function ($event) {
            try {
                $event->user->sendEmailVerificationNotification();
                Log::info('Registration verification email dispatched.', [
                    'user_id' => $event->user->id,
                    'email' => $event->user->email,
                ]);
            } catch (\Throwable $exception) {
                Log::error('Failed to send verification email.', [
                    'user_id' => $event->user->id,
                    'email' => $event->user->email,
                    'error' => $exception->getMessage(),
                ]);
            }
        });

        User::observe(UserObserver::class);
    }
}
