<?php

namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Settings;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('seventh-tradehub:poll')->everyTenMinutes()->withoutOverlapping(9);
        $schedule->call(function () {
            if (!\Illuminate\Support\Facades\Schema::hasTable('seventh_tradehub_nonces')) {
                return;
            }
            \Illuminate\Support\Facades\DB::table('seventh_tradehub_nonces')
                ->where('seen_at', '<', now()->subDays(7))
                ->delete();
        })->daily()->name('seventh-tradehub-nonce-prune')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
