<?php

namespace App\Http\Middleware;

use App\Services\AdminDatabaseAutoMigrate;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class RunAdminDatabaseMigrations
{
    /**
     * Apply pending database migrations when an authenticated admin loads any admin page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('admin')->check()) {
            try {
                app(AdminDatabaseAutoMigrate::class)->run(Auth::guard('admin')->id());
            } catch (Throwable $e) {
                session([
                    'auto_migration_errors' => [
                        'Auto-migration runner failed: ' . $e->getMessage(),
                    ],
                ]);
                report($e);
            }
        }

        return $next($request);
    }
}
