<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Corrective integrity:
 * - Drop Dead feature table autologin_tokens
 * - Add crypto_records.user_id for scoping
 * - Remove orphan rows that would block future FKs
 */
class IntegrityOrphansCryptoUserAndDropAutologin extends Migration
{
    public function up()
    {
        if (Schema::hasTable('autologin_tokens')) {
            Schema::drop('autologin_tokens');
        }

        if (Schema::hasTable('crypto_records') && !Schema::hasColumn('crypto_records', 'user_id')) {
            Schema::table('crypto_records', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id')->index();
            });
        }

        // Soft-FK orphan cleanup (logical user refs — no physical FKs yet)
        if (Schema::hasTable('deposits') && Schema::hasTable('users')) {
            DB::statement('DELETE FROM `deposits` WHERE `user` IS NOT NULL AND `user` NOT IN (SELECT `id` FROM `users`)');
        }
        if (Schema::hasTable('withdrawals') && Schema::hasTable('users')) {
            DB::statement('DELETE FROM `withdrawals` WHERE `user` IS NOT NULL AND `user` NOT IN (SELECT `id` FROM `users`)');
        }
        if (Schema::hasTable('user_plans') && Schema::hasTable('users')) {
            DB::statement('DELETE FROM `user_plans` WHERE `user` IS NOT NULL AND `user` NOT IN (SELECT `id` FROM `users`)');
        }
        if (Schema::hasTable('mt4_details') && Schema::hasTable('users')) {
            DB::statement('DELETE FROM `mt4_details` WHERE `client_id` IS NOT NULL AND `client_id` NOT IN (SELECT `id` FROM `users`)');
        }
        if (Schema::hasTable('notifications') && Schema::hasTable('users')) {
            DB::statement('DELETE FROM `notifications` WHERE `user_id` IS NOT NULL AND `user_id` NOT IN (SELECT `id` FROM `users`)');
        }
        if (Schema::hasTable('kycs') && Schema::hasTable('users')) {
            DB::statement('DELETE FROM `kycs` WHERE `user_id` IS NOT NULL AND `user_id` NOT IN (SELECT `id` FROM `users`)');
        }
        if (Schema::hasTable('irs_refunds') && Schema::hasTable('users')) {
            DB::statement('DELETE FROM `irs_refunds` WHERE `user_id` IS NOT NULL AND `user_id` NOT IN (SELECT `id` FROM `users`)');
        }
        if (Schema::hasTable('crypto_accounts') && Schema::hasTable('users')) {
            DB::statement('DELETE FROM `crypto_accounts` WHERE `user_id` IS NOT NULL AND `user_id` NOT IN (SELECT `id` FROM `users`)');
        }
    }

    public function down()
    {
        if (Schema::hasTable('crypto_records') && Schema::hasColumn('crypto_records', 'user_id')) {
            Schema::table('crypto_records', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }

        // Recreate empty autologin_tokens shell matching baseline (no data restore)
        if (!Schema::hasTable('autologin_tokens')) {
            Schema::create('autologin_tokens', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('token')->nullable();
                $table->timestamps();
            });
        }
    }
}
