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
}
