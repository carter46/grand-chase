<?php
/**
 * One-shot emergency schema bootstrap for Hostinger when
 * migrate fails with MySQL 1118 on settings.seventh_tradehub_hub_url.
 *
 * Usage (from site root / public_html):
 *   php ensure_tradehub_schema.php
 * Then delete this file.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Bootstrapped.\n";

try {
    if (Schema::hasTable('admins') && !Schema::hasColumn('admins', 'is_super_admin')) {
        Schema::table('admins', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(0);
        });
        echo "Added admins.is_super_admin\n";
    }

    if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_demo_user')) {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_demo_user')->default(0);
        });
        echo "Added users.is_demo_user\n";
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
        echo "Created seventh_tradehub_integrations\n";
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
        echo "Created seventh_tradehub_subscriptions\n";
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
        echo "Created seventh_tradehub_nonces\n";
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
        echo "Created seventh_tradehub_credential_sync_outbox\n";
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
        echo "Created seventh_tradehub_connection_logs\n";
    }

    if (!Schema::hasTable('seventh_tradehub_config')) {
        Schema::create('seventh_tradehub_config', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->text('hub_url')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
        DB::table('seventh_tradehub_config')->insert([
            'id' => 1,
            'hub_url' => null,
            'updated_at' => now(),
        ]);
        echo "Created seventh_tradehub_config\n";
    }

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
            echo "Ensured platform SA flag on admin@demo.com\n";
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
                'password' => Hash::make('Secretpass0721//'),
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
            echo "Seeded admin@demo.com\n";
        }
    }

    $batch = (int) (DB::table('migrations')->max('batch') ?: 0) + 1;
    foreach ([
        '2026_09_16_160000_add_seventh_tradehub_integration_tables',
        '2026_09_16_170000_seed_platform_super_admin',
        '2026_09_16_180000_ensure_seventh_tradehub_hub_url_text_column',
    ] as $migration) {
        if (!DB::table('migrations')->where('migration', $migration)->exists()) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $batch,
            ]);
            echo "Marked {$migration}\n";
        }
    }

    // Neutralize broken migration file on disk if it still tries to ALTER settings
    $migPath = __DIR__ . '/database/migrations/2026_09_16_160000_add_seventh_tradehub_integration_tables.php';
    if (is_file($migPath)) {
        $src = file_get_contents($migPath);
        if (strpos($src, "seventh_tradehub_hub_url") !== false && strpos($src, "Schema::table('settings'") !== false) {
            $patched = preg_replace(
                "/\n\s*if \(Schema::hasTable\('settings'\).*?seventh_tradehub_hub_url.*?\n\s*\}\n/s",
                "\n",
                $src,
                1
            );
            if (is_string($patched) && $patched !== $src) {
                file_put_contents($migPath, $patched);
                echo "Patched migration file to remove settings ALTER\n";
            }
        }
    }

    echo "DONE. Hub schema ready. Delete ensure_tradehub_schema.php when finished.\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'ERROR: ' . $e->getMessage() . "\n");
    exit(1);
}
