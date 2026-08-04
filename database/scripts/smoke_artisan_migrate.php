<?php
/**
 * Apply active migrations (baseline + corrective) to an empty verify DB via Artisan-like flow:
 * uses PDO + the baseline SQL + corrective SQL equivalent for smoke test without full Laravel boot.
 *
 * Preferred full check when .env points at an empty DB:
 *   php artisan migrate --force
 */

$root = dirname(__DIR__, 2);
require $root . '/vendor/autoload.php';

$app = require_once $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dbName = 'grand_chase_migrate_verify';
$mysql = 'C:\\xampp\\mysql\\bin\\mysql.exe';

function sh(string $cmd): void
{
    passthru($cmd, $code);
    if ($code !== 0) {
        fwrite(STDERR, "Command failed ({$code}): {$cmd}\n");
        exit($code);
    }
}

sh('"' . $mysql . '" -u root -e "DROP DATABASE IF EXISTS `' . $dbName . '`; CREATE DATABASE `' . $dbName . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"');

// Point config at verify DB for this process
config(['database.connections.mysql.database' => $dbName]);
config(['database.connections.mysql.username' => 'root']);
config(['database.connections.mysql.password' => '']);
config(['database.connections.mysql.host' => '127.0.0.1']);
DB::purge('mysql');
DB::reconnect('mysql');

$exit = $kernel->call('migrate', ['--force' => true]);
echo $kernel->output();
if ($exit !== 0) {
    sh('"' . $mysql . '" -u root -e "DROP DATABASE IF EXISTS `' . $dbName . '`;"');
    exit($exit);
}

$seedExit = $kernel->call('db:seed', ['--force' => true]);
echo $kernel->output();
if ($seedExit !== 0) {
    sh('"' . $mysql . '" -u root -e "DROP DATABASE IF EXISTS `' . $dbName . '`;"');
    exit($seedExit);
}

$settings = DB::table('settings')->where('id', 1)->first();
$admin = DB::table('admins')->first();
if (!$settings || !$admin) {
    fwrite(STDERR, "MinimumConfigSeeder did not create settings/admin.\n");
    sh('"' . $mysql . '" -u root -e "DROP DATABASE IF EXISTS `' . $dbName . '`;"');
    exit(1);
}
echo "Seeded settings.site_name={$settings->site_name}; admin.email={$admin->email}\n";

$tables = collect(DB::select('SHOW TABLES'))->map(fn ($r) => array_values((array) $r)[0])->sort()->values();
echo "Tables after migrate: " . $tables->count() . "\n";

$hasCurrency = Schema::hasColumn('users', 'currency');
$hasSCurrency = Schema::hasColumn('users', 's_currency');
echo "users.currency: " . ($hasCurrency ? 'yes' : 'no') . "\n";
echo "users.s_currency: " . ($hasSCurrency ? 'yes' : 'no') . "\n";

$balType = DB::selectOne("SELECT DATA_TYPE AS t FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'users' AND COLUMN_NAME = 'account_bal'", [$dbName]);
echo "users.account_bal type: " . ($balType->t ?? 'unknown') . "\n";
if (($balType->t ?? '') !== 'decimal') {
    fwrite(STDERR, "Expected users.account_bal DECIMAL after money migration.\n");
    sh('"' . $mysql . '" -u root -e "DROP DATABASE IF EXISTS `' . $dbName . '`;"');
    exit(1);
}

if (Schema::hasTable('autologin_tokens')) {
    fwrite(STDERR, "autologin_tokens should have been dropped.\n");
    sh('"' . $mysql . '" -u root -e "DROP DATABASE IF EXISTS `' . $dbName . '`;"');
    exit(1);
}

if (!Schema::hasColumn('crypto_records', 'user_id')) {
    fwrite(STDERR, "crypto_records.user_id missing.\n");
    sh('"' . $mysql . '" -u root -e "DROP DATABASE IF EXISTS `' . $dbName . '`;"');
    exit(1);
}

$charset = DB::selectOne("SELECT CCSA.character_set_name AS cs
  FROM information_schema.`TABLES` T
  JOIN information_schema.`COLLATION_CHARACTER_SET_APPLICABILITY` CCSA
    ON CCSA.collation_name = T.table_collation
  WHERE T.table_schema = ? AND T.table_name = 'appearance_settings'", [$dbName]);
echo "appearance_settings charset: " . ($charset->cs ?? 'unknown') . "\n";

sh('"' . $mysql . '" -u root -e "DROP DATABASE IF EXISTS `' . $dbName . '`;"');

if (!$hasCurrency || !$hasSCurrency) {
    fwrite(STDERR, "Corrective currency columns missing after migrate.\n");
    exit(1);
}

echo "Artisan migrate smoke PASSED.\n";
