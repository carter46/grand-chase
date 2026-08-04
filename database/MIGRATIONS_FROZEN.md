# Migrations status

Freeze lifted after baseline verification passed (`php database/scripts/verify_baseline_schema.php --live`).

## Active migrator path (`database/migrations`)

1. `2026_08_04_000000_baseline_from_u502532383_uscounty.php` — schema baseline from dump
2. `2026_08_04_010000_add_currency_columns_to_users_table.php` — corrective
3. `2026_08_04_020000_convert_latin1_tables_to_utf8mb4.php` — corrective

## Archive

Previous migrations live in `database/migrations_archive_pre_baseline/` (not loaded by Laravel).

## Go-live with SQL dump

```bash
# Import database/u502532383_uscounty.sql first, then:
php database/scripts/mark_baseline_after_dump_import.php
php artisan migrate
```

Do **not** run archived historical migrations. Do **not** use `migrate:fresh --seed` for production restore.
