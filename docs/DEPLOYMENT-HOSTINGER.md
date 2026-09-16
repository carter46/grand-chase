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
- **Document root:** Prefer `public_html` (repo root) with the committed root `.htaccess`, which rewrites into `public/`. That avoids Hostinger 403 when `index.php` only exists under `public/`.
- Alternative: set document root to `public_html/public` if hPanel allows it and the site loads cleanly. If you get **403 Forbidden** after changing docroot, switch back to `public_html` and keep the root `.htaccess`.

### Storage symlink (required for uploads/logo)

After pull, over SSH from the project root:

```bash
# Prefer a manual link if artisan fails (symlink() often disabled on Hostinger):
ln -sfn ../storage/app/public public/storage
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

## After deploy: clear Laravel caches (fixes blank 500 / “translator does not exist”)

If a site returns HTTP 500 and the log mentions `Target class [translator] does not exist`, the real error is usually a broken `bootstrap/cache` (especially an empty `packages.php`). On each domain that uses this codebase:

1. Delete these if present: `bootstrap/cache/packages.php`, `bootstrap/cache/services.php`, `bootstrap/cache/config.php`, `bootstrap/cache/routes-*.php`
2. Ensure `bootstrap/cache` is writable (not read-only)
3. Run:
   ```bash
   php artisan package:discover
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```
4. Load any admin page once so Hub migrations can auto-apply for that site’s DB

Do **not** copy an empty `bootstrap/cache/packages.php` between sites.

## 7th Trade Hub cron (M9)

Owned subscription poll runs via Laravel scheduler every 10 minutes (`seventh-tradehub:poll` with `withoutOverlapping`). Hostinger must invoke Artisan every minute:

```cron
* * * * * php /home/USER/domains/YOUR_DOMAIN/public_html/artisan schedule:run >> /dev/null 2>&1
```

Adjust the path to this app’s `artisan`. Platform SA seed (`admin@demo.com`) is applied by migration on the next authenticated admin dashboard load (`admin.automigrate`). After go-live, rotate that seed password and run Hub MERCHANT-GO-LIVE smoke on the public HTTPS URL.

