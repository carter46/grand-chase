<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Corrective: dump uniquely indexes zipcode under misnamed key kycs_email_unique.
 * Replace with unique user_id (one KYC row per user).
 */
class FixKycsUniqueIndex extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('kycs')) {
            return;
        }

        $indexes = collect(DB::select('SHOW INDEX FROM `kycs`'));
        $hasZipUnique = $indexes->contains(function ($row) {
            return ($row->Key_name ?? '') === 'kycs_email_unique';
        });

        if ($hasZipUnique) {
            Schema::table('kycs', function (Blueprint $table) {
                $table->dropUnique('kycs_email_unique');
            });
        }

        $hasUserUnique = $indexes->contains(function ($row) {
            return ($row->Key_name ?? '') === 'kycs_user_id_unique';
        });

        if (!$hasUserUnique && Schema::hasColumn('kycs', 'user_id')) {
            // Drop duplicate KYC rows keeping the latest id per user before unique add.
            DB::statement('
                DELETE k1 FROM `kycs` k1
                INNER JOIN `kycs` k2
                ON k1.user_id = k2.user_id AND k1.id < k2.id
            ');

            Schema::table('kycs', function (Blueprint $table) {
                $table->unique('user_id', 'kycs_user_id_unique');
            });
        }
    }

    public function down()
    {
        if (!Schema::hasTable('kycs')) {
            return;
        }

        $indexes = collect(DB::select('SHOW INDEX FROM `kycs`'));
        if ($indexes->contains(fn ($row) => ($row->Key_name ?? '') === 'kycs_user_id_unique')) {
            Schema::table('kycs', function (Blueprint $table) {
                $table->dropUnique('kycs_user_id_unique');
            });
        }

        if (!$indexes->contains(fn ($row) => ($row->Key_name ?? '') === 'kycs_email_unique')) {
            Schema::table('kycs', function (Blueprint $table) {
                $table->unique('zipcode', 'kycs_email_unique');
            });
        }
    }
}
