<?php
/**
 * Fix: "Please provide a valid cache path" (HTTP 500)
 * Creates missing Laravel storage / bootstrap cache directories.
 *
 * Upload to site root (same folder as artisan), then run:
 *   php fix_storage_dirs.php
 * Or open in browser once if PHP CLI is awkward, then DELETE this file.
 */

$root = __DIR__;

$dirs = [
    $root . '/storage',
    $root . '/storage/app',
    $root . '/storage/app/public',
    $root . '/storage/framework',
    $root . '/storage/framework/cache',
    $root . '/storage/framework/cache/data',
    $root . '/storage/framework/sessions',
    $root . '/storage/framework/testing',
    $root . '/storage/framework/views',
    $root . '/storage/logs',
    $root . '/bootstrap/cache',
];

$created = [];
$existed = [];
$failed = [];

foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $existed[] = $dir;
        @chmod($dir, 0775);
        continue;
    }
    if (@mkdir($dir, 0775, true)) {
        $created[] = $dir;
        @chmod($dir, 0775);
    } else {
        $failed[] = $dir;
    }
}

// Empty placeholders so dirs stay writable / tracked if needed
$keeps = [
    $root . '/storage/framework/views/.gitignore' => "*\n!.gitignore\n",
    $root . '/storage/framework/cache/.gitignore' => "*\n!data/\n!.gitignore\n",
    $root . '/storage/framework/cache/data/.gitignore' => "*\n!.gitignore\n",
    $root . '/storage/framework/sessions/.gitignore' => "*\n!.gitignore\n",
    $root . '/storage/logs/.gitignore' => "*\n!.gitignore\n",
];

foreach ($keeps as $file => $contents) {
    if (!is_file($file)) {
        @file_put_contents($file, $contents);
    }
}

header('Content-Type: text/plain; charset=utf-8');
echo "Laravel storage fix\n";
echo "Root: {$root}\n\n";
echo 'Created (' . count($created) . "):\n";
foreach ($created as $d) {
    echo "  + {$d}\n";
}
echo "\nAlready existed (" . count($existed) . "):\n";
foreach ($existed as $d) {
    echo "  = {$d}\n";
}
if ($failed) {
    echo "\nFAILED (" . count($failed) . ") — create these manually in File Manager:\n";
    foreach ($failed as $d) {
        echo "  ! {$d}\n";
    }
    exit(1);
}

echo "\nOK. Reload https://fsmtrustfinance.com/\n";
echo "Then DELETE this file: fix_storage_dirs.php\n";
