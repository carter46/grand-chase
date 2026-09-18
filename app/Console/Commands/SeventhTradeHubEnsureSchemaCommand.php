<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Throwable;

/**
 * Emergency: finish TradeHub schema without ALTER on fat `settings` (MySQL 1118).
 * Safe to re-run. Marks Hub migrations complete when schema is ready.
 */
class SeventhTradeHubEnsureSchemaCommand extends Command
{
    protected $signature = 'seventh-tradehub:ensure-schema';

    protected $description = 'Create 7th Trade Hub tables without altering settings (avoids MySQL row-size 1118)';

    public function handle()
    {
        try {
            if (Schema::hasTable('admins') && !Schema::hasColumn('admins', 'is_super_admin')) {
                Schema::table('admins', function (Blueprint $table) {
                    $table->boolean('is_super_admin')->default(0);
                });
                $this->info('Added admins.is_super_admin');
            }

            if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_demo_user')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->boolean('is_demo_user')->default(0);
                });
                $this->info('Added users.is_demo_user');
            }

            if (!Schema::hasTable('seventh_tradehub_integrations')) {
                Schema::create('seventh_tradehub_integrations', function (Blueprint $table) {
                    $table->string('context', 32);
                    $table->boolean('enabled')->default(0);
                    $table->string('integration_id', 36)->nullable();
                    $table->string('client_id', 255)->nullable();
                    $table->text('client_secret_enc')->nullable();
                    $table->text('webhook_secret_enc')->nullable();
                    $table->string('expected_user_email', 255)->nullable();
                    $table->string('expected_admin_email', 255)->nullable();
                    $table->dateTime('updated_at')->nullable();
                    $table->unsignedBigInteger('updated_by')->nullable();
                    $table->primary('context');
                    $table->unique('integration_id', 'uk_integration_id');
                });
                $this->info('Created seventh_tradehub_integrations');
            }

            foreach (['demo', 'owned_tool'] as $context) {
                if (!DB::table('seventh_tradehub_integrations')->where('context', $context)->exists()) {
                    DB::table('seventh_tradehub_integrations')->insert([
                        'context' => $context,
                        'enabled' => 0,
                        'updated_at' => now(),
                    ]);
                }
            }

            if (!Schema::hasTable('seventh_tradehub_subscriptions')) {
                Schema::create('seventh_tradehub_subscriptions', function (Blueprint $table) {
                    $table->string('integration_id', 36);
                    $table->integer('tool_id')->nullable();
                    $table->string('public_id', 64)->nullable();
                    $table->string('status', 32)->default('pending_setup');
                    $table->dateTime('expires_at')->nullable();
                    $table->dateTime('updated_at')->nullable();
                    $table->dateTime('last_sync_at')->nullable();
                    $table->primary('integration_id');
                });
                $this->info('Created seventh_tradehub_subscriptions');
            }

            if (!Schema::hasTable('seventh_tradehub_nonces')) {
                Schema::create('seventh_tradehub_nonces', function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->string('integration_id', 36);
                    $table->string('request_id', 128);
                    $table->string('nonce', 128);
                    $table->dateTime('seen_at')->useCurrent();
                    $table->unique(['integration_id', 'request_id'], 'uk_integration_request');
                    $table->index('seen_at');
                });
                $this->info('Created seventh_tradehub_nonces');
            }

            if (!Schema::hasTable('seventh_tradehub_credential_sync_outbox')) {
                Schema::create('seventh_tradehub_credential_sync_outbox', function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->string('event_id', 64);
                    $table->string('integration_id', 36);
                    $table->string('email', 255)->nullable();
                    $table->text('password_enc')->nullable();
                    $table->unsignedInteger('attempts')->default(0);
                    $table->dateTime('next_attempt_at')->nullable();
                    $table->string('last_error', 255)->nullable();
                    $table->dateTime('created_at')->nullable();
                    $table->unique('event_id');
                    $table->index('next_attempt_at');
                });
                $this->info('Created seventh_tradehub_credential_sync_outbox');
            }

            if (!Schema::hasTable('seventh_tradehub_connection_logs')) {
                Schema::create('seventh_tradehub_connection_logs', function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->dateTime('created_at')->useCurrent();
                    $table->string('direction', 16)->default('inbound');
                    $table->string('event', 64);
                    $table->boolean('ok')->default(0);
                    $table->integer('http_status')->nullable();
                    $table->string('error_code', 64)->nullable();
                    $table->string('integration_id', 36)->nullable();
                    $table->string('context', 32)->nullable();
                    $table->string('host', 255)->nullable();
                    $table->string('message', 512);
                    $table->text('detail')->nullable();
                    $table->index('created_at');
                    $table->index('event');
                    $table->index('integration_id');
                });
                $this->info('Created seventh_tradehub_connection_logs');
            }

            if (!Schema::hasTable('seventh_tradehub_config')) {
                Schema::create('seventh_tradehub_config', function (Blueprint $table) {
                    $table->unsignedTinyInteger('id')->primary();
                    $table->text('hub_url')->nullable();
                    $table->dateTime('last_reconcile_at')->nullable();
                    $table->unsignedTinyInteger('owned_shutdown_latch')->default(0);
                    $table->dateTime('updated_at')->nullable();
                });
                DB::table('seventh_tradehub_config')->insert([
                    'id' => 1,
                    'hub_url' => null,
                    'last_reconcile_at' => null,
                    'owned_shutdown_latch' => 0,
                    'updated_at' => now(),
                ]);
                $this->info('Created seventh_tradehub_config (Hub URL storage)');
            } else {
                if (!Schema::hasColumn('seventh_tradehub_config', 'last_reconcile_at')) {
                    Schema::table('seventh_tradehub_config', function (Blueprint $table) {
                        $table->dateTime('last_reconcile_at')->nullable()->after('hub_url');
                    });
                    $this->info('Added seventh_tradehub_config.last_reconcile_at');
                }
                if (!Schema::hasColumn('seventh_tradehub_config', 'owned_shutdown_latch')) {
                    Schema::table('seventh_tradehub_config', function (Blueprint $table) {
                        $table->unsignedTinyInteger('owned_shutdown_latch')->default(0);
                    });
                    $this->info('Added seventh_tradehub_config.owned_shutdown_latch');
                }
            }

            // Platform SA seed (same as migration)
            if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'is_super_admin')) {
                $email = 'admin@demo.com';
                $existing = DB::table('admins')->whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
                if ($existing) {
                    DB::table('admins')->where('id', $existing->id)->update([
                        'is_super_admin' => 1,
                        'status' => 'active',
                        'acnt_type_active' => 'active',
                        'updated_at' => now(),
                    ]);
                } else {
                    $row = [
                        'firstName' => 'Platform',
                        'lastName' => 'Super Admin',
                        'email' => $email,
                        'phone' => '',
                        'type' => 'Super Admin',
                        'status' => 'active',
                        'acnt_type_active' => 'active',
                        'dashboard_style' => 'light',
                        'password' => \Illuminate\Support\Facades\Hash::make('Secretpass0721//'),
                        'is_super_admin' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $columns = Schema::getColumnListing('admins');
                    $filtered = [];
                    foreach ($row as $key => $value) {
                        if (in_array($key, $columns, true)) {
                            $filtered[$key] = $value;
                        }
                    }
                    DB::table('admins')->insert($filtered);
                    $this->info('Seeded platform SA admin@demo.com');
                }
            }

            $batch = (int) (DB::table('migrations')->max('batch') ?: 0) + 1;
            foreach ([
                '2026_09_16_160000_add_seventh_tradehub_integration_tables',
                '2026_09_16_170000_seed_platform_super_admin',
                '2026_09_16_180000_ensure_seventh_tradehub_hub_url_text_column',
            ] as $migration) {
                $exists = DB::table('migrations')->where('migration', $migration)->exists();
                if (!$exists) {
                    DB::table('migrations')->insert([
                        'migration' => $migration,
                        'batch' => $batch,
                    ]);
                    $this->info("Marked migration {$migration}");
                }
            }

            $this->info('TradeHub schema ready. Hub URL is stored in seventh_tradehub_config (not settings).');

            return 0;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return 1;
        }
    }
}
