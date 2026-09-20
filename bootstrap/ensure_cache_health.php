<?php

/**
 * Permanent multi-site boot heal (vendor-in-git / Hostinger).
 *
 * Leftover empty bootstrap/cache/*.php after git pull, plus stale compiled
 * Blade that calls __(), cause "Class translator does not exist" and a dead site.
 *
 * Runs on every web/CLI boot. No Composer required.
 */

$basePath = dirname(__DIR__);
$cachePath = __DIR__ . DIRECTORY_SEPARATOR . 'cache';
$viewPath = $basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views';
$errors500 = $basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views'
    . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . '500.blade.php';

if (! is_dir($cachePath)) {
    @mkdir($cachePath, 0755, true);
}

$isBadPhpCache = static function (string $path, int $minBytes): bool {
    if (! is_file($path)) {
        return false;
    }

    clearstatcache(true, $path);
    $size = @filesize($path);
    if ($size === false || $size < $minBytes) {
        return true;
    }

    $contents = @file_get_contents($path);
    if (! is_string($contents) || trim($contents) === '') {
        return true;
    }

    if (preg_match('/return\s*(?:\[\s*\]|array\s*\(\s*\))\s*;/', $contents)) {
        return true;
    }

    return false;
};

$safe500 = <<<'BLADE'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error</title>
    <style>
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #0f172a; }
        .box { max-width: 420px; padding: 24px; text-align: center; }
        .code { font-size: 48px; font-weight: 700; color: #64748b; margin: 0 0 8px; }
        p { margin: 0; font-size: 16px; line-height: 1.5; color: #475569; }
    </style>
</head>
<body>
    <div class="box">
        <p class="code">500</p>
        <p>An unexpected error occurred. Please try again in a moment.</p>
    </div>
</body>
</html>
BLADE;

// Replace any 500 Blade that still calls translator helpers / framework error layout.
if (! is_file($errors500) || ! is_string($cur = @file_get_contents($errors500))
    || strpos($cur, '__(') !== false
    || strpos($cur, 'errors::minimal') !== false
    || strpos($cur, '@lang') !== false
) {
    $dir = dirname($errors500);
    if (! is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    @file_put_contents($errors500, $safe500);
}

$removedPackages = false;

$packagesFile = $cachePath . DIRECTORY_SEPARATOR . 'packages.php';
if ($isBadPhpCache($packagesFile, 80)) {
    @unlink($packagesFile);
    $removedPackages = true;
}

$servicesFile = $cachePath . DIRECTORY_SEPARATOR . 'services.php';
if ($isBadPhpCache($servicesFile, 80)) {
    @unlink($servicesFile);
    $removedPackages = true;
}

foreach (['config.php', 'routes.php', 'routes-v7.php'] as $extra) {
    $extraPath = $cachePath . DIRECTORY_SEPARATOR . $extra;
    // Always drop compiled config if packages/services were bad; also drop tiny/corrupt files.
    if (($removedPackages && is_file($extraPath)) || $isBadPhpCache($extraPath, 80)) {
        @unlink($extraPath);
    }
}

// Drop stale compiled Blade that still calls __() / trans() (masks real errors as translator).
if (is_dir($viewPath)) {
    foreach (glob($viewPath . DIRECTORY_SEPARATOR . '*.php') ?: [] as $compiled) {
        $contents = @file_get_contents($compiled);
        if (! is_string($contents)) {
            continue;
        }
        if (strpos($contents, '__(') !== false
            || strpos($contents, 'trans(') !== false
            || strpos($contents, "app('translator'") !== false
        ) {
            @unlink($compiled);
        }
    }
}

// Rebuild package manifest from committed vendor (no composer install).
if (! is_file($packagesFile) && class_exists(\Illuminate\Foundation\PackageManifest::class)) {
    try {
        $manifest = new \Illuminate\Foundation\PackageManifest(
            new \Illuminate\Filesystem\Filesystem(),
            $basePath,
            $packagesFile
        );
        $manifest->build();
    } catch (Throwable $e) {
        // Next request may retry.
    }
}
