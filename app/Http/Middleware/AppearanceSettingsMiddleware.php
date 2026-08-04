<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AppearanceSettings;

class AppearanceSettingsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get appearance settings
        $appearanceSettings = AppearanceSettings::first();
        
        // Share settings with all views
        view()->share('appearanceSettings', $appearanceSettings);
        
        return $next($request);
    }
} 