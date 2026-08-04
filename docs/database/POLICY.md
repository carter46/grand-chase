# Database Schema Policy

## Three data layers (keep distinct)

| Layer | What it is | How managed |
|-------|------------|-------------|
| **Schema** | Tables, columns, indexes, FKs, defaults, enums | Laravel migrations only |
| **Seed data** | Dev/demo fixtures for a usable empty app | Laravel seeders (`database/seeders`) |
| **Production data** | Real admins, settings, plans, users, transactions | SQL dump import / backups |

`php artisan migrate:fresh --seed` is **not** a production restore. It builds a **dev-only** schema plus `MinimumConfigSeeder` placeholders (admin@localhost.test / ChangeMe123!). Production restore = import SQL dump (or backup), then:

```bash
php database/scripts/mark_baseline_after_dump_import.php
php artisan migrate
```

## Schema change rules

Every schema change **must**:

1. Be implemented as a Laravel migration under `database/migrations`
2. Provide both `up()` and `down()` — no exceptions
3. Update related Eloquent models (`$casts`, `$fillable`/`$guarded`) when columns change
4. Be reviewed in a PR before merge

**Forbidden as primary schema evolution:**

- phpMyAdmin `ALTER` / `CREATE` / `DROP`
- Direct `ALTER TABLE` / `DB::statement` schema changes in application code
- Editing an already-shipped baseline migration

phpMyAdmin may be used only for: viewing data, importing/exporting data, debugging, emergency recovery.

## Baseline vs corrective migrations

- **Baseline** faithfully reproduces production schema (including known quirks such as latin1 tables).
- **Improvements** (utf8mb4 conversion, new columns, dropping unused tables) are **separate corrective migrations** after the baseline is verified.

## Authoritative dump

Until replaced by a newer verified export, go-live schema+data authority is:

`database/u502532383_uscounty.sql`

## PR checklist

- [ ] Migration present with `up()` and `down()`
- [ ] Models / validation updated
- [ ] No phpMyAdmin schema change
- [ ] Seeders updated only if seed data must change (not production data)
- [ ] Docs under `docs/database/` updated when structure changes

## Active migration set (post-baseline)

See `database/MIGRATIONS_FROZEN.md`. Historical files are in `database/migrations_archive_pre_baseline/`.

Package note: `Laravel\Sanctum\Sanctum::ignoreMigrations()` is called from `AppServiceProvider` because the baseline already creates `personal_access_tokens`.
