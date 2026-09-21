<?php

namespace App\Support;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class PlatformSuperAdmin
{
    /**
     * @param \App\Models\Admin|null $admin
     * @return bool
     */
    public static function check($admin = null)
    {
        $admin = $admin ?: Auth::guard('admin')->user();
        if (!$admin) {
            return false;
        }

        return !empty($admin->is_super_admin);
    }

    /**
     * Deny peer mutation of a platform super admin.
     * Call at the top of every admin-by-id mutator.
     *
     * @param \App\Models\Admin|null $target
     * @param string $action
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public static function assertCanMutateAdmin($target, $action = 'mutate')
    {
        if (!$target) {
            return;
        }

        if (!Schema::hasColumn('admins', 'is_super_admin')) {
            return;
        }

        if (empty($target->is_super_admin)) {
            return;
        }

        // Never allow deleting the platform SA (even by another platform SA).
        if ($action === 'delete') {
            abort(403, 'Cannot delete platform super admin.');
        }

        if (!self::check()) {
            abort(403, 'Not allowed to act on platform super admin.');
        }
    }

    /**
     * Soft redirect variant for legacy GET mutators that expect flash messages.
     *
     * @param \App\Models\Admin|null $target
     * @param string $action
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public static function denyMutateRedirect($target, $action = 'mutate')
    {
        if (!$target || !Schema::hasColumn('admins', 'is_super_admin') || empty($target->is_super_admin)) {
            return null;
        }

        if ($action === 'delete') {
            return redirect()->back()->with('message', 'Cannot delete platform super admin.');
        }

        if (!self::check()) {
            return redirect()->back()->with('message', 'Not allowed to act on platform super admin.');
        }

        return null;
    }

    /**
     * Promote matching admin via env when no platform SA exists yet.
     *
     * @return void
     */
    public static function maybeBootstrapFromEnv()
    {
        try {
            if (!Schema::hasColumn('admins', 'is_super_admin')) {
                return;
            }

            if (Admin::where('is_super_admin', 1)->exists()) {
                return;
            }

            $email = trim((string) (env('SEVENTH_TRADEHUB_SUPER_ADMIN_EMAIL') ?: getenv('SEVENTH_TRADEHUB_SUPER_ADMIN_EMAIL') ?: ''));
            if ($email === '') {
                return;
            }

            $admin = Admin::whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
            if ($admin) {
                $admin->is_super_admin = 1;
                $admin->save();
            }
        } catch (\Throwable $e) {
            // Fail open — missing migration/DB must not 500 the whole site.
        }
    }

    /**
     * One-time claim by current type=Super Admin when none exist.
     *
     * @param \App\Models\Admin $admin
     * @return array{ok: bool, message: string}
     */
    public static function claim(Admin $admin)
    {
        if (!Schema::hasColumn('admins', 'is_super_admin')) {
            return ['ok' => false, 'message' => 'Database not migrated yet. Reload any admin page to apply migrations.'];
        }

        if (Admin::where('is_super_admin', 1)->exists()) {
            return ['ok' => false, 'message' => 'A platform super admin already exists.'];
        }

        if (($admin->type ?? '') !== 'Super Admin') {
            return ['ok' => false, 'message' => 'Only a site Super Admin can claim platform super admin.'];
        }

        $admin->is_super_admin = 1;
        $admin->save();

        return ['ok' => true, 'message' => 'You are now the platform super admin for 7th Trade Hub.'];
    }

    /**
     * Whether claim UI should show.
     *
     * @param \App\Models\Admin|null $admin
     * @return bool
     */
    public static function canShowClaim($admin = null)
    {
        if (!Schema::hasColumn('admins', 'is_super_admin')) {
            return false;
        }
        $admin = $admin ?: Auth::guard('admin')->user();
        if (!$admin || ($admin->type ?? '') !== 'Super Admin') {
            return false;
        }
        if (!empty($admin->is_super_admin)) {
            return false;
        }

        return !Admin::where('is_super_admin', 1)->exists();
    }

    /**
     * Query scope helper: hide platform SA from peers.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \App\Models\Admin|null $viewer
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filterAdminsForViewer($query, $viewer = null)
    {
        if (!Schema::hasColumn('admins', 'is_super_admin')) {
            return $query;
        }
        if (self::check($viewer)) {
            return $query;
        }

        return $query->where(function ($q) {
            $q->where('is_super_admin', 0)->orWhereNull('is_super_admin');
        });
    }

    /**
     * Only platform Super Admin may manage admin accounts, add admins,
     * or change admin passwords/emails. Regular site admins cannot.
     *
     * @param \App\Models\Admin|null $admin
     * @return bool
     */
    public static function canManageAdmins($admin = null)
    {
        return self::check($admin);
    }

    /**
     * Redirect when a regular admin hits admin-management or self-password routes.
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public static function denyUnlessCanManageAdmins()
    {
        if (self::canManageAdmins()) {
            return null;
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('message', 'Only a Super Admin can manage administrator accounts or change admin passwords.');
    }
}
