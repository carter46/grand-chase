<?php
/**
 * Extract schema-only SQL from the authoritative dump.
 * Source of truth: database/u502532383_uscounty.sql
 *
 * This generates the baseline artifact from the exported database schema
 * (CREATE/ALTER for structure only — no INSERT data).
 *
 * Usage: php database/scripts/extract_schema_from_dump.php
 */

$root = dirname(__DIR__, 2);
$dumpPath = $root . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'u502532383_uscounty.sql';
$outDir = $root . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'schema';
$outSql = $outDir . DIRECTORY_SEPARATOR . 'baseline_u502532383_uscounty.sql';
$outJson = $outDir . DIRECTORY_SEPARATOR . 'baseline_inventory.json';

if (!is_file($dumpPath)) {
    fwrite(STDERR, "Dump not found: {$dumpPath}\n");
    exit(1);
}

if (!is_dir($outDir)) {
    mkdir($outDir, 0777, true);
}

$sql = file_get_contents($dumpPath);
// Normalize line endings
$sql = str_replace(["\r\n", "\r"], "\n", $sql);

$statements = [];
$inventory = [
    'source' => 'database/u502532383_uscounty.sql',
    'generated_at' => gmdate('c'),
    'tables' => [],
];

// Match CREATE TABLE ... ; blocks (non-greedy until next semicolon at end of statement)
if (!preg_match_all('/CREATE TABLE\s+`([^`]+)`\s*\((.*?)\)\s*ENGINE=([^;]+);/is', $sql, $creates, PREG_SET_ORDER)) {
    fwrite(STDERR, "No CREATE TABLE statements found.\n");
    exit(1);
}

foreach ($creates as $match) {
    $table = $match[1];
    // Laravel manages the migrations table itself — exclude from baseline schema.
    if ($table === 'migrations') {
        continue;
    }
    $full = "CREATE TABLE `{$table}` ({$match[2]}) ENGINE={$match[3]};";
    $statements[] = $full;
    $inventory['tables'][$table] = [
        'create' => true,
        'engine' => trim(explode(' ', $match[3])[0]),
    ];
}

// Capture ALTER TABLE ... ADD PRIMARY KEY / KEY / CONSTRAINT (structure only)
if (preg_match_all('/ALTER TABLE\s+`([^`]+)`\s+(.*?);/is', $sql, $alters, PREG_SET_ORDER)) {
    foreach ($alters as $match) {
        $table = $match[1];
        if ($table === 'migrations') {
            continue;
        }
        $body = trim($match[2]);
        // Strip dump-specific AUTO_INCREMENT counters (data state, not schema shape).
        $body = preg_replace('/,?\s*AUTO_INCREMENT\s*=\s*\d+/i', '', $body);
        $body = rtrim(trim($body), ',');
        if ($body === '') {
            continue;
        }
        if (preg_match('/^(ADD|MODIFY|CHANGE|DROP)/i', $body)) {
            $statements[] = "ALTER TABLE `{$table}` {$body};";
            $inventory['tables'][$table]['alters'][] = $body;
        }
    }
}

$header = <<<SQL
-- Schema-only baseline generated from u502532383_uscounty.sql
-- Generated at: {$inventory['generated_at']}
-- DO NOT hand-edit; re-run: php database/scripts/extract_schema_from_dump.php
-- Contains CREATE TABLE + structural ALTER TABLE only (no INSERT data).

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

SQL;

$footer = "\nSET FOREIGN_KEY_CHECKS = 1;\n";

file_put_contents($outSql, $header . "\n" . implode("\n\n", $statements) . "\n" . $footer);
file_put_contents($outJson, json_encode($inventory, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

echo "Wrote {$outSql}\n";
echo "Wrote {$outJson}\n";
echo "Tables: " . count($inventory['tables']) . "\n";
echo "Statements: " . count($statements) . "\n";
