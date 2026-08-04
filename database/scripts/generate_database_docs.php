<?php
/**
 * Generate docs/database/*.md from baseline inventory + dump CREATE bodies.
 * Usage: php database/scripts/generate_database_docs.php
 */

$root = dirname(__DIR__, 2);
$dumpPath = $root . '/database/u502532383_uscounty.sql';
$inventoryPath = $root . '/database/schema/baseline_inventory.json';
$docsDir = $root . '/docs/database';

$dump = str_replace(["\r\n", "\r"], "\n", file_get_contents($dumpPath));
$inventory = json_decode(file_get_contents($inventoryPath), true);
$tables = array_keys($inventory['tables']);
sort($tables, SORT_STRING);

if (!is_dir($docsDir)) {
    mkdir($docsDir, 0777, true);
}

$tablesMd = ["# Database tables\n", "Generated from `database/u502532383_uscounty.sql` baseline inventory.\n", "Do not treat as a production data dump — schema documentation only.\n"];
$indexesMd = ["# Indexes\n", "Primary, unique, and secondary indexes from the authoritative dump.\n"];
$enumsMd = ["# Enums\n"];
$relsMd = ["# Relationships\n", "Declared foreign keys and logical (application-level) relationships.\n"];

$declaredFks = [];
if (preg_match_all('/ALTER TABLE\s+`([^`]+)`\s+ADD CONSTRAINT\s+`([^`]+)`\s+FOREIGN KEY\s+\(`([^`]+)`\)\s+REFERENCES\s+`([^`]+)`\s+\(`([^`]+)`\)([^;]*);/is', $dump, $fks, PREG_SET_ORDER)) {
    foreach ($fks as $fk) {
        $declaredFks[] = [
            'table' => $fk[1],
            'name' => $fk[2],
            'column' => $fk[3],
            'ref_table' => $fk[4],
            'ref_column' => $fk[5],
            'extras' => trim($fk[6]),
        ];
    }
}

$relsMd[] = "## Declared foreign keys\n";
if (!$declaredFks) {
    $relsMd[] = "_None._\n";
} else {
    $relsMd[] = "| From | Column | To | On delete/update |\n|------|--------|----|------------------|\n";
    foreach ($declaredFks as $fk) {
        $relsMd[] = "| `{$fk['table']}` (`{$fk['name']}`) | `{$fk['column']}` | `{$fk['ref_table']}`.`{$fk['ref_column']}` | {$fk['extras']} |\n";
    }
}

$relsMd[] = "\n## Logical relationships (no DB FK)\n";
$relsMd[] = "Most domain tables store user/plan ids as integers without constraints:\n\n";
$relsMd[] = "- `deposits.user`, `withdrawals.user`, `tp__transactions.user` → `users.id`\n";
$relsMd[] = "- `user_plans.user` → `users.id`; `user_plans.plan` → `plans.id`\n";
$relsMd[] = "- `kycs.user_id`, `notifications.user_id`, `irs_refunds.user_id`, `bnc_transactions.user_id` → `users.id`\n";
$relsMd[] = "- `crypto_accounts.user_id` → `users.id`\n";
$relsMd[] = "- `agents.agent` → `users.id`\n";

$enumFound = false;
foreach ($tables as $table) {
    if (!preg_match('/CREATE TABLE\s+`' . preg_quote($table, '/') . '`\s*\((.*?)\)\s*ENGINE=([^;]+);/is', $dump, $m)) {
        continue;
    }
    $body = $m[1];
    $engine = trim($m[2]);
    $tablesMd[] = "\n## `{$table}`\n";
    $tablesMd[] = "Engine/charset: `{$engine}`\n\n";
    $tablesMd[] = "| Column | Definition |\n|--------|------------|\n";

    foreach (preg_split('/\n/', $body) as $line) {
        $line = trim($line, " \t\n\r\0\x0B,");
        if ($line === '' || !str_starts_with($line, '`')) {
            continue;
        }
        if (!preg_match('/^`([^`]+)`\s+(.+)$/', $line, $cm)) {
            continue;
        }
        $col = $cm[1];
        $def = str_replace('|', '\\|', $cm[2]);
        $tablesMd[] = "| `{$col}` | {$def} |\n";
        if (preg_match('/\benum\s*\((.*?)\)/i', $cm[2], $em)) {
            $enumFound = true;
            $enumsMd[] = "- `{$table}.{$col}` → enum({$em[1]})\n";
        }
    }

    $alters = $inventory['tables'][$table]['alters'] ?? [];
    $indexLines = [];
    foreach ($alters as $alter) {
        if (preg_match_all('/\b(ADD PRIMARY KEY|ADD UNIQUE KEY|ADD KEY|ADD CONSTRAINT)\s+([^,]+)/i', $alter, $im, PREG_SET_ORDER)) {
            foreach ($im as $idx) {
                $indexLines[] = trim($idx[1] . ' ' . $idx[2]);
            }
        } elseif (stripos($alter, 'ADD PRIMARY KEY') !== false || stripos($alter, 'ADD UNIQUE') !== false || stripos($alter, 'ADD KEY') !== false) {
            $indexLines[] = $alter;
        }
    }
    if ($indexLines) {
        $indexesMd[] = "\n## `{$table}`\n";
        foreach ($indexLines as $idx) {
            $indexesMd[] = "- {$idx}\n";
        }
    }
}

if (!$enumFound) {
    $enumsMd[] = "_No ENUM columns found._\n";
} else {
    array_splice($enumsMd, 1, 0, ["\n"]);
}

$readme = <<<MD
# Database documentation

- [POLICY.md](POLICY.md) — schema vs seed vs production data; migration rules
- [tables.md](tables.md) — all baseline tables and columns
- [relationships.md](relationships.md) — foreign keys and logical refs
- [indexes.md](indexes.md) — indexes and unique keys
- [enums.md](enums.md) — ENUM columns

## Regenerating

```bash
php database/scripts/extract_schema_from_dump.php
php database/scripts/verify_baseline_schema.php --live
php database/scripts/generate_database_docs.php
```

## Environments

| Environment | Schema | Data |
|-------------|--------|------|
| Local / CI fresh | `php artisan migrate` (baseline + later migrations) | Seeders only (dev config) |
| Production / staging restore | Import `database/u502532383_uscounty.sql` (or newer backup), then mark baseline migration as ran | Production data from dump |
| Disaster recovery | Restore backup/dump | Not `migrate:fresh --seed` |

MD;

file_put_contents($docsDir . '/README.md', $readme);
file_put_contents($docsDir . '/tables.md', implode('', $tablesMd));
file_put_contents($docsDir . '/indexes.md', implode('', $indexesMd));
file_put_contents($docsDir . '/enums.md', implode('', $enumsMd));
file_put_contents($docsDir . '/relationships.md', implode('', $relsMd));

echo "Wrote docs under {$docsDir}\n";
