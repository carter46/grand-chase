<?php

namespace App\Services;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminDatabaseAutoMigrate
{
    /** @var bool */
    private static $ranThisRequest = false;

    /**
     * Run pending Laravel migrations once per request when an admin loads a page.
     *
     * @param int|null $adminId
     * @return array<string, mixed>
     */
    public function run($adminId = null)
    {
        if (self::$ranThisRequest) {
            return session('auto_migration_last_result', $this->emptyResult(false));
        }
        self::$ranThisRequest = true;

        $result = $this->emptyResult(true);
        $adminId = $adminId ?? Auth::guard('admin')->id();

        try {
            $pending = $this->getPendingMigrationNames();

            if (empty($pending)) {
                session()->forget('auto_migration_errors');
                session(['auto_migration_last_result' => $result]);

                return $result;
            }

            $lock = Cache::lock('admin-database-auto-migrate', 120);

            if (!$lock->get()) {
                $result['errors'][] = 'Database update is running in another request. Reload this page in a moment.';
                session(['auto_migration_errors' => $result['errors']]);
                session(['auto_migration_last_result' => $result]);

                return $result;
            }

            try {
                Artisan::call('migrate', ['--force' => true]);

                $stillPending = $this->getPendingMigrationNames();
                $applied = array_values(array_diff($pending, $stillPending));
                $failed = array_values(array_intersect($pending, $stillPending));

                foreach ($applied as $name) {
                    $result['applied'][] = [
                        'id' => $name,
                        'description' => $this->describeMigration($name),
                    ];
                }

                foreach ($failed as $name) {
                    $result['failed'][] = [
                        'id' => $name,
                        'description' => $this->describeMigration($name),
                        'error' => 'Migration did not complete. Check storage/logs/laravel.log.',
                    ];
                    $result['errors'][] = $name . ': Migration did not complete.';
                }

                if (!empty($result['applied'])) {
                    session([
                        'auto_migration_success' => array_map(function ($row) {
                            return ($row['description'] ?? $row['id']) . ' (' . $row['id'] . ')';
                        }, $result['applied']),
                    ]);
                } else {
                    session()->forget('auto_migration_success');
                }

                if (!empty($result['errors'])) {
                    session(['auto_migration_errors' => $result['errors']]);
                    Log::error('Admin auto-migrate incomplete', [
                        'admin_id' => $adminId,
                        'errors' => $result['errors'],
                        'output' => trim(Artisan::output()),
                    ]);
                } else {
                    session()->forget('auto_migration_errors');
                }
            } finally {
                optional($lock)->release();
            }
        } catch (Throwable $e) {
            Log::error('Admin auto-migrate failed: ' . $e->getMessage(), [
                'admin_id' => $adminId,
                'exception' => $e,
            ]);
            $result['errors'][] = $e->getMessage();
            session(['auto_migration_errors' => $result['errors']]);
            session()->forget('auto_migration_success');
        }

        session(['auto_migration_last_result' => $result]);

        return $result;
    }

    /**
     * @return array<int, string>
     */
    private function getPendingMigrationNames()
    {
        /** @var Migrator $migrator */
        $migrator = app('migrator');
        $files = $migrator->getMigrationFiles([database_path('migrations')]);
        $ran = $migrator->getRepository()->getRan();

        return array_values(array_diff(array_keys($files), $ran));
    }

    private function describeMigration($name)
    {
        return str_replace('_', ' ', preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', (string) $name));
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyResult($ran)
    {
        return [
            'ran' => (bool) $ran,
            'applied' => [],
            'failed' => [],
            'skipped' => 0,
            'errors' => [],
        ];
    }
}
