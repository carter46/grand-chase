<?php
/**
 * Verify baseline schema against authoritative dump + optional live MySQL apply.
 *
 * Live mode imports schema-only from the dump into a reference DB and the
 * baseline artifact into a verify DB, then deep-compares information_schema
 * (tables, columns type/null/default/extra, indexes, FKs).
 *
 * Usage:
 *   php database/scripts/verify_baseline_schema.php
 *   php database/scripts/verify_baseline_schema.php --live
 */

$root = dirname(__DIR__, 2);
$dumpPath = $root . '/database/u502532383_uscounty.sql';
$baselinePath = $root . '/database/schema/baseline_u502532383_uscounty.sql';
$inventoryPath = $root . '/database/schema/baseline_inventory.json';
$mysql = 'C:\\xampp\\mysql\\bin\\mysql.exe';
$live = in_array('--live', $argv, true);
$errors = [];

function fail_exit(array $errors): void
{
    fwrite(STDERR, "BASELINE VERIFICATION FAILED\n");
    foreach ($errors as $e) {
        fwrite(STDERR, " - {$e}\n");
    }
    exit(1);
}

function mysql_file(string $mysql, string $db, string $sqlFile): array
{
    $cmd = sprintf('"%s" -u root %s < "%s"', $mysql, $db !== '' ? $db : '', $sqlFile);
    $descriptors = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
    $proc = proc_open('cmd /c ' . $cmd, $descriptors, $pipes);
    if (!is_resource($proc)) {
        return [1, '', 'failed to start mysql'];
    }
    $out = stream_get_contents($pipes[1]);
    $err = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    return [proc_close($proc), $out, $err];
}

function mysql_e(string $mysql, string $sql): array
{
    $tmp = tempnam(sys_get_temp_dir(), 'gc_sql_');
    file_put_contents($tmp, $sql);
    $result = mysql_file($mysql, '', $tmp);
    @unlink($tmp);
    return $result;
}

function normalize_default(?string $default): string
{
    if ($default === null || $default === '' || strtoupper($default) === 'NULL') {
        return 'NULL';
    }
    $default = trim($default, "'\"");
    $default = preg_replace('/^current_timestamp(?:\(\))?$/i', 'CURRENT_TIMESTAMP', $default);
    return strtolower($default);
}

function normalize_type(string $type): string
{
    $type = strtolower(trim($type));
    $type = preg_replace('/\s+/', '', $type);
    // Ignore display widths on integer types.
    $type = preg_replace('/(tiny|small|medium|big)?int\(\d+\)/', '$1int', $type);
    return $type;
}

function parse_tsv_map(string $out, int $expectCols): array
{
    $rows = [];
    foreach (explode("\n", trim($out)) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, 'TABLE_NAME') || str_starts_with($line, 'INDEX_NAME') || str_starts_with($line, 'CONSTRAINT_NAME')) {
            continue;
        }
        $parts = preg_split("/\t/", $line);
        if (count($parts) < $expectCols) {
            continue;
        }
        $rows[] = $parts;
    }
    return $rows;
}

if (!is_file($dumpPath) || !is_file($baselinePath) || !is_file($inventoryPath)) {
    fail_exit(['Missing dump, baseline SQL, or inventory JSON. Run extract_schema_from_dump.php first.']);
}

$dump = str_replace(["\r\n", "\r"], "\n", file_get_contents($dumpPath));
$baseline = str_replace(["\r\n", "\r"], "\n", file_get_contents($baselinePath));
$inventory = json_decode(file_get_contents($inventoryPath), true);

preg_match_all('/CREATE TABLE\s+`([^`]+)`/i', $dump, $dumpTables);
$dumpTableSet = array_values(array_filter($dumpTables[1], fn ($t) => $t !== 'migrations'));
sort($dumpTableSet, SORT_STRING);

$invTables = array_keys($inventory['tables'] ?? []);
sort($invTables, SORT_STRING);
if ($dumpTableSet !== $invTables) {
    $errors[] = 'Inventory tables differ from dump CREATE TABLE list (excluding migrations).';
    $errors[] = 'Dump only: ' . implode(', ', array_diff($dumpTableSet, $invTables));
    $errors[] = 'Inventory only: ' . implode(', ', array_diff($invTables, $dumpTableSet));
}

preg_match_all('/CREATE TABLE\s+`([^`]+)`/i', $baseline, $baseTables);
$baseTableSet = $baseTables[1];
sort($baseTableSet, SORT_STRING);
if ($baseTableSet !== $dumpTableSet) {
    $errors[] = 'Baseline SQL tables differ from dump.';
}

preg_match_all('/ADD CONSTRAINT\s+`([^`]+)`\s+FOREIGN KEY/i', $dump, $dumpFks);
preg_match_all('/ADD CONSTRAINT\s+`([^`]+)`\s+FOREIGN KEY/i', $baseline, $baseFks);
sort($dumpFks[1], SORT_STRING);
sort($baseFks[1], SORT_STRING);
if ($dumpFks[1] !== $baseFks[1]) {
    $errors[] = 'Foreign key constraints differ between dump and baseline.';
}

