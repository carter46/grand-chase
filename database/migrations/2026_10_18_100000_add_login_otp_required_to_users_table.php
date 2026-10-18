<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-user switch for the emailed login code that replaces the old login PIN screen.
 * On for every user by default; admins can turn it off per user.
 */
class AddLoginOtpRequiredToUsersTable extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('users', 'login_otp_required')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('login_otp_required')->default(true);
        });
    }

    public function down()
    {
        if (!Schema::hasColumn('users', 'login_otp_required')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('login_otp_required');
        });
    }
}
