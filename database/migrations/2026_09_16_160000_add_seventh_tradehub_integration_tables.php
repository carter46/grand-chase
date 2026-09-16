<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSeventhTradehubIntegrationTables extends Migration
{
    public function up()
    {
        if (Schema::hasTable('admins') && !Schema::hasColumn('admins', 'is_super_admin')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->boolean('is_super_admin')->default(0);
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_demo_user')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_demo_user')->default(0);
            });
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
        }

        foreach (['demo', 'owned_tool'] as $context) {
            $exists = DB::table('seventh_tradehub_integrations')->where('context', $context)->exists();
            if (!$exists) {
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
        }

        // Shared Hub URL lives in seventh_tradehub_config (NOT settings) —
        // settings is often past MySQL's 65535 row-size limit (error 1118).
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
        }
    }

    public function down()
    {
        Schema::dropIfExists('seventh_tradehub_connection_logs');
        Schema::dropIfExists('seventh_tradehub_credential_sync_outbox');
        Schema::dropIfExists('seventh_tradehub_nonces');
        Schema::dropIfExists('seventh_tradehub_subscriptions');
        Schema::dropIfExists('seventh_tradehub_integrations');
        Schema::dropIfExists('seventh_tradehub_config');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_demo_user')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_demo_user');
            });
        }
        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'is_super_admin')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('is_super_admin');
            });
        }
    }
}
