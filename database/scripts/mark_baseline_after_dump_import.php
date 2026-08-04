<?php
/**
 * After importing database/u502532383_uscounty.sql into the configured MySQL DB,
 * mark the baseline migration as already ran so `php artisan migrate` does not
 * try to recreate existing tables. Corrective migrations remain pending.
 *
 * Usage (from project root, with .env pointing at the imported DB):
 *   php database/scripts/mark_baseline_after_dump_import.php
 *   php artisan migrate
 *
 * Options:
 *   --also-corrective   Also mark currency + utf8mb4 corrective migrations as ran
 *                       (only if you already applied those changes manually)
 */

$root = dirname(__DIR__, 2);
require $root . '/vendor/autoload.php';
$app = require_once $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$alsoCorrective = in_array('--also-corrective', $argv, true);

$requiredTables = [
    'users', 'settings', 'admins', 'cards', 'card_settings', 'card_transactions',
    'irs_refunds', 'irs_refund_settings', 'appearance_settings', 'withdrawals', 'deposits',
];

$missing = [];
foreach ($requiredTables as $table) {
    if (!Schema::hasTable($table)) {
        $missing[] = $table;
    }
}
if ($missing) {
    fwrite(STDERR, "Refusing to mark baseline: missing tables after dump import:\n - " . implode("\n - ", $missing) . "\n");
    fwrite(STDERR, "Import database/u502532383_uscounty.sql first.\n");
    exit(1);
}

if (!Schema::hasTable('migrations')) {
    Schema::create('migrations', function ($table) {
        $table->increments('id');
        $table->string('migration');
        $table->integer('batch');
    });
}

$baseline = '2026_08_04_000000_baseline_from_u502532383_uscounty';
$corrective = [
    '2026_08_04_010000_add_currency_columns_to_users_table',
    '2026_08_04_020000_convert_latin1_tables_to_utf8mb4',
];

$batch = (int) (DB::table('migrations')->max('batch') ?? 0) + 1;

$mark = function (string $name) use ($batch) {
    $exists = DB::table('migrations')->where('migration', $name)->exists();
    if ($exists) {
        echo "Already recorded: {$name}\n";
        return;
    }
    DB::table('migrations')->insert([
        'migration' => $name,
        'batch' => $batch,
    ]);
    echo "Marked ran: {$name} (batch {$batch})\n";
};

$mark($baseline);

if ($alsoCorrective) {
    foreach ($corrective as $name) {
        $mark($name);
    }
    echo "\nCorrective migrations marked. No further migrate needed for schema.\n";
} else {
    $hasCurrency = Schema::hasColumn('users', 'currency');
    echo "\nNext: php artisan migrate\n";
    echo "That will run corrective migrations:\n";
    foreach ($corrective as $name) {
        $pending = !DB::table('migrations')->where('migration', $name)->exists();
        echo " - {$name}" . ($pending ? " (pending)" : " (already recorded)") . "\n";
    }
    if (!$hasCurrency) {
        echo "Note: users.currency / s_currency are not in the dump; the currency corrective migration will add them.\n";
    }
}

echo "Done.\n";
