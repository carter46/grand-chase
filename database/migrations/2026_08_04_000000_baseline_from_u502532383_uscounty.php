<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Baseline schema generated from authoritative dump database/u502532383_uscounty.sql
 * via: php database/scripts/extract_schema_from_dump.php
 *
 * Schema only — no production row data.
 * Do not hand-edit the SQL artifact; regenerate from the dump.
 *
 * Idempotent: if the inventory's first table already exists, skip CREATE
 * (safe after dump import without mark_baseline, or re-run migrate).
 */
class BaselineFromU502532383Uscounty extends Migration
{
    public function up()
    {
        $path = database_path('schema/baseline_u502532383_uscounty.sql');
        if (!File::exists($path)) {
            throw new RuntimeException("Baseline schema missing at {$path}. Run: php database/scripts/extract_schema_from_dump.php");
        }

        $inventoryPath = database_path('schema/baseline_inventory.json');
        if (File::exists($inventoryPath)) {
            $inventory = json_decode(File::get($inventoryPath), true);
            $tables = array_keys($inventory['tables'] ?? []);
            $probe = $tables[0] ?? 'users';
            if (Schema::hasTable($probe)) {
                // Dump already imported (or baseline previously applied): skip CREATE.
                return;
            }
        }

        $sql = File::get($path);
        $this->runSqlScript($sql);
    }

    public function down()
    {
        $inventoryPath = database_path('schema/baseline_inventory.json');
        if (!File::exists($inventoryPath)) {
            throw new RuntimeException("Baseline inventory missing at {$inventoryPath}");
        }

        $inventory = json_decode(File::get($inventoryPath), true);
        $tables = array_keys($inventory['tables'] ?? []);
        rsort($tables);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $table) {
            DB::statement('DROP TABLE IF EXISTS `' . str_replace('`', '``', $table) . '`');
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function runSqlScript(string $sql): void
    {
        // Strip full-line SQL comments only.
        $sql = preg_replace('/^--.*$/m', '', $sql);
        // Split on statement terminators at end of lines (baseline artifact has one statement per block).
        $parts = preg_split('/;\s*(?:\n|$)/', $sql);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            foreach ($parts as $statement) {
                $statement = trim($statement);
                if ($statement === '' || strtoupper($statement) === 'COMMIT') {
                    continue;
                }
                // Avoid re-setting flags mid-script if already present.
                if (preg_match('/^SET\s+FOREIGN_KEY_CHECKS\s*=/i', $statement)) {
                    continue;
                }
                // Skip CREATE TABLE if already present (partial imports / re-runs).
                if (preg_match('/^CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?([a-z0-9_]+)`?/i', $statement, $m)) {
                    if (Schema::hasTable($m[1])) {
                        continue;
                    }
                }
                DB::unprepared($statement);
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}
