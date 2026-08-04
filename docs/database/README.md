# Database documentation

- [FEATURE_CLASSIFICATION.md](FEATURE_CLASSIFICATION.md) — Active/Planned/Legacy/Dead; SaaS gate
- [POLICY.md](POLICY.md) — schema vs seed vs production data; migration rules
- [CLEANUP.md](CLEANUP.md) — dead-code / ghost-table cleanup notes
- [tables.md](tables.md) — all baseline tables and columns
- [relationships.md](relationships.md) — foreign keys and logical refs
- [indexes.md](indexes.md) — indexes and unique keys
- [enums.md](enums.md) — ENUM columns

## Regenerating

```bash
php database/scripts/extract_schema_from_dump.php
php database/scripts/verify_baseline_schema.php --live
php database/scripts/generate_database_docs.php
```

## Environments

| Environment | Schema | Data |
|-------------|--------|------|
| Local / CI fresh | `php artisan migrate` then `php artisan db:seed` | `MinimumConfigSeeder` (settings, admin, appearance, cards, wdmethods, …) — **dev placeholders only** |
| Staging/production new | Prefer import dump, then mark baseline | Production data from dump/backup |
| Disaster recovery | Restore from backup/dump | **Never** `migrate:fresh --seed` |

## Go-live after importing the SQL dump

```bash
# 1. Import database/u502532383_uscounty.sql in phpMyAdmin / mysql client
# 2. Point .env at that database
php database/scripts/mark_baseline_after_dump_import.php
php artisan migrate
```

That records the baseline as already applied and runs corrective migrations (`users.currency` / utf8mb4).

## Dump secrets / credential rotation

The authoritative dump file `database/u502532383_uscounty.sql` may contain production hashes, SMTP passwords, payment keys, and PII.

- Keep the dump **out of public remotes** (listed in `.gitignore`).
- After any go-live or dump share, **rotate**: admin passwords, SMTP credentials, payment API keys, IRS sample credentials, and any user-facing secrets present in the dump.
- Prefer restoring from a private backup channel, not from a committed dump.

## Admin after local seed

- Email: `admin@localhost.test`
- Password: `ChangeMe123!`
