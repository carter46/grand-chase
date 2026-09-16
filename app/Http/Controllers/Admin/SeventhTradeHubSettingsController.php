<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Settings;
use App\Models\User;
use App\Services\SeventhTradeHub\SeventhTradeHubService;
use App\Support\PlatformSuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SeventhTradeHubSettingsController extends Controller
{
    public function index(SeventhTradeHubService $hub)
    {
        $admin = Auth::guard('admin')->user();
        PlatformSuperAdmin::maybeBootstrapFromEnv();

        if (!PlatformSuperAdmin::check($admin) && !PlatformSuperAdmin::canShowClaim($admin)) {
            abort(403, 'Platform super admin access required.');
        }

        $summary = PlatformSuperAdmin::check($admin) ? $hub->adminSummary() : null;

        return view('admin.seventh-tradehub.settings', [
            'title' => '7th Trade Hub',
            'settings' => Settings::where('id', 1)->first(),
            'summary' => $summary,
            'canClaim' => PlatformSuperAdmin::canShowClaim($admin),
            'isPlatformSa' => PlatformSuperAdmin::check($admin),
            'logs' => PlatformSuperAdmin::check($admin) ? $hub->listConnectionLogs(40) : [],
        ]);
    }

    public function claim(Request $request)
    {
        $result = PlatformSuperAdmin::claim(Auth::guard('admin')->user());
        return redirect()->route('admin.seventh-tradehub.settings')
            ->with($result['ok'] ? 'success' : 'message', $result['message']);
    }

    public function save(Request $request, SeventhTradeHubService $hub)
    {
        $this->requirePlatformSa();

        if ($request->filled('hub_url')) {
            $hub->saveHubUrl($request->input('hub_url'));
        }

        $context = $request->input('context');
        if (!in_array($context, [SeventhTradeHubService::CONTEXT_DEMO, SeventhTradeHubService::CONTEXT_OWNED], true)) {
            return back()->with('message', 'Invalid context.');
        }

        $result = $hub->saveIntegration($context, [
            'enabled' => $request->boolean('enabled'),
            'integration_id' => $request->input('integration_id'),
            'client_id' => $request->input('client_id'),
            'client_secret' => $request->input('client_secret'),
            'webhook_secret' => $request->input('webhook_secret'),
            'expected_user_email' => $request->input('expected_user_email'),
            'expected_admin_email' => $request->input('expected_admin_email'),
        ], Auth::guard('admin')->id());

        return back()->with($result['ok'] ? 'success' : 'message', $result['ok'] ? 'Integration saved.' : ($result['error'] ?? 'Save failed'));
    }

    public function webhookPing(Request $request, SeventhTradeHubService $hub)
    {
        $this->requirePlatformSa();
        $context = $request->input('context', SeventhTradeHubService::CONTEXT_DEMO);
        $integration = $hub->getByContext($context);
        $result = $hub->webhookPing($integration ?: []);
        return back()->with($result['ok'] ? 'success' : 'message', $result['message'] ?? 'Ping finished');
    }

    public function pollOwned(SeventhTradeHubService $hub)
    {
        $this->requirePlatformSa();
        $owned = $hub->getByContext(SeventhTradeHubService::CONTEXT_OWNED);
        $body = $hub->pollSubscription($owned ?: []);
        if ($body === null) {
            return back()->with('message', 'Poll failed — check Owned credentials and Hub URL.');
        }
        return back()->with('success', 'Subscription polled. Status: ' . ($body['status'] ?? 'unknown'));
    }

    public function syncPassword(Request $request, SeventhTradeHubService $hub)
    {
        $this->requirePlatformSa();
        $result = $hub->manualSyncOwnedAdminPassword((string) $request->input('password', ''));
        return back()->with($result['ok'] ? 'success' : 'message', $result['message'] ?? 'Sync finished');
    }

    public function createDemoUser(Request $request)
    {
        $this->requirePlatformSa();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|max:255',
        ]);

        $user = new User();
        $user->forceFill([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'username' => Str::slug(explode('@', $data['email'])[0]) . rand(100, 999),
            'status' => 'active',
            'is_demo_user' => 1,
            'email_verified_at' => now(),
            'account_verify' => 'Verified',
            'pinstatus' => 0,
        ]);
        $user->save();

        return back()->with('success', 'Demo user created: ' . $user->email);
    }

    public function createDemoAdmin(Request $request)
    {
        $this->requirePlatformSa();
        $data = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email',
            'password' => 'required|string|min:6|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $admin = new Admin();
        $admin->forceFill([
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'email' => strtolower($data['email']),
            'phone' => $data['phone'] ?? '',
            'password' => Hash::make($data['password']),
            'type' => 'Admin',
            'status' => 'active',
            'acnt_type_active' => 'active',
            'is_super_admin' => 0,
            'dashboard_style' => 'light',
        ]);
        $admin->save();

        return back()->with('success', 'Demo admin created: ' . $admin->email . ' (not platform super admin)');
    }

    private function requirePlatformSa()
    {
        PlatformSuperAdmin::maybeBootstrapFromEnv();
        if (!PlatformSuperAdmin::check()) {
            abort(403, 'Platform super admin access required.');
        }
    }
}
