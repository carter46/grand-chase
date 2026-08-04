<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

if (! function_exists('public_storage_url')) {
    /**
     * Public URL for uploads (logo, KYC, deposits, etc.).
     * Prefers real files under public/ (Hostinger-safe), then /storage symlink,
     * and mirrors from the public disk into public/uploads when needed.
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

        if ($path === '' || str_contains($path, '..')) {
            return '';
        }

        // Already stored directly under public/ (e.g. uploads/branding/logo.png)
        if (is_file(public_path($path))) {
            return asset($path);
        }

        // Live via public/storage symlink
        if (is_file(public_path('storage/'.$path))) {
            return asset('storage/'.$path);
        }

        // Mirror from storage/app/public → public/uploads so Hostinger can serve without symlink
        if (Storage::disk('public')->exists($path)) {
            $mirrorRel = 'uploads/'.$path;
            $mirrorAbs = public_path($mirrorRel);
            $mirrorDir = dirname($mirrorAbs);

            if (! is_dir($mirrorDir)) {
                File::makeDirectory($mirrorDir, 0755, true);
            }

            if (! is_file($mirrorAbs)) {
                @copy(Storage::disk('public')->path($path), $mirrorAbs);
            }

            if (is_file($mirrorAbs)) {
                return asset($mirrorRel);
            }

            return asset('storage/'.$path);
        }

        // Last resort (may 404 if symlink missing)
        return asset('storage/'.$path);
    }
}

if (! function_exists('public_storage_path')) {
    /**
     * Absolute filesystem path for mail embed, PDF, etc.
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

        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        foreach ([public_path($path), public_path('uploads/'.$path), public_path('storage/'.$path)] as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
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

if (! function_exists('store_public_upload')) {
    /**
     * Store an uploaded file under public/uploads (works without storage:link).
     * Returns relative path like "uploads/branding/logo-xxx.png".
     */
    function store_public_upload(\Illuminate\Http\UploadedFile $file, string $folder = 'branding', ?string $basename = null): string
    {
        $folder = trim($folder, '/');
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'png');
        $name = ($basename ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $name = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $name) ?: 'file';
        $filename = $name.'-'.time().'-'.bin2hex(random_bytes(3)).'.'.$ext;

        $dir = public_path('uploads/'.$folder);
        if (! is_dir($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $file->move($dir, $filename);

        return 'uploads/'.$folder.'/'.$filename;
    }
}

if (! function_exists('delete_public_upload')) {
    /**
     * Delete a previously stored public upload or public-disk file.
     */
    function delete_public_upload(?string $path): void
    {
        if ($path === null || trim($path) === '') {
            return;
        }

        $path = trim(str_replace('\\', '/', $path));

        if (preg_match('#^https?://#i', $path) || str_contains($path, '..')) {
            return;
        }

        $path = ltrim($path, '/');
        $path = preg_replace('#^(storage/app/public/|storage/|public/)#', '', $path);

        foreach ([public_path($path), public_path('uploads/'.$path), public_path('storage/'.$path)] as $candidate) {
            if (is_file($candidate)) {
                @unlink($candidate);
            }
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
