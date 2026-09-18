<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastReconcileAtToSeventhTradehubConfig extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('seventh_tradehub_config')) {
            return;
        }
        if (!Schema::hasColumn('seventh_tradehub_config', 'last_reconcile_at')) {
            Schema::table('seventh_tradehub_config', function (Blueprint $table) {
                $table->dateTime('last_reconcile_at')->nullable()->after('hub_url');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('seventh_tradehub_config') && Schema::hasColumn('seventh_tradehub_config', 'last_reconcile_at')) {
            Schema::table('seventh_tradehub_config', function (Blueprint $table) {
                $table->dropColumn('last_reconcile_at');
            });
        }
    }
}
