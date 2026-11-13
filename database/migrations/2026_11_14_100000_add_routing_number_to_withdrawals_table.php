<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * International wire transfers need a routing number alongside SWIFT and IBAN.
 */
class AddRoutingNumberToWithdrawalsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('withdrawals') || Schema::hasColumn('withdrawals', 'routing_number')) {
            return;
        }

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->string('routing_number', 50)->nullable()->after('iban');
        });
    }

    public function down()
    {
        if (!Schema::hasTable('withdrawals') || !Schema::hasColumn('withdrawals', 'routing_number')) {
            return;
        }

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn('routing_number');
        });
    }
}
