<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Hub URL must not live on `settings` (MySQL 1118 row-size on wide installs).
 * Creates seventh_tradehub_config and migrates any legacy settings column value.
 */
class EnsureSeventhTradehubHubUrlTextColumn extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('seventh_tradehub_config')) {
            Schema::create('seventh_tradehub_config', function (Blueprint $table) {
                $table->unsignedTinyInteger('id')->primary();
                $table->text('hub_url')->nullable();
                $table->dateTime('last_reconcile_at')->nullable();
                $table->dateTime('updated_at')->nullable();
            });
        }

        $exists = DB::table('seventh_tradehub_config')->where('id', 1)->exists();
        if (!$exists) {
            DB::table('seventh_tradehub_config')->insert([
                'id' => 1,
                'hub_url' => null,
                'updated_at' => now(),
            ]);
        }

        // Copy legacy value if an older deploy somehow added settings.seventh_tradehub_hub_url
        try {
            if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'seventh_tradehub_hub_url')) {
                $legacy = DB::table('settings')->where('id', 1)->value('seventh_tradehub_hub_url');
                if (is_string($legacy) && trim($legacy) !== '') {
                    DB::table('seventh_tradehub_config')->where('id', 1)->update([
                        'hub_url' => rtrim(trim($legacy), '/'),
                        'updated_at' => now(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // ignore — config table is enough
        }
    }

    public function down()
    {
        Schema::dropIfExists('seventh_tradehub_config');
    }
}
