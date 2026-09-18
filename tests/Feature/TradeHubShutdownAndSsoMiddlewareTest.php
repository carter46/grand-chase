<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureEmailIsVerifiedOrHubSso;
use App\Http\Middleware\EnforceSeventhTradeHubShutdown;
use Illuminate\Http\Request;
use Tests\TestCase;

class TradeHubShutdownAndSsoMiddlewareTest extends TestCase
{
    public function test_verified_middleware_allows_hub_sso_session()
    {
        $request = Request::create('/dashboard', 'GET');
        $request->setLaravelSession(app('session')->driver());
        $request->session()->put('hub_sso_login', 1);

        $middleware = new EnsureEmailIsVerifiedOrHubSso();
        $response = $middleware->handle($request, function () {
            return response('ok');
        });

        $this->assertSame('ok', $response->getContent());
    }

    public function test_shutdown_exceptions_include_logout_and_reset_post()
    {
        $middleware = new EnforceSeventhTradeHubShutdown();
        $ref = new \ReflectionClass($middleware);
        $method = $ref->getMethod('isShutdownAuthException');
        $method->setAccessible(true);

        foreach ([
            '/admin/logout',
            '/logout',
            '/reset-password-admin',
            '/admin/send-request',
            '/admin/login',
        ] as $path) {
            $request = Request::create($path, 'POST');
            $this->assertTrue(
                $method->invoke($middleware, $request),
                "Expected shutdown exception for {$path}"
            );
        }
    }

    public function test_hub_protocol_paths_are_excepted()
    {
        $middleware = new EnforceSeventhTradeHubShutdown();
        $ref = new \ReflectionClass($middleware);
        $method = $ref->getMethod('isHubProtocol');
        $method->setAccessible(true);

        $request = Request::create('/api/7th-tradehub/v1/health', 'POST');
        $this->assertTrue($method->invoke($middleware, $request));
    }

    public function test_admin_offline_blade_renders_cta()
    {
        $html = view('errors.hub-admin-offline', [
            'status' => 'expired',
            'message' => 'Your website subscription has expired. Sign in to your 7th Trade Hub account to renew this website subscription.',
            'cta_href' => 'https://7th-tradehub.online/login',
            'cta_label' => 'Sign in to 7th Trade Hub',
        ])->render();

        $this->assertStringContainsString('Admin · subscription expired', $html);
        $this->assertStringContainsString('subscription has expired', $html);
        $this->assertStringContainsString('https://7th-tradehub.online/login', $html);
        $this->assertStringContainsString('Sign in to 7th Trade Hub', $html);
        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('rel="noopener"', $html);
        $this->assertStringNotContainsString('Session expired', $html);
    }

    public function test_admin_offline_copy_matches_merchant_guide()
    {
        $hub = app(\App\Services\SeventhTradeHub\SeventhTradeHubService::class);

        $expired = $hub->adminOfflineCopy('expired');
        $this->assertSame(
            'Your website subscription has expired. Sign in to your 7th Trade Hub account to renew this website subscription.',
            $expired['message']
        );
        $this->assertSame('Sign in to 7th Trade Hub', $expired['cta_label']);
        $this->assertStringEndsWith('/login', $expired['cta_href']);

        $suspended = $hub->adminOfflineCopy('suspended');
        $this->assertSame(
            'This website has been suspended. Contact 7th Trade Hub support for help.',
            $suspended['message']
        );
        $this->assertSame('Open Help Center', $suspended['cta_label']);
        $this->assertStringEndsWith('/help', $suspended['cta_href']);

        $cancelled = $hub->adminOfflineCopy('cancelled');
        $this->assertSame(
            'This website subscription has been cancelled. Contact 7th Trade Hub support for help.',
            $cancelled['message']
        );

        $inactive = $hub->adminOfflineCopy('inactive');
        $this->assertSame(
            'This website is inactive. Contact 7th Trade Hub support for help.',
            $inactive['message']
        );
    }
}
