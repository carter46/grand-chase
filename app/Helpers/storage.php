<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('public_storage_url')) {
    /**
     * Public disk URL for uploads (logo, KYC, deposits, etc.).
     * Files live in storage/app/public and are served via public/storage → /storage/...
     */
    function public_storage_url(?string $path): string
    {
        if ($path === null || trim($path) === '') {
            return '';
        }

        $path = trim(str_replace('\\', '/', $path));

        // Already an absolute URL (external CDN, etc.)
        if (preg_match('#^https?://#i', $path) || str_starts_with($path, '//')) {
            return $path;
        }

        $path = ltrim($path, '/');
        $path = preg_replace('#^(storage/app/public/|storage/|public/)#', '', $path);

        if ($path === '') {
            return '';
        }

        return asset('storage/'.$path);
    }
}

if (! function_exists('public_storage_path')) {
    /**
     * Absolute filesystem path on the public disk (for mail embed, PDF, etc.).
     */
    function public_storage_path(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim(str_replace('\\', '/', $path));

        if (preg_match('#^https?://#i', $path) || str_starts_with($path, '//')) {
            return null;
        }

        $path = ltrim($path, '/');
        $path = preg_replace('#^(storage/app/public/|storage/|public/)#', '', $path);

        if ($path === '') {
            return null;
        }

        $full = Storage::disk('public')->path($path);

        return is_file($full) ? $full : null;
    }
}

if (! function_exists('profile_photo_url')) {
    /**
     * Profile photo URL. Supports legacy filename-only DB values.
     * Falls back to a generated avatar when empty (avoids broken <img>).
     */
    function profile_photo_url(?string $path, ?string $name = 'User'): string
    {
        if ($path === null || trim($path) === '') {
            $label = trim((string) $name) !== '' ? $name : 'User';

            return 'https://ui-avatars.com/api/?name='.urlencode($label).'&color=FFFFFF&background=AC2E00';
        }

        $path = trim(str_replace('\\', '/', $path));

        if (preg_match('#^https?://#i', $path) || str_starts_with($path, '//')) {
            return $path;
        }

        $path = ltrim($path, '/');
        $path = preg_replace('#^(storage/app/public/|storage/|public/)#', '', $path);

        if (! str_contains($path, '/')) {
            $path = 'photos/'.$path;
        }

        return public_storage_url($path);
    }
}
