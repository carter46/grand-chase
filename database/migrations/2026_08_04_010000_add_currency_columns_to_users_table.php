<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Corrective migration (post-baseline): per-user currency overrides used by
 * ManageUsersController and AppServiceProvider. Absent from production dump;
 * added after baseline fidelity was verified.
 */
class AddCurrencyColumnsToUsersTable extends Migration
{
    public function up()
    {
        $addCurrency = !Schema::hasColumn('users', 'currency');
        $addSCurrency = !Schema::hasColumn('users', 's_currency');

        if (!$addCurrency && !$addSCurrency) {
            return;
        }

        Schema::table('users', function (Blueprint $table) use ($addCurrency, $addSCurrency) {
            if ($addCurrency) {
                $table->string('currency', 10)->nullable()->after('ref_link');
            }
            if ($addSCurrency) {
                $after = $addCurrency ? 'currency' : 'ref_link';
                $table->string('s_currency', 25)->nullable()->after($after);
            }
        });
    }

    public function down()
    {
        $drop = [];
        if (Schema::hasColumn('users', 's_currency')) {
            $drop[] = 's_currency';
        }
        if (Schema::hasColumn('users', 'currency')) {
            $drop[] = 'currency';
        }

        if ($drop === []) {
            return;
        }

        Schema::table('users', function (Blueprint $table) use ($drop) {
            $table->dropColumn($drop);
        });
    }
}
