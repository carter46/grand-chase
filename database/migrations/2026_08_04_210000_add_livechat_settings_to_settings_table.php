<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLivechatSettingsToSettingsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        // Use short string / TEXT to avoid MySQL "Row size too large" on wide settings tables
        if (!Schema::hasColumn('settings', 'livechat_provider')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('livechat_provider', 32)->nullable()->default('none');
            });
        }
        if (!Schema::hasColumn('settings', 'smartsupp_key')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->text('smartsupp_key')->nullable();
            });
        }
        if (!Schema::hasColumn('settings', 'chatway_widget_id')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->text('chatway_widget_id')->nullable();
            });
        }

        // Preserve previous hardcoded Smartsupp embed until admin changes it
        $row = DB::table('settings')->where('id', 1)->first();
        if ($row) {
            $updates = [];
            if (empty($row->livechat_provider) || $row->livechat_provider === 'none') {
                if (!empty($row->tido)) {
                    $updates['livechat_provider'] = 'tidio';
                } else {
                    $updates['livechat_provider'] = 'smartsupp';
                    if (empty($row->smartsupp_key ?? null)) {
                        $updates['smartsupp_key'] = '981552f3acb735de4aff60e78c1154b36c17fa30';
                    }
                }
            }
            if (!empty($updates)) {
                DB::table('settings')->where('id', 1)->update($updates);
            }
        }
    }

    public function down()
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('settings', 'chatway_widget_id')) {
                $cols[] = 'chatway_widget_id';
            }
            if (Schema::hasColumn('settings', 'smartsupp_key')) {
                $cols[] = 'smartsupp_key';
            }
            if (Schema::hasColumn('settings', 'livechat_provider')) {
                $cols[] = 'livechat_provider';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
}
