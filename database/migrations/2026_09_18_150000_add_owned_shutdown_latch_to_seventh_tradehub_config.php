<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOwnedShutdownLatchToSeventhTradehubConfig extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('seventh_tradehub_config')) {
            return;
        }
        if (!Schema::hasColumn('seventh_tradehub_config', 'owned_shutdown_latch')) {
            Schema::table('seventh_tradehub_config', function (Blueprint $table) {
                $after = Schema::hasColumn('seventh_tradehub_config', 'last_reconcile_at')
                    ? 'last_reconcile_at'
                    : 'hub_url';
                $table->unsignedTinyInteger('owned_shutdown_latch')->default(0)->after($after);
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('seventh_tradehub_config') && Schema::hasColumn('seventh_tradehub_config', 'owned_shutdown_latch')) {
            Schema::table('seventh_tradehub_config', function (Blueprint $table) {
                $table->dropColumn('owned_shutdown_latch');
            });
        }
    }
}
