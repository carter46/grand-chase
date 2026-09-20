<?php
/**
 * Hostinger / vendor-in-git repair — NO composer install/update.
 *
 * Fixes empty/corrupt bootstrap/cache/packages.php + services.php
 * and clears compiled views that mask the real error as "translator".
 *
 * Usage (SSH, from site root next to artisan):
 *   php repair_laravel_cache.php
 *
 * Or open once in browser if public_html IS the Laravel root, then DELETE this file.
 * If document root is /public, run via SSH only (do not put this in public/).
 */

$root = __DIR__;
if (!is_file($root . '/artisan') || !is_dir($root . '/vendor')) {
    fwrite(STDERR, "Run this from the Laravel root (folder that contains artisan + vendor).\n");
    exit(1);
}

require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cacheDir = $root . '/bootstrap/cache';
$viewDir = $root . '/storage/framework/views';

foreach (['packages.php', 'services.php', 'config.php', 'routes-v7.php', 'routes.php'] as $file) {
    $path = $cacheDir . '/' . $file;
    if (is_file($path)) {
        @unlink($path);
        echo "Removed bootstrap/cache/{$file}\n";
    }
}

if (is_dir($viewDir)) {
    $n = 0;
    foreach (glob($viewDir . '/*.php') ?: [] as $f) {
        if (@unlink($f)) {
            $n++;
        }
    }
    echo "Cleared {$n} compiled views\n";
}

passthru('php "' . $root . '/artisan" package:discover --ansi', $discoverCode);
passthru('php "' . $root . '/artisan" config:clear', $c1);
passthru('php "' . $root . '/artisan" cache:clear', $c2);
passthru('php "' . $root . '/artisan" view:clear', $c3);
passthru('php "' . $root . '/artisan" route:clear', $c4);

$packages = $cacheDir . '/packages.php';
if (!is_file($packages) || filesize($packages) < 20) {
    echo "FAIL: packages.php was not rebuilt. Check vendor/ and PHP errors above.\n";
    exit(1);
}

echo "OK: packages.php rebuilt (" . filesize($packages) . " bytes). Site should load; check storage/logs/laravel.log for any remaining real error.\n";
echo "Delete this file when done: repair_laravel_cache.php\n";
exit(0);
