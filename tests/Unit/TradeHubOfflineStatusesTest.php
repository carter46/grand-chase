<?php

namespace Tests\Unit;

use App\Services\SeventhTradeHub\SeventhTradeHubService;
use Tests\TestCase;

class TradeHubOfflineStatusesTest extends TestCase
{
    /** @var SeventhTradeHubService */
    private $hub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hub = app(SeventhTradeHubService::class);
    }

    public function test_only_merchant_guide_statuses_are_offline()
    {
        $this->assertFalse($this->hub->subscriptionIsOffline([
            'status' => 'active',
            'expires_at' => null,
        ]));

        // pending_setup alone stays online on authenticated sites (MERCHANT-GUIDE + Hub sample)
        $this->assertFalse($this->hub->subscriptionIsOffline([
            'status' => 'pending_setup',
            'expires_at' => null,
        ]));

        foreach (['expired', 'suspended', 'cancelled', 'inactive'] as $status) {
            $this->assertTrue(
                $this->hub->subscriptionIsOffline(['status' => $status, 'expires_at' => null]),
                "Expected offline for status={$status}"
            );
        }

        // Unknown Hub status → fail closed
        $this->assertTrue($this->hub->subscriptionIsOffline([
            'status' => 'mystery_status',
            'expires_at' => null,
        ]));
    }

    public function test_past_expires_at_is_offline_even_when_active()
    {
        $this->assertTrue($this->hub->subscriptionIsOffline([
            'status' => 'active',
            'expires_at' => '2020-01-01 00:00:00',
        ]));
    }

    public function test_subscription_is_expired_aliases_offline()
    {
        $sub = ['status' => 'suspended', 'expires_at' => null];
        $this->assertSame(
            $this->hub->subscriptionIsOffline($sub),
            $this->hub->subscriptionIsExpired($sub)
        );
    }

    public function test_trust_expired_when_last_sync_stale()
    {
        $this->assertTrue($this->hub->subscriptionTrustExpired([
            'status' => 'active',
            'expires_at' => null,
            'last_sync_at' => '2020-01-01 00:00:00',
        ]));

        $this->assertFalse($this->hub->subscriptionTrustExpired([
            'status' => 'active',
            'expires_at' => null,
            'last_sync_at' => gmdate('Y-m-d H:i:s'),
        ]));

        // Already offline by status — trust helper is not the signal
        $this->assertFalse($this->hub->subscriptionTrustExpired([
            'status' => 'expired',
            'expires_at' => null,
            'last_sync_at' => '2020-01-01 00:00:00',
        ]));
    }

    public function test_resolve_offline_display_status()
    {
        $this->assertSame('suspended', $this->hub->resolveOfflineDisplayStatus([
            'status' => 'suspended',
            'expires_at' => null,
        ]));

        $this->assertSame('expired', $this->hub->resolveOfflineDisplayStatus([
            'status' => 'active',
            'expires_at' => '2020-01-01 00:00:00',
        ]));

        $this->assertSame('expired', $this->hub->resolveOfflineDisplayStatus([
            'status' => 'active',
            'expires_at' => null,
            'last_sync_at' => '2020-01-01 00:00:00',
        ]));
    }

    public function test_admin_offline_copy_for_expired_and_suspended()
    {
        $expired = $this->hub->adminOfflineCopy('expired');
        $this->assertStringContainsString('expired', strtolower($expired['message']));
        $this->assertStringContainsString('/login', $expired['cta_href']);
        $this->assertSame('Sign in to 7th Trade Hub', $expired['cta_label']);

        $suspended = $this->hub->adminOfflineCopy('suspended');
        $this->assertStringContainsString('suspended', strtolower($suspended['message']));
        $this->assertStringContainsString('/help', $suspended['cta_href']);
        $this->assertSame('Open Help Center', $suspended['cta_label']);
    }
}
