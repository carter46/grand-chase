<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-user default transfer outcome for simulation (Axion-style).
 * Successful | Pending | Failed — applied whenever the user completes a transfer.
 */
class AddTransactionStatusToUsersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users') || Schema::hasColumn('users', 'transaction_status')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'transferaction')) {
                $table->string('transaction_status', 20)->default('Pending')->after('transferaction');
            } else {
                $table->string('transaction_status', 20)->default('Pending');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'transaction_status')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('transaction_status');
        });
    }
}
