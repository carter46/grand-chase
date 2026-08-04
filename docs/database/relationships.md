# Relationships
Declared foreign keys and logical (application-level) relationships.
## Declared foreign keys
| From | Column | To | On delete/update |
|------|--------|----|------------------|
| `cards` (`cards_user_id_foreign`) | `user_id` | `users`.`id` | ON DELETE CASCADE |
| `card_transactions` (`card_transactions_card_id_foreign`) | `card_id` | `cards`.`id` | ON DELETE CASCADE,
  ADD CONSTRAINT `card_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE |

## Logical relationships (no DB FK)
Most domain tables store user/plan ids as integers without constraints:

- `deposits.user`, `withdrawals.user`, `tp__transactions.user` → `users.id`
- `user_plans.user` → `users.id`; `user_plans.plan` → `plans.id`
- `kycs.user_id`, `notifications.user_id`, `irs_refunds.user_id`, `bnc_transactions.user_id` → `users.id`
- `crypto_accounts.user_id` → `users.id`
- `agents.agent` → `users.id`
