<?php
/**
 * Fix: "Please provide a valid cache path" (HTTP 500)
 *
 * Causes:
 * 1) Missing storage/framework/views (and related dirs)
 * 2) Stale bootstrap/cache/config.php that cached realpath() === false
 *
 * Place next to artisan. Run: php fix_storage_dirs.php
 * Or visit once in browser, then DELETE this file.
 */

$root = __DIR__;

header('Content-Type: text/plain; charset=utf-8');

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

$views = $root . '/storage/framework/views';
$probe = $views . '/.write_test';
$writable = false;
if (is_dir($views)) {
    $writable = @file_put_contents($probe, 'ok') !== false;
    if ($writable) {
        @unlink($probe);
    }
}

// Wipe cached config — this is why folders alone often don't fix the 500
$cacheFiles = [
    $root . '/bootstrap/cache/config.php',
    $root . '/bootstrap/cache/routes.php',
    $root . '/bootstrap/cache/routes-v7.php',
    $root . '/bootstrap/cache/services.php',
    $root . '/bootstrap/cache/packages.php',
];
$deleted = [];
foreach ($cacheFiles as $file) {
    if (is_file($file) && @unlink($file)) {
        $deleted[] = basename($file);
    }
}

echo "Laravel storage + config cache fix\n";
echo "Root: {$root}\n\n";

echo 'Created (' . count($created) . "):\n";
foreach ($created as $d) {
    echo '  + ' . str_replace($root . '/', '', $d) . "\n";
}

echo "\nAlready existed (" . count($existed) . "):\n";
foreach ($existed as $d) {
    echo '  = ' . str_replace($root . '/', '', $d) . "\n";
}

echo "\nDeleted bootstrap cache files: " . (empty($deleted) ? '(none found)' : implode(', ', $deleted)) . "\n";

echo "\nChecks:\n";
echo '  storage/framework/views exists: ' . (is_dir($views) ? 'YES' : 'NO') . "\n";
echo '  storage/framework/views writable: ' . ($writable ? 'YES' : 'NO') . "\n";
echo '  realpath(views): ' . var_export(realpath($views), true) . "\n";

if ($failed) {
    echo "\nFAILED to create:\n";
    foreach ($failed as $d) {
        echo "  ! {$d}\n";
    }
    echo "\nCreate those folders in File Manager, then run this script again.\n";
    exit(1);
}

if (!$writable) {
    echo "\nWARNING: views folder is not writable. In File Manager set storage/ and storage/framework/views to 775.\n";
    exit(1);
}

echo "\nOK. Reload the site now.\n";
echo "DELETE this file when done: fix_storage_dirs.php\n";
echo "Also pull/update config/view.php if you have not already (avoids realpath false).\n";
