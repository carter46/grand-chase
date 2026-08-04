# Indexes
Primary, unique, and secondary indexes from the authoritative dump.

## `activities`
- ADD PRIMARY KEY (`id`)

## `admins`
- ADD PRIMARY KEY (`id`)
- ADD UNIQUE KEY `admins_email_unique` (`email`)

## `agents`
- ADD PRIMARY KEY (`id`)

## `appearance_settings`
- ADD PRIMARY KEY (`id`)

## `autologin_tokens`
- ADD PRIMARY KEY (`id`)
- ADD UNIQUE KEY `autologin_tokens_token_unique` (`token`)

## `bnc_transactions`
- ADD PRIMARY KEY (`id`)

## `card_settings`
- ADD PRIMARY KEY (`id`)

## `card_transactions`
- ADD PRIMARY KEY (`id`)
- ADD KEY `card_transactions_card_id_foreign` (`card_id`)
- ADD KEY `card_transactions_user_id_foreign` (`user_id`)
- ADD KEY `card_transactions_transaction_type_index` (`transaction_type`)
- ADD KEY `card_transactions_status_index` (`status`)
- ADD CONSTRAINT `card_transactions_card_id_foreign` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`) ON DELETE CASCADE
- ADD CONSTRAINT `card_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE

## `cards`
- ADD PRIMARY KEY (`id`)
- ADD KEY `cards_user_id_foreign` (`user_id`)
- ADD KEY `cards_status_index` (`status`)
- ADD KEY `cards_card_type_index` (`card_type`)
- ADD CONSTRAINT `cards_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE

## `contents`
- ADD PRIMARY KEY (`id`)

## `cp_transactions`
- ADD PRIMARY KEY (`id`)

## `crypto_accounts`
- ADD PRIMARY KEY (`id`)

## `crypto_records`
- ADD PRIMARY KEY (`id`)

## `deposits`
- ADD PRIMARY KEY (`id`)

## `failed_jobs`
- ADD PRIMARY KEY (`id`)
- ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)

## `faqs`
- ADD PRIMARY KEY (`id`)

## `images`
- ADD PRIMARY KEY (`id`)

## `ipaddresses`
- ADD PRIMARY KEY (`id`)

## `irs_refund_settings`
- ADD PRIMARY KEY (`id`)

## `irs_refunds`
- ADD PRIMARY KEY (`id`)
- ADD KEY `fk_user_id` (`user_id`)

## `kycs`
- ADD PRIMARY KEY (`id`)
- ADD UNIQUE KEY `kycs_email_unique` (`zipcode`)

## `mt4_details`
- ADD PRIMARY KEY (`id`)

## `notifications`
- ADD PRIMARY KEY (`id`)
- ADD KEY `notifications_user_id_foreign` (`user_id`)

## `password_resets`
- ADD KEY `password_resets_email_index` (`email`)

## `paystacks`
- ADD PRIMARY KEY (`id`)

## `personal_access_tokens`
- ADD PRIMARY KEY (`id`)
- ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`)
- ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`

## `plans`
- ADD PRIMARY KEY (`id`)

## `sessions`
- ADD PRIMARY KEY (`id`)
- ADD KEY `sessions_user_id_index` (`user_id`)
- ADD KEY `sessions_last_activity_index` (`last_activity`)

## `settings`
- ADD PRIMARY KEY (`id`)

## `settings_conts`
- ADD PRIMARY KEY (`id`)

## `tasks`
- ADD PRIMARY KEY (`id`)

## `terms_privacies`
- ADD PRIMARY KEY (`id`)

## `testimonies`
- ADD PRIMARY KEY (`id`)

## `tp__transactions`
- ADD PRIMARY KEY (`id`)

## `user_plans`
- ADD PRIMARY KEY (`id`)

## `users`
- ADD PRIMARY KEY (`id`)
- ADD UNIQUE KEY `users_email_unique` (`email`)
- ADD UNIQUE KEY `usernumber` (`usernumber`)

## `wdmethods`
- ADD PRIMARY KEY (`id`)

## `withdrawals`
- ADD PRIMARY KEY (`id`)
