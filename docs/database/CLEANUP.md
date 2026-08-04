# Schema cleanup actions taken

Completed as part of the Database Migration Audit implementation.

## Go-live crash fixes

- Removed `Asset::all()` from admin settings; `/admin/dashboard/settings` redirects to App Settings.
- Restored `LogicController` for add/delete agent against live `agents` table.
- Removed broken `SettingsController` routes; `deletewdmethod` → `PaymentController::deletepaymethod`.
- Removed dead `SomeController` / `changetheme` route.
- Guarded `users.currency` / `users.s_currency` writes/reads until corrective migration runs.
- Emptied `DatabaseSeeder` (no ghost `adverts` seed); documented seed ≠ production data.

## Removed dead code targeting missing tables

- Models: `Asset`, `Adverts`, `Upgrade`, `Userlog`
- Factories: `AdvertsFactory`, `OrdersP2pFactory`
- Unused import: `CourseCategory` in `MembershipController`
- Unused import: `OrdersP2p` / `Asset` from `HomeController`

## Still present (product/SaaS — not local DB ghosts)

Membership, signals, and copy-trade stacks may still call remote APIs. They do **not** require restoring ghost tables. Strip routes/nav later if those modules are retired.

## Optional later corrective

- ~~Drop unused dump table `autologin_tokens`~~ — done in `2026_08_04_050000_*` (Dead)

## Follow-up fixes (deep audit)

- Added `MinimumConfigSeeder` for local/CI `db:seed`
- Added `database/scripts/mark_baseline_after_dump_import.php` for go-live
- Deepened `verify_baseline_schema.php` (dump-ref vs baseline-verify information_schema)
- Hardened currency migration `hasColumn` checks outside Blueprint
- Removed dead `HomeController::p2pView`
- Guarded null `$settings` in `AppServiceProvider`
- Feature classification + SaaS gate (`config/features.php`)
- Money DECIMAL, KYC unique fix, crypto_records.user_id, orphan cleanup migrations
- Idempotent baseline migration
