<?php
/**
 * Create Laravel storage folders (not in Git) + clear bad config cache.
 *
 * Upload next to artisan, open once:
 *   https://fsmtrustfinance.com/fix_storage_dirs.php
 * Or: php fix_storage_dirs.php
 * Then DELETE this file.
 */

$root = __DIR__;
header('Content-Type: text/plain; charset=utf-8');

$dirs = [
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/testing',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
];

echo "Creating storage folders under:\n{$root}\n\n";

foreach ($dirs as $rel) {
    $dir = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
    if (is_dir($dir)) {
        @chmod($dir, 0775);
        echo "[ok] exists  {$rel}\n";
        continue;
    }
    if (@mkdir($dir, 0775, true)) {
        @chmod($dir, 0775);
        echo "[++] created {$rel}\n";
    } else {
        echo "[!!] FAILED  {$rel}\n";
    }
}

$views = $root . '/storage/framework/views';
$okWrite = false;
if (is_dir($views)) {
    $t = $views . '/.__w';
    $okWrite = @file_put_contents($t, '1') !== false;
    if ($okWrite) {
        @unlink($t);
    }
}
echo "\nviews writable: " . ($okWrite ? 'YES' : 'NO — set chmod 775 on storage/') . "\n";

foreach ([
    'bootstrap/cache/config.php',
    'bootstrap/cache/services.php',
    'bootstrap/cache/packages.php',
    'bootstrap/cache/routes.php',
    'bootstrap/cache/routes-v7.php',
] as $rel) {
    $f = $root . '/' . $rel;
    if (is_file($f) && @unlink($f)) {
        echo "deleted {$rel}\n";
    }
}

echo "\nDone. Reload the homepage, then DELETE fix_storage_dirs.php\n";
