<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Corrective: money columns as DECIMAL for safe arithmetic.
 * Casts existing values; empty/non-numeric varchar amounts become 0.
 */
class ConvertMoneyColumnsToDecimal extends Migration
{
    public function up()
    {
        if (Schema::hasTable('users')) {
            DB::statement('ALTER TABLE `users` MODIFY `account_bal` DECIMAL(16,2) NOT NULL DEFAULT 0.00');
            DB::statement('ALTER TABLE `users` MODIFY `roi` DECIMAL(16,2) NULL DEFAULT NULL');
            DB::statement('ALTER TABLE `users` MODIFY `bonus` DECIMAL(16,2) NULL DEFAULT NULL');
            DB::statement('ALTER TABLE `users` MODIFY `ref_bonus` DECIMAL(16,2) NULL DEFAULT NULL');
        }

        if (Schema::hasTable('deposits') && Schema::hasColumn('deposits', 'amount')) {
            DB::statement("UPDATE `deposits` SET `amount` = '0' WHERE `amount` IS NULL OR `amount` = '' OR `amount` NOT REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$'");
            DB::statement('ALTER TABLE `deposits` MODIFY `amount` DECIMAL(16,2) NULL DEFAULT NULL');
        }

        if (Schema::hasTable('withdrawals') && Schema::hasColumn('withdrawals', 'amount')) {
            DB::statement("UPDATE `withdrawals` SET `amount` = '0' WHERE `amount` IS NULL OR `amount` = '' OR `amount` NOT REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$'");
            DB::statement('ALTER TABLE `withdrawals` MODIFY `amount` DECIMAL(16,2) NULL DEFAULT NULL');
        }

        if (Schema::hasTable('user_plans') && Schema::hasColumn('user_plans', 'amount')) {
            DB::statement("UPDATE `user_plans` SET `amount` = '0' WHERE `amount` IS NULL OR `amount` = '' OR `amount` NOT REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$'");
            DB::statement('ALTER TABLE `user_plans` MODIFY `amount` DECIMAL(16,2) NULL DEFAULT NULL');
        }

        if (Schema::hasTable('tp__transactions') && Schema::hasColumn('tp__transactions', 'amount')) {
            DB::statement("UPDATE `tp__transactions` SET `amount` = '0' WHERE `amount` IS NULL OR `amount` = '' OR `amount` NOT REGEXP '^-?[0-9]+(\\\\.[0-9]+)?$'");
            DB::statement('ALTER TABLE `tp__transactions` MODIFY `amount` DECIMAL(16,2) NULL DEFAULT NULL');
        }
    }

    public function down()
    {
        if (Schema::hasTable('users')) {
            DB::statement('ALTER TABLE `users` MODIFY `account_bal` FLOAT NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE `users` MODIFY `roi` FLOAT NULL DEFAULT NULL');
            DB::statement('ALTER TABLE `users` MODIFY `bonus` FLOAT NULL DEFAULT NULL');
            DB::statement('ALTER TABLE `users` MODIFY `ref_bonus` FLOAT NULL DEFAULT NULL');
        }

        if (Schema::hasTable('deposits')) {
            DB::statement('ALTER TABLE `deposits` MODIFY `amount` VARCHAR(255) NULL DEFAULT NULL');
        }
        if (Schema::hasTable('withdrawals')) {
            DB::statement('ALTER TABLE `withdrawals` MODIFY `amount` VARCHAR(255) NULL DEFAULT NULL');
        }
        if (Schema::hasTable('user_plans')) {
            DB::statement('ALTER TABLE `user_plans` MODIFY `amount` VARCHAR(255) NULL DEFAULT NULL');
        }
        if (Schema::hasTable('tp__transactions')) {
            DB::statement('ALTER TABLE `tp__transactions` MODIFY `amount` VARCHAR(255) NULL DEFAULT NULL');
        }
    }
}
