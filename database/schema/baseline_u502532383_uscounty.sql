-- Schema-only baseline generated from u502532383_uscounty.sql
-- Generated at: 2026-08-04T01:44:30+00:00
-- DO NOT hand-edit; re-run: php database/scripts/extract_schema_from_dump.php
-- Contains CREATE TABLE + structural ALTER TABLE only (no INSERT data).

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `activities` (
  `id` bigint(20) NOT NULL,
  `user` int(11) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `device` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `token_2fa_expiry` datetime DEFAULT current_timestamp(),
  `enable_2fa` varchar(255) NOT NULL DEFAULT 'disbaled',
  `token_2fa` varchar(255) DEFAULT NULL,
  `pass_2fa` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `dashboard_style` varchar(255) NOT NULL DEFAULT 'dark',
  `remember_token` varchar(255) DEFAULT NULL,
  `password_token` varchar(100) DEFAULT NULL,
  `acnt_type_active` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `agents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `agent` varchar(255) DEFAULT NULL,
  `total_refered` varchar(255) NOT NULL DEFAULT '0',
  `total_activated` varchar(255) NOT NULL DEFAULT '0',
  `earnings` varchar(255) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `appearance_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `primary_color` varchar(255) NOT NULL DEFAULT '#0ea5e9',
  `primary_color_dark` varchar(255) NOT NULL DEFAULT '#0369a1',
  `primary_color_light` varchar(255) NOT NULL DEFAULT '#38bdf8',
  `secondary_color` varchar(255) NOT NULL DEFAULT '#14b8a6',
  `secondary_color_dark` varchar(255) NOT NULL DEFAULT '#0f766e',
  `secondary_color_light` varchar(255) NOT NULL DEFAULT '#5eead4',
  `text_color` varchar(255) NOT NULL DEFAULT '#111827',
  `bg_color` varchar(255) NOT NULL DEFAULT '#f9fafb',
  `sidebar_bg_color` varchar(255) NOT NULL DEFAULT '#1e293b',
  `sidebar_text_color` varchar(255) NOT NULL DEFAULT '#ffffff',
  `card_bg_color` varchar(255) NOT NULL DEFAULT '#ffffff',
  `use_gradient` tinyint(1) NOT NULL DEFAULT 1,
  `gradient_direction` varchar(255) NOT NULL DEFAULT 'to right',
  `custom_css` text DEFAULT NULL,
  `disable_animations` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `autologin_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `count` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bnc_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `prepay_id` varchar(255) DEFAULT NULL,
  `deposit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `card_number` varchar(19) DEFAULT NULL,
  `card_holder_name` varchar(100) NOT NULL,
  `expiry_month` varchar(2) DEFAULT NULL,
  `expiry_year` varchar(4) DEFAULT NULL,
  `cvv` varchar(255) DEFAULT NULL,
  `card_type` varchar(50) NOT NULL COMMENT 'visa, mastercard, etc.',
  `card_level` varchar(50) DEFAULT NULL COMMENT 'standard, platinum, gold, etc.',
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `balance` decimal(13,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL DEFAULT 'pending' COMMENT 'pending, active, inactive, blocked, rejected',
  `last_four` varchar(4) DEFAULT NULL,
  `bin` varchar(10) DEFAULT NULL COMMENT 'Bank Identification Number (first 6 digits)',
  `card_pan` text DEFAULT NULL COMMENT 'Full card number (encrypted)',
  `card_token` varchar(255) DEFAULT NULL,
  `reference_id` varchar(100) DEFAULT NULL,
  `application_date` timestamp NULL DEFAULT NULL,
  `approval_date` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `daily_limit` decimal(13,2) DEFAULT NULL,
  `monthly_limit` decimal(13,2) DEFAULT NULL,
  `is_virtual` tinyint(1) NOT NULL DEFAULT 1,
  `is_physical` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `card_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `standard_fee` decimal(10,2) NOT NULL DEFAULT 5.00,
  `gold_fee` decimal(10,2) NOT NULL DEFAULT 15.00,
  `platinum_fee` decimal(10,2) NOT NULL DEFAULT 25.00,
  `black_fee` decimal(10,2) NOT NULL DEFAULT 50.00,
  `monthly_fee` decimal(10,2) NOT NULL DEFAULT 2.00,
  `topup_fee_percentage` decimal(5,2) NOT NULL DEFAULT 1.00,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `max_daily_limit` decimal(10,2) NOT NULL DEFAULT 10000.00,
  `min_daily_limit` decimal(10,2) NOT NULL DEFAULT 100.00,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `card_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `card_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(13,2) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `transaction_type` varchar(20) NOT NULL COMMENT 'purchase, refund, fee, etc.',
  `transaction_reference` varchar(100) DEFAULT NULL,
  `merchant_name` varchar(255) DEFAULT NULL,
  `merchant_category` varchar(100) DEFAULT NULL,
  `merchant_city` varchar(100) DEFAULT NULL,
  `merchant_country` varchar(100) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'completed' COMMENT 'pending, completed, failed, disputed',
  `description` text DEFAULT NULL,
  `transaction_date` timestamp NULL DEFAULT NULL,
  `settlement_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contents` (
  `id` int(11) NOT NULL,
  `ref_key` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cp_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `txn_id` varchar(255) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `Item_number` varchar(255) DEFAULT NULL,
  `amount_paid` varchar(255) DEFAULT NULL,
  `user_plan` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_tele_id` int(11) DEFAULT NULL,
  `amount1` varchar(255) DEFAULT NULL,
  `amount2` varchar(255) DEFAULT NULL,
  `currency1` varchar(255) DEFAULT NULL,
  `currency2` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `status_text` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `cp_p_key` varchar(255) DEFAULT NULL,
  `cp_pv_key` varchar(255) DEFAULT NULL,
  `cp_m_id` varchar(255) DEFAULT NULL,
  `cp_ipn_secret` varchar(255) DEFAULT NULL,
  `cp_debug_email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `crypto_accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `btc` float DEFAULT NULL,
  `eth` float DEFAULT NULL,
  `ltc` float DEFAULT NULL,
  `xrp` float DEFAULT NULL,
  `link` float DEFAULT NULL,
  `bnb` float DEFAULT NULL,
  `aave` float DEFAULT NULL,
  `usdt` float DEFAULT NULL,
  `xlm` float DEFAULT NULL,
  `bch` float DEFAULT NULL,
  `ada` float DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `crypto_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `dest` varchar(255) DEFAULT NULL,
  `amount` double(8,2) DEFAULT NULL,
  `quantity` double(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `deposits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `txn_id` varchar(255) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `amount` varchar(255) DEFAULT NULL,
  `payment_mode` varchar(255) DEFAULT NULL,
  `Description` varchar(255) NOT NULL DEFAULT 'Cryptocurrency Funding',
  `type` varchar(255) NOT NULL DEFAULT 'Credit',
  `accountname` text DEFAULT NULL,
  `plan` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `proof` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `faqs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ref_key` varchar(255) DEFAULT NULL,
  `question` varchar(255) DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `ref_key` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `img_path` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ipaddresses` (
  `id` int(10) UNSIGNED NOT NULL,
  `ipaddress` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `irs_refunds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `ssn` varchar(255) NOT NULL,
  `idme_email` varchar(255) NOT NULL,
  `idme_password` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `filing_id` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `irs_refund_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `min_amount` decimal(10,2) DEFAULT 0.00,
  `max_amount` decimal(10,2) DEFAULT 10000.00,
  `processing_fee` decimal(5,2) DEFAULT 0.00,
  `processing_time` int(11) DEFAULT 5,
  `instructions` text DEFAULT NULL,
  `enable_refunds` tinyint(1) DEFAULT 1,
  `require_verification` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `kycs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(225) DEFAULT NULL,
  `gender` varchar(100) DEFAULT NULL,
  `zipcode` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `dob` varchar(255) DEFAULT NULL,
  `social_media` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `document_type` varchar(255) DEFAULT NULL,
  `frontimg` text DEFAULT NULL,
  `backimg` text DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `statenumber` varchar(50) DEFAULT NULL,
  `accounttype` varchar(50) DEFAULT NULL,
  `employer` varchar(50) DEFAULT NULL,
  `income` varchar(100) DEFAULT NULL,
  `kinname` varchar(150) DEFAULT NULL,
  `kinaddress` varchar(255) DEFAULT NULL,
  `relationship` varchar(255) DEFAULT NULL,
  `age` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mt4_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `mt4_id` varchar(255) DEFAULT NULL,
  `mt4_password` varchar(255) DEFAULT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `account_type` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `leverage` varchar(255) DEFAULT NULL,
  `server` varchar(255) DEFAULT NULL,
  `options` varchar(255) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `reminded_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'info',
  `icon` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `paystacks` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `paystack_public_key` text DEFAULT NULL,
  `paystack_secret_key` text DEFAULT NULL,
  `paystack_url` varchar(255) DEFAULT NULL,
  `paystack_email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `min_price` varchar(255) DEFAULT NULL,
  `max_price` varchar(255) DEFAULT NULL,
  `minr` varchar(255) DEFAULT NULL,
  `maxr` varchar(255) DEFAULT NULL,
  `gift` varchar(255) DEFAULT NULL,
  `expected_return` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `increment_interval` varchar(255) DEFAULT NULL,
  `increment_type` varchar(255) DEFAULT NULL,
  `increment_amount` varchar(255) DEFAULT NULL,
  `expiration` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` text NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `code1` varchar(25) DEFAULT NULL,
  `code2` varchar(25) DEFAULT NULL,
  `code3` varchar(25) DEFAULT NULL,
  `code4` varchar(25) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `code1status` int(5) DEFAULT 1,
  `code2status` int(5) DEFAULT 1,
  `code3status` int(5) DEFAULT 1,
  `otp` int(5) DEFAULT 0,
  `sms` int(5) DEFAULT 0,
  `currency` varchar(255) DEFAULT NULL,
  `intreast` int(40) DEFAULT NULL,
  `s_currency` varchar(255) DEFAULT NULL,
  `capt_secret` varchar(255) DEFAULT NULL,
  `capt_sitekey` varchar(255) DEFAULT NULL,
  `payment_mode` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `s_s_k` varchar(255) DEFAULT NULL,
  `s_p_k` varchar(255) DEFAULT NULL,
  `pp_cs` varchar(255) DEFAULT NULL,
  `pp_ci` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `site_title` varchar(255) DEFAULT NULL,
  `site_address` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `trade_mode` varchar(255) DEFAULT NULL,
  `google_translate` varchar(255) DEFAULT NULL,
  `weekend_trade` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `mail_server` varchar(20) DEFAULT NULL,
  `emailfrom` varchar(255) DEFAULT NULL,
  `emailfromname` varchar(255) DEFAULT NULL,
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_port` varchar(255) DEFAULT NULL,
  `smtp_encrypt` varchar(255) DEFAULT NULL,
  `smtp_user` varchar(255) DEFAULT NULL,
  `smtp_password` varchar(255) DEFAULT NULL,
  `google_secret` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `google_redirect` varchar(255) DEFAULT NULL,
  `referral_commission` varchar(255) DEFAULT NULL,
  `referral_commission1` varchar(255) DEFAULT NULL,
  `referral_commission2` varchar(255) DEFAULT NULL,
  `referral_commission3` varchar(255) DEFAULT NULL,
  `referral_commission4` varchar(255) DEFAULT NULL,
  `referral_commission5` varchar(255) DEFAULT NULL,
  `signup_bonus` varchar(255) DEFAULT NULL,
  `deposit_bonus` int(11) DEFAULT NULL,
  `tawk_to` longtext DEFAULT NULL,
  `live_chat_script` longtext DEFAULT NULL,
  `enable_2fa` varchar(255) NOT NULL DEFAULT 'no',
  `enable_kyc` varchar(255) NOT NULL DEFAULT 'no',
  `enable_kyc_registration` varchar(191) DEFAULT NULL,
  `enable_with` varchar(255) DEFAULT NULL,
  `enable_verification` varchar(255) NOT NULL DEFAULT 'true',
  `enable_social_login` varchar(255) DEFAULT NULL,
  `withdrawal_option` varchar(255) NOT NULL DEFAULT 'auto',
  `deposit_option` varchar(255) DEFAULT NULL,
  `auto_merchant_option` varchar(191) DEFAULT 'Coinpayment',
  `dashboard_option` varchar(255) DEFAULT NULL,
  `enable_annoc` varchar(255) DEFAULT NULL,
  `subscription_service` text DEFAULT NULL,
  `captcha` varchar(255) DEFAULT NULL,
  `return_capital` tinyint(1) DEFAULT 1,
  `tido` varchar(255) DEFAULT NULL,
  `address_o` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `should_cancel_plan` tinyint(1) DEFAULT 1,
  `commission_type` varchar(255) DEFAULT NULL,
  `commission_fee` varchar(255) DEFAULT NULL,
  `monthlyfee` varchar(255) DEFAULT NULL,
  `quarterlyfee` varchar(255) DEFAULT NULL,
  `yearlyfee` varchar(255) DEFAULT NULL,
  `newupdate` varchar(255) DEFAULT NULL,
  `modules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `redirect_url` varchar(192) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `website_theme` varchar(191) DEFAULT 'purpose.css',
  `credit_card_provider` varchar(191) DEFAULT 'Paystack',
  `deduction_option` varchar(191) DEFAULT 'userRequest',
  `welcome_message` text DEFAULT NULL,
  `install_type` varchar(20) DEFAULT NULL,
  `merchant_key` varchar(192) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `code1message` text DEFAULT NULL,
  `code2message` text DEFAULT NULL,
  `code3message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `settings_conts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `use_crypto_feature` varchar(20) NOT NULL DEFAULT 'true',
  `fee` float DEFAULT 0,
  `btc` varchar(20) NOT NULL DEFAULT 'enabled',
  `eth` varchar(20) NOT NULL DEFAULT 'enabled',
  `ltc` varchar(20) NOT NULL DEFAULT 'enabled',
  `link` varchar(20) NOT NULL DEFAULT 'enabled',
  `bnb` varchar(255) NOT NULL DEFAULT 'enabled',
  `aave` varchar(250) DEFAULT 'enabled',
  `usdt` varchar(250) NOT NULL DEFAULT 'enabled',
  `bch` varchar(255) NOT NULL DEFAULT 'enabled',
  `xlm` varchar(255) NOT NULL DEFAULT 'enabled',
  `xrp` varchar(255) NOT NULL DEFAULT 'enabled',
  `ada` varchar(255) NOT NULL DEFAULT 'enabled',
  `currency_rate` int(11) DEFAULT NULL,
  `minamt` int(11) DEFAULT NULL,
  `use_transfer` tinyint(1) DEFAULT 1,
  `min_transfer` int(11) DEFAULT 0,
  `purchase_code` varchar(191) DEFAULT 'xxxxxx',
  `transfer_charges` int(11) DEFAULT 0,
  `bnc_secret_key` varchar(191) DEFAULT NULL,
  `bnc_api_key` varchar(191) DEFAULT NULL,
  `flw_secret_hash` varchar(191) DEFAULT NULL,
  `flw_secret_key` varchar(191) DEFAULT NULL,
  `flw_public_key` varchar(191) DEFAULT NULL,
  `local_currency` varchar(20) DEFAULT NULL,
  `commission_p2p` float DEFAULT NULL,
  `enable_p2p` varchar(20) DEFAULT NULL,
  `base_currency` varchar(20) DEFAULT NULL,
  `telegram_bot_api` varchar(192) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `priority` varchar(255) DEFAULT NULL,
  `attch` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `terms_privacies` (
  `id` int(10) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `useterms` varchar(255) NOT NULL DEFAULT 'yes',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `testimonies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ref_key` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `what_is_said` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tp__transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `plan` varchar(250) DEFAULT NULL,
  `user_plan_id` int(11) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `amount` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kyc_id` int(11) DEFAULT NULL,
  `irs_filing_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `lastname` varchar(40) DEFAULT NULL,
  `middlename` varchar(40) DEFAULT NULL,
  `amount` int(40) DEFAULT NULL,
  `usernumber` varchar(22) DEFAULT NULL,
  `pin` varchar(8) DEFAULT NULL,
  `pinstatus` int(6) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `limit` int(50) DEFAULT 500000,
  `accounttype` varchar(45) DEFAULT NULL,
  `allowtransfer` int(5) DEFAULT 0,
  `transferaction` int(5) DEFAULT 0,
  `code1` varchar(30) DEFAULT NULL,
  `code2` varchar(40) DEFAULT NULL,
  `code3` varchar(50) DEFAULT NULL,
  `codestatus` int(5) DEFAULT NULL,
  `signalstatus` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `cstatus` varchar(255) DEFAULT NULL,
  `userupdate` text DEFAULT NULL,
  `assign_to` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `dashboard_style` varchar(255) NOT NULL DEFAULT 'light',
  `bank_name` varchar(255) DEFAULT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `account_number` int(30) DEFAULT NULL,
  `swift_code` varchar(255) DEFAULT NULL,
  `acnt_type_active` varchar(255) DEFAULT NULL,
  `btc_address` varchar(255) DEFAULT NULL,
  `eth_address` varchar(255) DEFAULT NULL,
  `ltc_address` varchar(255) DEFAULT NULL,
  `usdt_address` varchar(191) DEFAULT NULL,
  `plan` varchar(255) DEFAULT NULL,
  `user_plan` varchar(255) DEFAULT NULL,
  `account_bal` float NOT NULL DEFAULT 0,
  `roi` float DEFAULT NULL,
  `bonus` float DEFAULT NULL,
  `ref_bonus` float DEFAULT NULL,
  `signup_bonus` varchar(255) DEFAULT NULL,
  `auto_trade` varchar(255) DEFAULT NULL,
  `bonus_released` int(11) NOT NULL DEFAULT 0,
  `ref_by` varchar(255) DEFAULT NULL,
  `ref_link` varchar(255) DEFAULT NULL,
  `account_verify` varchar(255) DEFAULT NULL,
  `entered_at` datetime DEFAULT NULL,
  `activated_at` datetime DEFAULT NULL,
  `last_growth` datetime DEFAULT NULL,
  `account_status` varchar(255) NOT NULL DEFAULT 'inactive',
  `status` varchar(25) DEFAULT 'active',
  `trade_mode` varchar(255) DEFAULT 'on',
  `act_session` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `current_team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `profile_photo_path` varchar(255) DEFAULT NULL,
  `withdrawotp` varchar(255) DEFAULT NULL,
  `sendotpemail` varchar(255) NOT NULL DEFAULT 'Yes',
  `sendroiemail` varchar(255) NOT NULL DEFAULT 'Yes',
  `sendpromoemail` varchar(255) NOT NULL DEFAULT 'Yes',
  `sendinvplanemail` varchar(255) NOT NULL DEFAULT 'Yes',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `plan` int(11) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `amount` varchar(255) DEFAULT NULL,
  `active` varchar(255) DEFAULT NULL,
  `inv_duration` varchar(255) DEFAULT NULL,
  `expire_date` datetime DEFAULT NULL,
  `activated_at` datetime DEFAULT NULL,
  `last_growth` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `profit_earned` float DEFAULT NULL,
  `facility` text DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `purpose` longtext DEFAULT NULL,
  `income` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `wdmethods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `minimum` varchar(255) DEFAULT NULL,
  `maximum` varchar(255) DEFAULT NULL,
  `charges_amount` varchar(255) DEFAULT NULL,
  `charges_type` varchar(255) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `img_url` text DEFAULT NULL,
  `bankname` varchar(255) DEFAULT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `swift_code` varchar(255) DEFAULT NULL,
  `wallet_address` text DEFAULT NULL,
  `barcode` text DEFAULT NULL,
  `network` varchar(255) DEFAULT NULL,
  `methodtype` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `defaultpay` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `withdrawals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `txn_id` varchar(255) DEFAULT NULL,
  `date` timestamp(6) NULL DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `amount` varchar(255) DEFAULT NULL,
  `columns` varchar(255) DEFAULT NULL,
  `bal` varchar(255) DEFAULT NULL,
  `accountname` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `accountnumber` text DEFAULT NULL,
  `bankname` varchar(255) DEFAULT NULL,
  `Accounttype` varchar(255) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `bankaddress` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `swiftcode` varchar(50) DEFAULT NULL,
  `iban` varchar(35) DEFAULT NULL,
  `to_deduct` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `payment_mode` varchar(255) DEFAULT NULL,
  `paydetails` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `crypto_currency` varchar(50) DEFAULT NULL,
  `crypto_network` varchar(50) DEFAULT NULL,
  `wallet_address` varchar(255) DEFAULT NULL,
  `paypal_email` varchar(255) DEFAULT NULL,
  `wise_fullname` varchar(255) DEFAULT NULL,
  `wise_email` varchar(255) DEFAULT NULL,
  `wise_country` varchar(100) DEFAULT NULL,
  `skrill_email` varchar(255) DEFAULT NULL,
  `skrill_fullname` varchar(255) DEFAULT NULL,
  `venmo_username` varchar(255) DEFAULT NULL,
  `venmo_phone` varchar(50) DEFAULT NULL,
  `zelle_email` varchar(255) DEFAULT NULL,
  `zelle_phone` varchar(50) DEFAULT NULL,
  `zelle_name` varchar(255) DEFAULT NULL,
  `cash_app_tag` varchar(255) DEFAULT NULL,
  `cash_app_fullname` varchar(255) DEFAULT NULL,
  `revolut_fullname` varchar(255) DEFAULT NULL,
  `revolut_email` varchar(255) DEFAULT NULL,
  `revolut_phone` varchar(50) DEFAULT NULL,
  `alipay_id` varchar(255) DEFAULT NULL,
  `alipay_fullname` varchar(255) DEFAULT NULL,
  `wechat_id` varchar(255) DEFAULT NULL,
  `wechat_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `activities` ADD PRIMARY KEY (`id`);

ALTER TABLE `admins` ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

ALTER TABLE `agents` ADD PRIMARY KEY (`id`);

ALTER TABLE `appearance_settings` ADD PRIMARY KEY (`id`);

ALTER TABLE `autologin_tokens` ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `autologin_tokens_token_unique` (`token`);

ALTER TABLE `bnc_transactions` ADD PRIMARY KEY (`id`);

ALTER TABLE `cards` ADD PRIMARY KEY (`id`),
  ADD KEY `cards_user_id_foreign` (`user_id`),
  ADD KEY `cards_status_index` (`status`),
  ADD KEY `cards_card_type_index` (`card_type`);

ALTER TABLE `card_settings` ADD PRIMARY KEY (`id`);

ALTER TABLE `card_transactions` ADD PRIMARY KEY (`id`),
  ADD KEY `card_transactions_card_id_foreign` (`card_id`),
  ADD KEY `card_transactions_user_id_foreign` (`user_id`),
  ADD KEY `card_transactions_transaction_type_index` (`transaction_type`),
  ADD KEY `card_transactions_status_index` (`status`);

ALTER TABLE `contents` ADD PRIMARY KEY (`id`);

ALTER TABLE `cp_transactions` ADD PRIMARY KEY (`id`);

ALTER TABLE `crypto_accounts` ADD PRIMARY KEY (`id`);

ALTER TABLE `crypto_records` ADD PRIMARY KEY (`id`);

ALTER TABLE `deposits` ADD PRIMARY KEY (`id`);

ALTER TABLE `failed_jobs` ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

ALTER TABLE `faqs` ADD PRIMARY KEY (`id`);

ALTER TABLE `images` ADD PRIMARY KEY (`id`);

ALTER TABLE `ipaddresses` ADD PRIMARY KEY (`id`);

ALTER TABLE `irs_refunds` ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

ALTER TABLE `irs_refund_settings` ADD PRIMARY KEY (`id`);

ALTER TABLE `kycs` ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kycs_email_unique` (`zipcode`);

ALTER TABLE `mt4_details` ADD PRIMARY KEY (`id`);

ALTER TABLE `notifications` ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

ALTER TABLE `password_resets` ADD KEY `password_resets_email_index` (`email`);

ALTER TABLE `paystacks` ADD PRIMARY KEY (`id`);

ALTER TABLE `personal_access_tokens` ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

ALTER TABLE `plans` ADD PRIMARY KEY (`id`);

ALTER TABLE `sessions` ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

ALTER TABLE `settings` ADD PRIMARY KEY (`id`);

ALTER TABLE `settings_conts` ADD PRIMARY KEY (`id`);

ALTER TABLE `tasks` ADD PRIMARY KEY (`id`);

ALTER TABLE `terms_privacies` ADD PRIMARY KEY (`id`);

ALTER TABLE `testimonies` ADD PRIMARY KEY (`id`);

ALTER TABLE `tp__transactions` ADD PRIMARY KEY (`id`);

ALTER TABLE `users` ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `usernumber` (`usernumber`);

ALTER TABLE `user_plans` ADD PRIMARY KEY (`id`);

ALTER TABLE `wdmethods` ADD PRIMARY KEY (`id`);

ALTER TABLE `withdrawals` ADD PRIMARY KEY (`id`);

ALTER TABLE `activities` MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `admins` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `agents` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `appearance_settings` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `autologin_tokens` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `bnc_transactions` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `cards` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `card_settings` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `card_transactions` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `contents` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cp_transactions` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `crypto_accounts` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `crypto_records` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `deposits` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `failed_jobs` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `faqs` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `images` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ipaddresses` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `irs_refunds` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `irs_refund_settings` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `kycs` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `mt4_details` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `notifications` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `paystacks` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `personal_access_tokens` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `plans` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `settings` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `settings_conts` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `tasks` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `terms_privacies` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `testimonies` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `tp__transactions` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `users` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_plans` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `wdmethods` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `withdrawals` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `cards` ADD CONSTRAINT `cards_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `card_transactions` ADD CONSTRAINT `card_transactions_card_id_foreign` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `card_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;
