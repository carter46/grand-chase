<?php

namespace App\Http\Controllers\SeventhTradeHub;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\SeventhTradeHub\SeventhTradeHubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsumeController extends Controller
{
    public function __invoke(Request $request, SeventhTradeHubService $hub)
    {
        if (!$request->isMethod('get')) {
            return $this->consumeError('Invalid request method.');
        }

        $token = trim((string) $request->query('token', ''));
        $queryIntegrationId = trim((string) $request->query('integration_id', ''));

        if ($token === '' || $queryIntegrationId === '') {
            return $this->consumeError();
        }

        $integration = $hub->getByIntegrationId($queryIntegrationId);
        if (!$integration || !$hub->isIntegrationOperational($integration)) {
            return $this->consumeError('This integration is not available.');
        }

        $context = trim((string) ($integration['context'] ?? ''));

        if ($context === SeventhTradeHubService::CONTEXT_OWNED && $hub->isOwnedSiteShutdown()) {
            $hub->connectionLog([
                'direction' => 'inbound',
                'event' => 'sso_consume',
                'ok' => false,
                'http_status' => 403,
                'error_code' => 'shutdown_active',
                'integration_id' => $queryIntegrationId,
                'context' => $context,
                'message' => 'SSO refused — owned shutdown is ACTIVE',
            ]);
            // Axion: no session; show status-specific Hub CTA (not public "Session expired").
            return response()->view('errors.hub-admin-offline', $hub->adminOfflineCopy(), 200);
        }

        $result = $hub->validateToken($token, $integration);
        if (empty($result['valid']) || !is_array($result['data'] ?? null)) {
            $hub->connectionLog([
                'direction' => 'inbound',
                'event' => 'sso_consume',
                'ok' => false,
                'http_status' => 401,
                'error_code' => 'invalid_token',
                'integration_id' => $queryIntegrationId,
                'context' => $context,
                'message' => 'SSO refused — token validation failed',
            ]);
            return $this->consumeError();
        }

        $validated = $result['data'];
        $responseIntegrationId = trim((string) ($validated['integration_id'] ?? ''));
        $responseContext = trim((string) ($validated['context'] ?? ''));

        if ($responseIntegrationId === '' || !hash_equals($queryIntegrationId, $responseIntegrationId)) {
            return $this->consumeError();
        }
        if ($responseContext === '' || !hash_equals($context, $responseContext)) {
            return $this->consumeError();
        }
        if (!$hub->validateResponseIsFresh($validated)) {
            return $this->consumeError();
        }

        $hubRole = strtolower(trim((string) ($validated['role'] ?? 'user')));
        $email = trim((string) ($validated['identity']['email'] ?? ''));

        if ($context === SeventhTradeHubService::CONTEXT_OWNED && $hubRole !== 'admin') {
            return $this->consumeError('Admin login only for owned tools.');
        }

        $resolved = $hub->resolveLocalIdentity($email, $hubRole, $context);
        if (!empty($resolved['error']) || empty($resolved['model'])) {
            $hub->connectionLog([
                'direction' => 'inbound',
                'event' => 'sso_consume',
                'ok' => false,
                'http_status' => 403,
                'error_code' => $resolved['error'] ?? 'resolve_failed',
                'integration_id' => $queryIntegrationId,
                'context' => $context,
                'message' => 'SSO resolve failed: ' . ($resolved['error'] ?? 'unknown'),
            ]);
            return $this->consumeError('No matching account for this Hub login.');
        }

        $request->session()->regenerate();
        $request->session()->put('hub_sso_login', 1);
        $request->session()->put('hub_sso_context', $context);

        if (($resolved['type'] ?? '') === 'admin') {
            /** @var Admin $admin */
            $admin = $resolved['model'];
            Auth::guard('admin')->login($admin, false);
            // Mark 2FA satisfied for Hub SSO
            if (($admin->enable_2fa ?? '') === 'enabled') {
                Admin::where('id', $admin->id)->update(['pass_2fa' => 'true']);
            }
            $hub->connectionLog([
                'direction' => 'inbound',
                'event' => 'sso_consume',
                'ok' => true,
                'http_status' => 200,
                'integration_id' => $queryIntegrationId,
                'context' => $context,
                'message' => 'Admin SSO success for ' . $email,
            ]);
            return redirect('/admin/dashboard?hub_sso=1');
        }

        Auth::guard('web')->login($resolved['model'], false);
        $userModel = $resolved['model'];
        if (empty($userModel->email_verified_at)) {
            $userModel->forceFill(['email_verified_at' => now()])->save();
        }
        $hub->connectionLog([
            'direction' => 'inbound',
            'event' => 'sso_consume',
            'ok' => true,
            'http_status' => 200,
            'integration_id' => $queryIntegrationId,
            'context' => $context,
            'message' => 'User SSO success for ' . $email,
        ]);

        return redirect('/dashboard?hub_sso=1');
    }

    private function consumeError($message = '')
    {
        return response()->view('errors.hub-consume-error', [
            'message' => $message !== ''
                ? $message
                : 'This login link has expired or is invalid. Return to 7th Trade Hub and try again.',
        ], 403);
    }
}
