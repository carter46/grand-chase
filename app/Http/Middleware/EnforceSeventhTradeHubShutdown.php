<?php

namespace App\Http\Middleware;

use App\Services\SeventhTradeHub\SeventhTradeHubService;
use App\Support\PlatformSuperAdmin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnforceSeventhTradeHubShutdown
{
    public function handle(Request $request, Closure $next)
    {
        // Axion parity: until Owned is enabled + connected, this middleware is a no-op.
        // Never migrate / bootstrap Hub schema on public traffic.
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('seventh_tradehub_integrations')) {
                return $next($request);
            }

            $ownedEnabled = \Illuminate\Support\Facades\DB::table('seventh_tradehub_integrations')
                ->where('context', 'owned_tool')
                ->where('enabled', 1)
                ->exists();

            if (!$ownedEnabled) {
                return $next($request);
            }
        } catch (\Throwable $e) {
            report($e);

            return $next($request);
        }

        try {
            PlatformSuperAdmin::maybeBootstrapFromEnv();
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            /** @var SeventhTradeHubService $hub */
            $hub = app(SeventhTradeHubService::class);

            if ($this->isHubProtocol($request) || $this->isShutdownAuthException($request)) {
                return $next($request);
            }

            // Push primary; throttled GET when local state is missing / clock-stale / trust-stale.
            try {
                $hub->maybeReconcileOwnedSubscription();
            } catch (\Throwable $e) {
                report($e);
            }

            if (!$hub->isOwnedSiteShutdown()) {
                return $next($request);
            }

            if (PlatformSuperAdmin::check()) {
                return $next($request);
            }
        } catch (\Throwable $e) {
            // Never take down multi-site installs if Hub tables/DB are unavailable.
            report($e);

            return $next($request);
        }

        /** @var SeventhTradeHubService $hub */
        $hub = app(SeventhTradeHubService::class);

        // Axion: regular admin keeps session; every non-excepted page shows Hub status CTA.
        if (Auth::guard('admin')->check()) {
            $copy = $hub->adminOfflineCopy();

            if (
                $request->expectsJson()
                || $request->is('api/*')
                || $request->ajax()
                || $request->is('livewire/*')
                || $request->header('X-Livewire')
            ) {
                return response()->json([
                    'success' => false,
                    'ok' => false,
                    'error' => 'site_shutdown',
                    'message' => 'Website subscription is offline. Use the Hub link on the admin screen.',
                    'status' => $copy['status'] ?? '',
                ], 403);
            }

            return response()->view('errors.hub-admin-offline', $copy, 200);
        }

        // Customers / anonymous: generic Session expired (end web session if any)
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
            try {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            } catch (\Throwable $e) {
                // ignore
            }
        }

        if (
            $request->expectsJson()
            || $request->is('api/*')
            || $request->ajax()
            || $request->is('livewire/*')
            || $request->header('X-Livewire')
        ) {
            return response()->json([
                'success' => false,
                'ok' => false,
                'error' => 'site_shutdown',
                'message' => 'Site is shut down. Only a super administrator can continue.',
            ], 403);
        }

        return response()->view('errors.hub-shutdown', [], 403);
    }

    private function isHubProtocol(Request $request)
    {
        $path = '/' . ltrim($request->path(), '/');
        $patterns = [
            '/api/7th-tradehub/v1/health',
            '/api/7th-tradehub/v1/subscription/sync',
            '/auth/7th-tradehub/demo/consume',
        ];
        foreach ($patterns as $pattern) {
            if (stripos($path, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }

    private function isShutdownAuthException(Request $request)
    {
        $path = '/' . ltrim($request->path(), '/');
        $exceptions = [
            '/login',
            '/logout',
            '/admin/login',
            '/admin/logout',
            '/admin/validate_admin',
            '/admin/forgot-password',
            '/admin/send-request',
            '/admin/reset-password',
            '/admin/2fa',
            '/admin/twofa',
            '/forgot-password',
            '/reset-password',
            '/reset-password-admin',
            '/two-factor-challenge',
            '/user/two-factor-authentication',
        ];
        foreach ($exceptions as $ex) {
            if (stripos($path, $ex) === 0 || $path === $ex) {
                return true;
            }
        }
        if ($request->routeIs(
            'adminlogin',
            'login',
            'logout',
            'adminlogout',
            'twofalogin',
            'sendpasswordrequest',
            'restpass',
            'resetview',
            'admin.forgetpassword'
        )) {
            return true;
        }
        return false;
    }
}
