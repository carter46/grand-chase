<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Minimum config for local/CI only — NOT production data.
 * Production restore = import SQL dump / backup.
 */
class MinimumConfigSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        if (Schema::hasTable('settings') && DB::table('settings')->where('id', 1)->doesntExist()) {
            DB::table('settings')->insert([
                'id' => 1,
                'site_name' => 'Local Dev Bank',
                'site_title' => 'Local Dev Bank',
                'description' => 'Local development configuration only.',
                'currency' => '$',
                's_currency' => 'USD',
                'contact_email' => 'admin@localhost.test',
                'emailfrom' => 'noreply@localhost.test',
                'emailfromname' => 'Local Dev Bank',
                'timezone' => 'UTC',
                'mail_server' => 'smtp',
                'enable_2fa' => 'no',
                'enable_kyc' => 'no',
                'enable_kyc_registration' => 'no',
                'enable_verification' => 'true',
                'withdrawal_option' => 'manual',
                'deposit_option' => 'manual',
                'website_theme' => 'purpose.css',
                'modules' => json_encode([
                    'investment' => true,
                    'cryptoswap' => false,
                    'membership' => false,
                    'signal' => false,
                ]),
                'return_capital' => 1,
                'should_cancel_plan' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('settings_conts') && DB::table('settings_conts')->where('id', 1)->doesntExist()) {
            DB::table('settings_conts')->insert([
                'id' => 1,
                'use_crypto_feature' => 'false',
                'fee' => 0,
                'btc' => 'enabled',
                'eth' => 'enabled',
                'ltc' => 'enabled',
                'link' => 'enabled',
                'bnb' => 'enabled',
                'usdt' => 'enabled',
                'bch' => 'enabled',
                'xlm' => 'enabled',
                'xrp' => 'enabled',
                'ada' => 'enabled',
                'use_transfer' => 1,
                'min_transfer' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('admins') && DB::table('admins')->count() === 0) {
            DB::table('admins')->insert([
                'firstName' => 'Local',
                'lastName' => 'Admin',
                'email' => 'admin@localhost.test',
                'password' => Hash::make('ChangeMe123!'),
                'enable_2fa' => 'disbaled',
                'dashboard_style' => 'light',
                'status' => 'active',
                'type' => 'Super Admin',
                'acnt_type_active' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('appearance_settings') && DB::table('appearance_settings')->where('id', 1)->doesntExist()) {
            DB::table('appearance_settings')->insert([
                'id' => 1,
                'primary_color' => '#0ea5e9',
                'primary_color_dark' => '#0369a1',
                'primary_color_light' => '#38bdf8',
                'secondary_color' => '#14b8a6',
                'secondary_color_dark' => '#0f766e',
                'secondary_color_light' => '#5eead4',
                'text_color' => '#111827',
                'bg_color' => '#f9fafb',
                'sidebar_bg_color' => '#1e293b',
                'sidebar_text_color' => '#ffffff',
                'card_bg_color' => '#ffffff',
                'use_gradient' => 1,
                'gradient_direction' => 'to right',
                'disable_animations' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('card_settings') && DB::table('card_settings')->where('id', 1)->doesntExist()) {
            DB::table('card_settings')->insert([
                'id' => 1,
                'standard_fee' => 5.00,
                'gold_fee' => 15.00,
                'platinum_fee' => 25.00,
                'black_fee' => 50.00,
                'monthly_fee' => 2.00,
                'topup_fee_percentage' => 1.00,
                'is_enabled' => 1,
                'max_daily_limit' => 10000.00,
                'min_daily_limit' => 100.00,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('wdmethods') && DB::table('wdmethods')->count() === 0) {
            DB::table('wdmethods')->insert([
                [
                    'name' => 'Bank Transfer',
                    'minimum' => '100',
                    'maximum' => '100000',
                    'charges_amount' => '0',
                    'charges_type' => 'fixed',
                    'duration' => '1-3 business days',
                    'methodtype' => 'currency',
                    'type' => 'withdrawal',
                    'status' => 'enabled',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'Bank Deposit',
                    'minimum' => '100',
                    'maximum' => '100000',
                    'charges_amount' => '0',
                    'charges_type' => 'fixed',
                    'duration' => 'Instant',
                    'methodtype' => 'currency',
                    'type' => 'deposit',
                    'status' => 'enabled',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        }

        if (Schema::hasTable('terms_privacies') && DB::table('terms_privacies')->where('id', 1)->doesntExist()) {
            DB::table('terms_privacies')->insert([
                'id' => 1,
                'description' => 'Local development terms placeholder. Replace before production.',
                'useterms' => 'yes',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('irs_refund_settings') && DB::table('irs_refund_settings')->where('id', 1)->doesntExist()) {
            DB::table('irs_refund_settings')->insert([
                'id' => 1,
                'min_amount' => 0.00,
                'max_amount' => 10000.00,
                'processing_fee' => 0.00,
                'processing_time' => 5,
                'enable_refunds' => 0,
                'require_verification' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('paystacks') && DB::table('paystacks')->where('id', 1)->doesntExist()) {
            DB::table('paystacks')->insert([
                'id' => 1,
                'paystack_public_key' => null,
                'paystack_secret_key' => null,
                'paystack_url' => null,
                'paystack_email' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
