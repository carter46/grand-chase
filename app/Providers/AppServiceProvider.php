<?php

namespace App\Providers;

use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use Illuminate\Support\Facades\View;
use App\Models\Settings;
use App\Models\SettingsCont;
use App\Models\TermsPrivacy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage as FacadesStorage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Baseline migration already creates personal_access_tokens.
        // Prevent Sanctum from registering a duplicate vendor migration.
        \Laravel\Sanctum\Sanctum::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        FacadesStorage::extend('sftp', function ($app, $config) {
            return new Filesystem(new SftpAdapter($config));
        });

        Paginator::useBootstrap();

        View::composer('*', function ($view) {
            static $sharedData = null;

            if ($sharedData === null) {
                $settings = Settings::where('id', '1')->first();
                $terms = TermsPrivacy::find(1);
                $moreset = SettingsCont::find(1);

                // Guard: unseeded local DBs should not fatal every view.
                if (!$settings) {
                    $settings = new Settings([
                        'site_name' => 'Unconfigured',
                        'site_title' => 'Unconfigured',
                        'currency' => '$',
                        's_currency' => 'USD',
                        'modules' => [],
                    ]);
                }

                if ($settings && Auth::guard('web')->check()) {
                    $user = Auth::guard('web')->user();
                    // Only override when columns exist (corrective migration may not have run yet).
                    if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'currency') && !empty($user->currency)) {
                        $settings->currency = $user->currency;
                    }
                    if (\Illuminate\Support\Facades\Schema::hasColumn('users', 's_currency') && !empty($user->s_currency)) {
                        $settings->s_currency = $user->s_currency;
                    }
                }

                $modules = $settings->modules;
                if (!is_array($modules)) {
                    $modules = [];
                }
                // Planned SaaS modules stay disabled until FEATURE_SAAS_REMOTE_MODULES=true
                if (!config('features.saas_remote_modules')) {
                    $modules['membership'] = false;
                    $modules['signal'] = false;
                    $modules['subscription'] = false;
                }

                $sharedData = [
                    'settings' => $settings,
                    'terms' => $terms,
                    'moresettings' => $moreset,
                    'mod' => $modules,
                ];
            }

            $view->with($sharedData);
        });
    }
}