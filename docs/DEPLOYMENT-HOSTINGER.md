# Deployment on Hostinger (Git pull)

This project follows the same shared-hosting pattern as other Hostinger Laravel apps (e.g. 7th-trade-hub): **build/install locally, commit artifacts, pull on the server**. The server should not need to resolve Composer packages.

## Why Hostinger Git deploy failed before

Hostinger clones the repo, then runs Composer against the **server PHP** (often 8.2–8.4). This app is **Laravel 8** and its lockfile targets **PHP 8.1**. Composer then fails with “requirements could not be resolved” even though the PHP files were already cloned into File Manager.

## Deployment workflow

1. **Local:** Edit code.
2. **Local (when PHP deps change):**  
   `composer install --no-dev --optimize-autoloader`  
   then commit `vendor/` + `composer.lock` + `composer.json`.
3. **Local:** Commit and push to GitHub.
4. **Server:** Hostinger Git → **Deploy / Pull**.
5. **Server:** Confirm `vendor/autoload.php` exists after pull.

### PHP version on Hostinger

In hPanel → PHP Configuration, set the site to **PHP 8.1** (matches `composer.json` `config.platform.php`).

### Environment

- **Do not** commit `.env`.
- Create `.env` on the server (copy from `.env.example`).
- Set `APP_KEY`, `APP_URL`, DB_*, `APP_ENV=production`, `APP_DEBUG=false`.
- Point the domain document root at the Laravel **`public/`** folder (or Hostinger equivalent).

### After first successful pull

If SSH/terminal is available:

```bash
php artisan key:generate   # if APP_KEY empty
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If there is no SSH: import DB via phpMyAdmin using your dump/baseline process, and create `.env` in File Manager.

## Composer note

`vendor/` **is tracked** in Git for Hostinger. After changing dependencies locally, always re-run:

```bash
composer install --no-dev --optimize-autoloader
```

Then commit the updated `vendor/` with the lockfile.

If Hostinger’s auto-Composer step still prints an error after this change, the pull itself usually still has a working `vendor/` from Git — verify `vendor/autoload.php` and set PHP 8.1.
