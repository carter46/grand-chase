<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Impersonation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function leave(Request $request)
    {
        $userId = Impersonation::stop($request);

        if (!Auth::guard('admin')->check()) {
            return redirect()->route('adminloginform')
                ->with('status', 'Your admin session has ended. Please log in again.');
        }

        if ($userId) {
            return redirect()->route('viewuser', $userId)->with('success', 'You are back in the admin panel.');
        }

        return redirect()->route('admin.dashboard');
    }
}
