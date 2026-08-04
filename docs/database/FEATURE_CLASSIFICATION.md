# Feature classification

Product decisions for keep / disable / archive / remove.  
**Rule:** Do not delete SaaS code until Decision = Dead and signed off.

| Feature | Decision | Action |
|---------|----------|--------|
| Core banking (deposits/withdrawals/transfers) | **Active** | Keep |
| Cards | **Active** | Keep |
| KYC / IRS | **Active** | Keep |
| Investments / plans | **Active** | Keep |
| Crypto swap | **Planned** | Keep code; gate on `modules.cryptoswap` |
| MT4 managed accounts (`mt4_details`) | **Legacy** | Keep local confirm/delete; SaaS copy-trade gated |
| Courses / Membership | **Planned** | Keep code; **disabled** via feature gate + module flag |
| Signals | **Planned** | Keep code; **disabled** via feature gate; `signalaction` only updates `signalstatus` |
| Copy-trade masters (remote SaaS) | **Planned** | Keep code; **disabled** via feature gate until merchant_key + product OK |
| P2P | **Dead** | No UI; leave `settings_conts` leftovers; no DROP yet |
| Ads / Upgrades / Asset | **Dead** | Already removed from app |
| Autologin (`autologin_tokens`) | **Dead** | Dropped via corrective migration `2026_08_04_050000_*` |

## Feature gate

`config/features.php` → `saas_remote_modules` (env `FEATURE_SAAS_REMOTE_MODULES`, default `false`).

When false, `AppServiceProvider` forces `$mod['membership']`, `$mod['signal']`, and `$mod['subscription']` to false so nav/routes that check modules stay hidden without deleting controllers.

Set `FEATURE_SAAS_REMOTE_MODULES=true` only after licensing/merchant_key and promoting those rows to **Active**.