if (strpos($dump, "enum('pending','approved','rejected')") !== false
    && strpos($baseline, "enum('pending','approved','rejected')") === false) {
    $errors[] = 'irs_refunds.status ENUM missing from baseline.';
}

// Static: every dump table CREATE must appear in baseline with same column names
foreach ($dumpTableSet as $table) {
    if (!preg_match('/CREATE TABLE\s+`' . preg_quote($table, '/') . '`\s*\((.*?)\)\s*ENGINE=/is', $dump, $m)) {
        $errors[] = "Could not parse dump CREATE for `{$table}`";
        continue;
    }
    if (!preg_match('/CREATE TABLE\s+`' . preg_quote($table, '/') . '`\s*\((.*?)\)\s*ENGINE=/is', $baseline, $bm)) {
        $errors[] = "Baseline missing CREATE for `{$table}`";
        continue;
    }
    preg_match_all('/^\s*`([^`]+)`/m', $m[1], $dc);
    preg_match_all('/^\s*`([^`]+)`/m', $bm[1], $bc);
    sort($dc[1], SORT_STRING);
    sort($bc[1], SORT_STRING);
    if ($dc[1] !== $bc[1]) {
        $errors[] = "Column name set differs for `{$table}` in dump vs baseline CREATE";
        $errors[] = '  missing in baseline: ' . implode(', ', array_diff($dc[1], $bc[1]));
        $errors[] = '  extra in baseline: ' . implode(', ', array_diff($bc[1], $dc[1]));
    }
}

if ($errors) {
    fail_exit($errors);
}

echo "Static comparison PASSED (" . count($dumpTableSet) . " tables, " . count($dumpFks[1]) . " FKs).\n";

if (!$live) {
    echo "Skipped live DB apply (pass --live for information_schema deep compare).\n";
    exit(0);
}

if (!is_file($mysql)) {
    fail_exit(["mysql.exe not found at {$mysql}"]);
}

// Build schema-only dump reference (no INSERT data) — includes migrations table from dump;
// we still compare only non-migrations business tables.
$schemaOnly = $dump;
$schemaOnly = preg_replace('/^INSERT INTO .*?;\s*$/ims', '', $schemaOnly);
$schemaOnlyPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'gc_dump_schema_only.sql';
file_put_contents($schemaOnlyPath, $schemaOnly);

$refDb = 'grand_chase_dump_ref';
$verifyDb = 'grand_chase_baseline_verify';

