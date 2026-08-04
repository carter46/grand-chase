<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Corrective migration (post-baseline): convert known latin1 tables to utf8mb4.
 * Baseline intentionally preserved production latin1; this improves encoding separately.
 */
class ConvertLatin1TablesToUtf8mb4 extends Migration
{
    protected $tables = [
        'appearance_settings',
        'card_settings',
        'irs_refunds',
        'irs_refund_settings',
        'notifications',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            if (!\Illuminate\Support\Facades\Schema::hasTable($table)) {
                continue;
            }
            DB::statement("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            if (!\Illuminate\Support\Facades\Schema::hasTable($table)) {
                continue;
            }
            // Lossy for non-latin1 data — rollback only for local/dev.
            DB::statement("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET latin1 COLLATE latin1_swedish_ci");
        }
    }
}
