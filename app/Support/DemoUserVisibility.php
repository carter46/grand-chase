<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Schema;

class DemoUserVisibility
{
    /**
     * Hide Hub demo users from peer-facing lists and stats.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public static function excludeFromQuery($query)
    {
        if (!Schema::hasColumn('users', 'is_demo_user')) {
            return $query;
        }

        return $query->where(function ($q) {
            $q->where('is_demo_user', 0)->orWhereNull('is_demo_user');
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|null $query
     * @return int
     */
    public static function count($query = null)
    {
        $query = $query ?: User::query();

        return self::excludeFromQuery($query)->count();
    }

    /**
     * Whether this user is a Hub demo account.
     *
     * @param \App\Models\User|array|null $user
     * @return bool
     */
    public static function isDemo($user)
    {
        if (!$user || !Schema::hasColumn('users', 'is_demo_user')) {
            return false;
        }
        if (is_array($user)) {
            return !empty($user['is_demo_user']);
        }

        return !empty($user->is_demo_user);
    }

    /**
     * Peers may not view/mutate demo users. Platform SA may.
     *
     * @param \App\Models\User|null $user
     * @return void
     */
    public static function assertPeerMayAccessUser($user)
    {
        if (!$user || !self::isDemo($user)) {
            return;
        }
        if (PlatformSuperAdmin::check()) {
            return;
        }
        abort(403, 'Demo users are managed from 7th Trade Hub settings.');
    }

    /**
     * @param \App\Models\User|null $user
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public static function denyPeerAccessRedirect($user)
    {
        if (!$user || !self::isDemo($user)) {
            return null;
        }
        if (PlatformSuperAdmin::check()) {
            return null;
        }

        return redirect()->back()->with('message', 'Demo users are managed from 7th Trade Hub settings.');
    }

    /**
     * whereHas helper for relations named user/duser/tuser.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $relation
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function whereHasNonDemo($query, $relation = 'user')
    {
        if (!Schema::hasColumn('users', 'is_demo_user')) {
            return $query;
        }

        return $query->whereHas($relation, function ($q) {
            self::excludeFromQuery($q);
        });
    }
}
