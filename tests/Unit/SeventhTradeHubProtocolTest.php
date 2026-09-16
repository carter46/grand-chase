<?php

namespace Tests\Unit;

use App\Services\SeventhTradeHub\SeventhTradeHubService;
use Tests\TestCase;

class SeventhTradeHubProtocolTest extends TestCase
{
    public function test_canonicalize_matches_hub_sample_shapes()
    {
        $hub = new SeventhTradeHubService();

        $this->assertSame('true', $hub->canonicalize(true));
        $this->assertSame('false', $hub->canonicalize(false));
        $this->assertSame('null', $hub->canonicalize(null));
        $this->assertSame('42', $hub->canonicalize(42));
        $this->assertSame('"hello"', $hub->canonicalize('hello'));
        $this->assertSame('[1,2]', $hub->canonicalize([1, 2]));

        $object = ['b' => 2, 'a' => 1];
        $this->assertSame('{"a":1,"b":2}', $hub->canonicalize($object));
    }

    public function test_sign_and_verify_round_trip()
    {
        $hub = new SeventhTradeHubService();
        $secret = 'test-client-secret';
        $payload = [
            'integration_id' => '11111111-1111-1111-1111-111111111111',
            'context' => 'demo',
            'role' => 'health',
            'request_id' => 'abc',
            'nonce' => 'def',
            'issued_at' => '2026-09-02T10:00:00+00:00',
            'expires_at' => '2026-09-02T10:02:00+00:00',
        ];

        $signed = $hub->signPayload($payload, $secret);
        $this->assertArrayHasKey('signature', $signed);
        $this->assertTrue($hub->verifyPayload($signed, $secret));

        $signed['signature'] = 'deadbeef';
        $this->assertFalse($hub->verifyPayload($signed, $secret));
    }

    public function test_assertion_expired_with_empty_expires()
    {
        $hub = new SeventhTradeHubService();
        $this->assertTrue($hub->assertionExpired([]));
        $this->assertTrue($hub->assertionExpired(['expires_at' => '2000-01-01T00:00:00+00:00']));
        $this->assertFalse($hub->assertionExpired([
            'expires_at' => (new \DateTimeImmutable('+10 minutes'))->format(\DateTimeInterface::ATOM),
        ]));
    }

    public function test_capabilities_per_context()
    {
        $hub = new SeventhTradeHubService();
        $this->assertSame(
            ['health', 'demo_user_login', 'demo_admin_login'],
            $hub->capabilitiesForContext('demo')
        );
        $this->assertContains('admin_credential_sync', $hub->capabilitiesForContext('owned_tool'));
        $this->assertContains('shutdown_on_expiry', $hub->capabilitiesForContext('owned_tool'));
    }

    public function test_seal_sth0_round_trip()
    {
        $hub = new SeventhTradeHubService();
        $sealed = $hub->sealSecret('super-secret');
        $this->assertStringStartsWith('sth0:', $sealed);
        $this->assertSame('super-secret', $hub->unsealSecret($sealed));
    }
}
