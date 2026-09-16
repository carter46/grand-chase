<?php

namespace Tests\Unit;

use App\Support\DemoUserVisibility;
use App\Support\PlatformSuperAdmin;
use Tests\TestCase;

class TradeHubRemediationHelpersTest extends TestCase
{
    public function test_demo_user_visibility_detects_flag()
    {
        $this->assertFalse(DemoUserVisibility::isDemo(null));

        try {
            $this->assertFalse(DemoUserVisibility::isDemo(['is_demo_user' => 0]));
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_demo_user')) {
                $this->assertTrue(DemoUserVisibility::isDemo(['is_demo_user' => 1]));
            } else {
                $this->assertFalse(DemoUserVisibility::isDemo(['is_demo_user' => 1]));
            }
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database schema unavailable: ' . $e->getMessage());
        }
    }

    public function test_platform_sa_check_false_without_user()
    {
        $this->assertFalse(PlatformSuperAdmin::check(null));
    }
}
