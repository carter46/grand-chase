# Deployment on Hostinger (Git pull)

This project follows the same shared-hosting pattern as other Hostinger Laravel apps (e.g. 7th-trade-hub): **build/install locally, commit artifacts, pull on the server**. The server should not need to resolve Composer packages.

## Why Hostinger Git deploy “fails”

After every pull, Hostinger auto-runs **Composer** when it sees `composer.lock`. On shared hosting that often fails for two reasons:

1. **PHP version mismatch** — Laravel 8 lockfile expects ~PHP 8.1; Hostinger may use 8.2–8.4 → “requirements could not be resolved”.
2. **`proc_open` disabled** — Composer needs `proc_open`; Hostinger PHP often blocks it →  
   `The Process class relies on proc_open, which is not available on your PHP installation.`

**Important:** The Git pull of your code usually still succeeds. The red “Deployment failed” is often only the Composer step. If `vendor/` is **committed in Git**, the app can still run after pull even when Hostinger’s Composer step errors.

## Deployment workflow

1. **Local:** Edit code.
2. **Local (when PHP deps change):**  
   `composer install --no-dev --optimize-autoloader --ignore-platform-reqs`  
   then commit `vendor/` + `composer.lock` + `composer.json` yourself (e.g. GitHub Desktop).
3. **Local:** Push to GitHub.
4. **Server:** Hostinger Git → **Deploy / Pull**.
5. **Server:** Confirm `vendor/autoload.php` exists in File Manager (ignore Composer failure if vendor is present).

### Optional: stop Hostinger from running Composer

In hPanel Git / deployment settings, clear any post-deploy Composer / build command if the UI allows it. Otherwise keep committing `vendor/` and treat the Composer error as noise.

### PHP version on Hostinger

In hPanel → PHP Configuration, set the site to **PHP 8.1** (matches `composer.json` `config.platform.php`).

### Environment

- **Do not** commit `.env`.
- Create `.env` on the server (copy from `.env.example`).
- Set `APP_KEY`, `APP_URL`, DB_*, `APP_ENV=production`, `APP_DEBUG=false`.
- **Document root must be** `public_html/public` (the Laravel `public/` folder), not `public_html`.

### Storage symlink (required for uploads/logo)

After pull, over SSH from the project root:

```bash
php artisan storage:link
php artisan config:clear
```

This creates `public/storage` → `storage/app/public` so URLs like `/storage/photos/...` work.

Static marketing images live in `public/assets/images/` and are committed with the repo (no symlink needed).

### After first successful pull

If SSH/terminal is available (and `proc_open` works there — often it does not):

```bash
php artisan key:generate   # if APP_KEY empty
php artisan storage:link
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If there is no SSH: import DB via phpMyAdmin using your dump/baseline process, and create `.env` in File Manager. For the storage link without Artisan, create a symlink/junction in File Manager from `public/storage` to `../storage/app/public` if Hostinger allows it.

## Composer note

`vendor/` **is tracked** in Git for Hostinger (same as 7th-trade-hub). After changing dependencies locally, always re-run:

```bash
composer install --no-dev --optimize-autoloader --ignore-platform-reqs
```

Then commit the updated `vendor/` with the lockfile yourself.
