<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Dev/demo seed only — NOT a production restore.
 *
 * Production data (admins, settings, plans, wdmethods, appearance, etc.)
 * comes from SQL dump import / backups. See docs/database/POLICY.md.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            MinimumConfigSeeder::class,
        ]);
    }
}