mysql_e($mysql, "DROP DATABASE IF EXISTS `{$refDb}`; DROP DATABASE IF EXISTS `{$verifyDb}`;");
[$code, $out, $err] = mysql_e($mysql, "CREATE DATABASE `{$refDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE DATABASE `{$verifyDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
if ($code !== 0) {
    fail_exit(["Could not create compare DBs: {$err} {$out}"]);
}

[$code, $out, $err] = mysql_file($mysql, $refDb, $schemaOnlyPath);
@unlink($schemaOnlyPath);
if ($code !== 0) {
    mysql_e($mysql, "DROP DATABASE IF EXISTS `{$refDb}`; DROP DATABASE IF EXISTS `{$verifyDb}`;");
    fail_exit(["Importing dump schema-only failed: {$err} {$out}"]);
}

[$code, $out, $err] = mysql_file($mysql, $verifyDb, $baselinePath);
if ($code !== 0) {
    mysql_e($mysql, "DROP DATABASE IF EXISTS `{$refDb}`; DROP DATABASE IF EXISTS `{$verifyDb}`;");
    fail_exit(["Applying baseline SQL failed: {$err} {$out}"]);
}

$loadCols = function (string $dbName) use ($mysql) {
    [$code, $out, $err] = mysql_e($mysql, "SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA
FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='{$dbName}' AND TABLE_NAME != 'migrations'
ORDER BY TABLE_NAME, ORDINAL_POSITION;");
    $map = [];
    foreach (parse_tsv_map($out, 5) as $p) {
        $map[$p[0]][$p[1]] = [
            'type' => normalize_type($p[2]),
            'nullable' => strtoupper($p[3]),
            'default' => normalize_default(($p[4] ?? null) === 'NULL' ? null : ($p[4] ?? null)),
            'extra' => strtolower(trim($p[5] ?? '')),
        ];
    }
    return $map;
};

$refCols = $loadCols($refDb);
$verCols = $loadCols($verifyDb);

$refTables = array_keys($refCols);
$verTables = array_keys($verCols);
sort($refTables, SORT_STRING);
sort($verTables, SORT_STRING);
// Dump ref includes migrations table filtered out already in loadCols.
if ($refTables !== $dumpTableSet) {
    $errors[] = 'Dump-ref tables differ from expected dump table set.';
    $errors[] = 'Missing: ' . implode(', ', array_diff($dumpTableSet, $refTables));
    $errors[] = 'Extra: ' . implode(', ', array_diff($refTables, $dumpTableSet));
}
if ($verTables !== $dumpTableSet) {
    $errors[] = 'Baseline-verify tables differ from dump table set.';
    $errors[] = 'Missing: ' . implode(', ', array_diff($dumpTableSet, $verTables));
    $errors[] = 'Extra: ' . implode(', ', array_diff($verTables, $dumpTableSet));
}

foreach ($dumpTableSet as $table) {
    $r = $refCols[$table] ?? [];
    $v = $verCols[$table] ?? [];
    $rNames = array_keys($r);
    $vNames = array_keys($v);
    sort($rNames, SORT_STRING);
    sort($vNames, SORT_STRING);
    if ($rNames !== $vNames) {
        $errors[] = "Column names differ for `{$table}`";
        $errors[] = '  missing in baseline: ' . implode(', ', array_diff($rNames, $vNames));
        $errors[] = '  extra in baseline: ' . implode(', ', array_diff($vNames, $rNames));
        continue;
    }
    foreach ($rNames as $col) {
        foreach (['type', 'nullable', 'default', 'extra'] as $attr) {
            // AUTO_INCREMENT presence may differ if dump ALTER set AI and baseline MODIFY kept it — both should have it.
            if (($r[$col][$attr] ?? null) !== ($v[$col][$attr] ?? null)) {
                // Ignore default quirks on timestamps where both are effectively null/current_timestamp variants already normalized.
                $errors[] = "`{$table}`.`{$col}`.{$attr} dump-ref={$r[$col][$attr]} baseline={$v[$col][$attr]}";
            }
        }
    }
}

$loadIndexes = function (string $dbName) use ($mysql) {
    [$code, $out, $err] = mysql_e($mysql, "SELECT TABLE_NAME, INDEX_NAME, NON_UNIQUE, GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS cols
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA='{$dbName}' AND TABLE_NAME != 'migrations'
GROUP BY TABLE_NAME, INDEX_NAME, NON_UNIQUE
ORDER BY TABLE_NAME, INDEX_NAME;");
    $map = [];
    foreach (parse_tsv_map($out, 4) as $p) {
        $map[$p[0]][$p[1]] = [
            'non_unique' => $p[2],
            'cols' => $p[3],
        ];
    }
    return $map;
};

$refIdx = $loadIndexes($refDb);
$verIdx = $loadIndexes($verifyDb);
foreach ($dumpTableSet as $table) {
    $r = $refIdx[$table] ?? [];
    $v = $verIdx[$table] ?? [];
    $rNames = array_keys($r);
    $vNames = array_keys($v);
    sort($rNames, SORT_STRING);
    sort($vNames, SORT_STRING);
    if ($rNames !== $vNames) {
        $errors[] = "Indexes differ for `{$table}`";
        $errors[] = '  missing in baseline: ' . implode(', ', array_diff($rNames, $vNames));
        $errors[] = '  extra in baseline: ' . implode(', ', array_diff($vNames, $rNames));
        continue;
    }
    foreach ($rNames as $idx) {
        if (($r[$idx]['cols'] ?? null) !== ($v[$idx]['cols'] ?? null) || ($r[$idx]['non_unique'] ?? null) !== ($v[$idx]['non_unique'] ?? null)) {
            $errors[] = "`{$table}` index `{$idx}` dump-ref={$r[$idx]['non_unique']}:{$r[$idx]['cols']} baseline={$v[$idx]['non_unique']}:{$v[$idx]['cols']}";
        }
    }
}

$loadFks = function (string $dbName) use ($mysql) {
    [$code, $out, $err] = mysql_e($mysql, "SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA='{$dbName}' AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY CONSTRAINT_NAME, ORDINAL_POSITION;");
    $map = [];
    foreach (parse_tsv_map($out, 5) as $p) {
        $map[$p[0]] = "{$p[1]}.{$p[2]}->{$p[3]}.{$p[4]}";
    }
    ksort($map);
    return $map;
};

$refFks = $loadFks($refDb);
$verFks = $loadFks($verifyDb);
if ($refFks !== $verFks) {
    $errors[] = 'Foreign keys differ between dump-ref and baseline-verify.';
    foreach (array_keys($refFks + $verFks) as $name) {
        if (($refFks[$name] ?? null) !== ($verFks[$name] ?? null)) {
            $errors[] = "  {$name}: dump-ref=" . ($refFks[$name] ?? 'missing') . ' baseline=' . ($verFks[$name] ?? 'missing');
        }
    }
}

mysql_e($mysql, "DROP DATABASE IF EXISTS `{$refDb}`; DROP DATABASE IF EXISTS `{$verifyDb}`;");

if ($errors) {
    fail_exit($errors);
}

echo "Live deep information_schema comparison PASSED (dump-ref vs baseline-verify).\n";
echo "FKs verified: " . count($refFks) . "\n";
exit(0);
