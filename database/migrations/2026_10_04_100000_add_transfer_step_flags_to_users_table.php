<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Per-user transfer verification steps (code1/code2/code3/OTP), replacing the
 * site-wide settings.code{n}status / settings.otp switches.
 * Each flag is seeded from the current site-wide value so behaviour is unchanged on deploy.
 */
class AddTransferStepFlagsToUsersTable extends Migration
{
    private const FLAGS = [
        'code1_required' => 'code1status',
        'code2_required' => 'code2status',
        'code3_required' => 'code3status',
        'transfer_otp_required' => 'otp',
    ];

    public function up()
    {
        $missing = array_values(array_filter(array_keys(self::FLAGS), function ($column) {
            return !Schema::hasColumn('users', $column);
        }));

        if ($missing === []) {
            return;
        }

        Schema::table('users', function (Blueprint $table) use ($missing) {
            foreach ($missing as $column) {
                $table->boolean($column)->default(false);
            }
        });

        $settings = Schema::hasTable('settings') ? DB::table('settings')->where('id', 1)->first() : null;
        if (!$settings) {
            return;
        }

        $seed = [];
        foreach ($missing as $column) {
            $siteColumn = self::FLAGS[$column];
            if (isset($settings->{$siteColumn}) && (int) $settings->{$siteColumn} === 1) {
                $seed[$column] = 1;
            }
        }

        if ($seed !== []) {
            DB::table('users')->update($seed);
        }

        foreach (['code1', 'code2', 'code3'] as $code) {
            if (empty($seed[$code . '_required'])) {
                continue;
            }
            DB::table('users')
                ->where(function ($query) use ($code) {
                    $query->whereNull($code)->orWhere($code, '');
                })
                ->select('id')
                ->chunkById(200, function ($users) use ($code) {
                    foreach ($users as $user) {
                        DB::table('users')->where('id', $user->id)->update([$code => (string) random_int(100000, 999999)]);
                    }
                });
        }
    }

    public function down()
    {
        $existing = array_values(array_filter(array_keys(self::FLAGS), function ($column) {
            return Schema::hasColumn('users', $column);
        }));

        if ($existing === []) {
            return;
        }

        Schema::table('users', function (Blueprint $table) use ($existing) {
            $table->dropColumn($existing);
        });
    }
}
