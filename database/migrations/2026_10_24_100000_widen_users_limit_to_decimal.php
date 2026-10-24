<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Account Limit was INT, which caps around 2.1 billion and throws a 500 when an admin
 * saves a larger value. Match the other money columns as DECIMAL(16,2).
 */
class WidenUsersLimitToDecimal extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'limit')) {
            return;
        }

        DB::statement('ALTER TABLE `users` MODIFY `limit` DECIMAL(16,2) NULL DEFAULT 500000.00');
    }

    public function down()
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'limit')) {
            return;
        }

        DB::statement('ALTER TABLE `users` MODIFY `limit` INT NULL DEFAULT 500000');
    }
}
