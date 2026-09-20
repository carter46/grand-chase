<?php
/**
 * ONE-CLICK Hostinger fix (no Composer). Upload to site root (next to artisan)
 * OR to /public, open in browser once, then DELETE this file.
 *
 * Fixes: empty packages.php, stale compiled views with __(), bad 500.blade.php
 * that causes Class "translator" does not exist.
 */

header('Content-Type: text/plain; charset=UTF-8');

$candidates = [
    __DIR__,
    dirname(__DIR__),
];

$root = null;
foreach ($candidates as $dir) {
    if (is_file($dir . '/artisan') && is_dir($dir . '/vendor') && is_dir($dir . '/bootstrap')) {
        $root = $dir;
        break;
    }
}

if ($root === null) {
    http_response_code(500);
    echo "FAIL: Could not find Laravel root (artisan + vendor + bootstrap).\n";
    exit;
}

echo "Laravel root: {$root}\n";

$deleted = [];

$cacheDir = $root . '/bootstrap/cache';
foreach (glob($cacheDir . '/*.php') ?: [] as $file) {
    if (@unlink($file)) {
        $deleted[] = 'cache:' . basename($file);
    }
}

$viewDir = $root . '/storage/framework/views';
$viewCount = 0;
foreach (glob($viewDir . '/*.php') ?: [] as $file) {
    if (@unlink($file)) {
        $viewCount++;
    }
}
echo "Cleared {$viewCount} compiled views\n";

$safe500 = <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error</title>
</head>
<body style="margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:sans-serif;">
    <div style="text-align:center;padding:24px;">
        <p style="font-size:48px;margin:0 0 8px;color:#64748b;">500</p>
        <p style="margin:0;color:#475569;">An unexpected error occurred. Please try again in a moment.</p>
    </div>
</body>
</html>
HTML;

$errorsDir = $root . '/resources/views/errors';
if (! is_dir($errorsDir)) {
    @mkdir($errorsDir, 0755, true);
}
@file_put_contents($errorsDir . '/500.blade.php', $safe500);
@file_put_contents($errorsDir . '/404.blade.php', str_replace(['500', 'An unexpected error occurred. Please try again in a moment.'], ['404', 'We could not find the page you were looking for.'], $safe500));
echo "Wrote safe errors/500.blade.php and 404.blade.php\n";

// Rebuild packages via artisan if possible
$autoload = $root . '/vendor/autoload.php';
if (is_file($autoload)) {
    try {
        require $autoload;
        if (class_exists(\Illuminate\Foundation\PackageManifest::class)) {
            $manifest = new \Illuminate\Foundation\PackageManifest(
                new \Illuminate\Filesystem\Filesystem(),
                $root,
                $cacheDir . '/packages.php'
            );
            $manifest->build();
            echo "Rebuilt bootstrap/cache/packages.php (" . (is_file($cacheDir . '/packages.php') ? filesize($cacheDir . '/packages.php') : 0) . " bytes)\n";
        }
    } catch (Throwable $e) {
        echo "PackageManifest rebuild warning: " . $e->getMessage() . "\n";
    }
}

echo "Deleted: " . (count($deleted) ? implode(', ', $deleted) : '(none)') . "\n";
echo "OK. Reload the homepage now, then DELETE fix-cache-now.php\n";
