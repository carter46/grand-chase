<?php
/**
 * Add livechat columns to settings table.
 * Upload next to artisan, open once in browser or: php add_livechat_settings.php
 * DELETE this file after success.
 */
header('Content-Type: text/plain; charset=utf-8');

try {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    if (!Illuminate\Support\Facades\Schema::hasTable('settings')) {
        echo "ERROR: settings table missing\n";
        exit(1);
    }

    $added = [];

    if (!Illuminate\Support\Facades\Schema::hasColumn('settings', 'livechat_provider')) {
        Illuminate\Support\Facades\DB::statement("ALTER TABLE `settings` ADD COLUMN `livechat_provider` VARCHAR(32) NULL DEFAULT 'none'");
        $added[] = 'livechat_provider';
    }
    if (!Illuminate\Support\Facades\Schema::hasColumn('settings', 'smartsupp_key')) {
        Illuminate\Support\Facades\DB::statement("ALTER TABLE `settings` ADD COLUMN `smartsupp_key` VARCHAR(191) NULL");
        $added[] = 'smartsupp_key';
    }
    if (!Illuminate\Support\Facades\Schema::hasColumn('settings', 'chatway_widget_id')) {
        Illuminate\Support\Facades\DB::statement("ALTER TABLE `settings` ADD COLUMN `chatway_widget_id` VARCHAR(191) NULL");
        $added[] = 'chatway_widget_id';
    }

    $row = Illuminate\Support\Facades\DB::table('settings')->where('id', 1)->first();
    if ($row && Illuminate\Support\Facades\Schema::hasColumn('settings', 'livechat_provider')) {
        $provider = $row->livechat_provider ?? null;
        if (empty($provider) || $provider === 'none') {
            $updates = [];
            if (!empty($row->tido)) {
                $updates['livechat_provider'] = 'tidio';
            } else {
                $updates['livechat_provider'] = 'smartsupp';
                if (Illuminate\Support\Facades\Schema::hasColumn('settings', 'smartsupp_key') && empty($row->smartsupp_key ?? null)) {
                    $updates['smartsupp_key'] = '981552f3acb735de4aff60e78c1154b36c17fa30';
                }
            }
            if (!empty($updates)) {
                Illuminate\Support\Facades\DB::table('settings')->where('id', 1)->update($updates);
                echo 'Seeded defaults: ' . json_encode($updates) . "\n";
            }
        }
    }

    if (empty($added)) {
        echo "OK — columns already exist.\n";
    } else {
        echo 'OK — added: ' . implode(', ', $added) . "\n";
    }
    echo "Reload admin and save logo again. Then DELETE add_livechat_settings.php\n";
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
    exit(1);
}
