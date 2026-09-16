<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Services\SeventhTradeHub\SeventhTradeHubService;
use App\Support\PlatformSuperAdmin;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TradeHubNonceAndSaGuardTest extends TestCase
{
    public function test_record_nonce_rejects_duplicate_request_id_as_replay()
    {
        if (!$this->schemaAvailable() || !Schema::hasTable('seventh_tradehub_nonces')) {
            $this->markTestSkipped('seventh_tradehub_nonces table not available');
        }

        $hub = new SeventhTradeHubService();
        $integration = ['integration_id' => 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'];
        $payload = [
            'request_id' => 'remediation-nonce-test-' . uniqid('', true),
            'nonce' => 'nonce-one',
        ];

        $first = $hub->recordNonce($integration, $payload);
        $this->assertTrue($first['ok']);

        $second = $hub->recordNonce($integration, $payload);
        $this->assertFalse($second['ok']);
        $this->assertSame('replay_detected', $second['error']);
    }

    public function test_record_nonce_soft_allows_empty_request_id()
    {
        $hub = new SeventhTradeHubService();
        $result = $hub->recordNonce(
            ['integration_id' => 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'],
            ['request_id' => '', 'nonce' => '']
        );
        $this->assertTrue($result['ok']);
    }

    public function test_assert_can_mutate_admin_blocks_peer_on_platform_sa()
    {
        if (!$this->schemaAvailable() || !Schema::hasColumn('admins', 'is_super_admin')) {
            $this->markTestSkipped('is_super_admin column not available');
        }

        $target = new Admin(['is_super_admin' => 1, 'email' => 'sa@example.com']);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        PlatformSuperAdmin::assertCanMutateAdmin($target, 'block');
    }

    private function schemaAvailable()
    {
        try {
            Schema::hasTable('migrations');

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
