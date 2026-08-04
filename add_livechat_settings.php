<?php
/**
 * One-shot: add livechat columns to settings (for hosts where artisan migrate is awkward).
 * Run: php add_livechat_settings.php
 * Delete this file after a successful run.
 */
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (!Schema::hasTable('settings')) {
    echo "settings table missing\n";
    exit(1);
}

$added = [];

if (!Schema::hasColumn('settings', 'livechat_provider')) {
    DB::statement("ALTER TABLE settings ADD COLUMN livechat_provider VARCHAR(32) NULL DEFAULT 'none'");
    $added[] = 'livechat_provider';
}
if (!Schema::hasColumn('settings', 'smartsupp_key')) {
    DB::statement('ALTER TABLE settings ADD COLUMN smartsupp_key VARCHAR(191) NULL');
    $added[] = 'smartsupp_key';
}
if (!Schema::hasColumn('settings', 'chatway_widget_id')) {
    DB::statement('ALTER TABLE settings ADD COLUMN chatway_widget_id VARCHAR(191) NULL');
    $added[] = 'chatway_widget_id';
}

$row = DB::table('settings')->where('id', 1)->first();
if ($row) {
    $updates = [];
    $provider = $row->livechat_provider ?? null;
    if (empty($provider) || $provider === 'none') {
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
        echo "Seeded provider defaults: " . json_encode($updates) . "\n";
    }
}

echo empty($added)
    ? "Columns already present. OK.\n"
    : 'Added columns: ' . implode(', ', $added) . "\n";
