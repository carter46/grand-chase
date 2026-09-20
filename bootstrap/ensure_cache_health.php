<?php

/**
 * Permanent multi-site boot heal (vendor-in-git / Hostinger).
 *
 * After Git pull, hosts often keep a leftover empty or corrupt
 * bootstrap/cache/packages.php that Git will not overwrite. That breaks
 * package discovery and surfaces as Class "translator" does not exist
 * when Laravel tries to render an error page.
 *
 * This runs on every web/CLI boot: removes bad cache files and rebuilds
 * the package manifest from vendor/ (no Composer required).
 */

$basePath = dirname(__DIR__);
$cachePath = __DIR__ . DIRECTORY_SEPARATOR . 'cache';

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

    // Empty manifest / services array written by a failed deploy hook.
    if (preg_match('/return\s*(?:\[\s*\]|array\s*\(\s*\))\s*;/', $contents)) {
        return true;
    }

    return false;
};

$removedPackages = false;

$packagesFile = $cachePath . DIRECTORY_SEPARATOR . 'packages.php';
if ($isBadPhpCache($packagesFile, 80)) {
    @unlink($packagesFile);
    $removedPackages = true;
}

$servicesFile = $cachePath . DIRECTORY_SEPARATOR . 'services.php';
if ($isBadPhpCache($servicesFile, 80)) {
    @unlink($servicesFile);
}

// Compiled config from another clone/host often ships broken bindings.
if ($removedPackages) {
    foreach (['config.php', 'routes.php', 'routes-v7.php'] as $extra) {
        $extraPath = $cachePath . DIRECTORY_SEPARATOR . $extra;
        if (is_file($extraPath)) {
            @unlink($extraPath);
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
        // Next request / Laravel boot may retry; do not block startup.
    }
}
