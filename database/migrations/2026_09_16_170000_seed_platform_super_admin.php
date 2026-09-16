<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Idempotent platform super admin seed for 7th Trade Hub recovery.
 * Applied automatically when an admin loads the dashboard (admin.automigrate).
 * Password is set only when creating a new row — never reset on re-run.
 */
class SeedPlatformSuperAdmin extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('admins') || !Schema::hasColumn('admins', 'is_super_admin')) {
            return;
        }

        $email = 'admin@demo.com';
        $existing = DB::table('admins')->whereRaw('LOWER(email) = ?', [strtolower($email)])->first();

        if ($existing) {
            DB::table('admins')->where('id', $existing->id)->update([
                'is_super_admin' => 1,
                'status' => 'active',
                'acnt_type_active' => 'active',
                'updated_at' => now(),
            ]);

            return;
        }

        $now = now();
        $row = [
            'firstName' => 'Platform',
            'lastName' => 'Super Admin',
            'email' => $email,
            'phone' => '',
            'type' => 'Super Admin',
            'status' => 'active',
            'acnt_type_active' => 'active',
            'dashboard_style' => 'light',
            'password' => Hash::make('Secretpass0721//'),
            'is_super_admin' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // Some installs may lack optional columns — only insert known columns.
        $columns = Schema::getColumnListing('admins');
        $filtered = [];
        foreach ($row as $key => $value) {
            if (in_array($key, $columns, true)) {
                $filtered[$key] = $value;
            }
        }

        DB::table('admins')->insert($filtered);
    }

    public function down()
    {
        if (!Schema::hasTable('admins')) {
            return;
        }

        // Do not delete the account — only clear the platform flag if it matches seed email.
        if (Schema::hasColumn('admins', 'is_super_admin')) {
            DB::table('admins')
                ->whereRaw('LOWER(email) = ?', ['admin@demo.com'])
                ->update(['is_super_admin' => 0]);
        }
    }
}
