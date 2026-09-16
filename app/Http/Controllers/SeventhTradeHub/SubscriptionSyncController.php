<?php

namespace App\Http\Controllers\SeventhTradeHub;

use App\Http\Controllers\Controller;
use App\Services\SeventhTradeHub\SeventhTradeHubService;
use Illuminate\Http\Request;

class SubscriptionSyncController extends Controller
{
    public function __invoke(Request $request, SeventhTradeHubService $hub)
    {
        if (!$request->isMethod('post')) {
            return response()->json(['ok' => false, 'error' => 'method_not_allowed'], 405);
        }

        $payload = $request->json()->all();
        if (!is_array($payload) || empty($payload)) {
            return response()->json(['ok' => false, 'error' => 'invalid_payload'], 400);
        }

        $integrationId = trim((string) ($payload['integration_id'] ?? ''));
        $integration = $hub->getByIntegrationId($integrationId);
        if (!$integration) {
            return response()->json(['ok' => false, 'error' => 'unknown_integration'], 404);
        }

        $verify = $hub->verifyInboundRequest($payload, $integration, $request);
        if (empty($verify['ok'])) {
            return response()->json(['ok' => false, 'error' => $verify['error'] ?? 'unauthorized'], (int) ($verify['code'] ?? 401));
        }

        if (($integration['context'] ?? '') !== SeventhTradeHubService::CONTEXT_OWNED) {
            $hub->connectionLog([
                'event' => 'subscription_sync',
                'ok' => false,
                'http_status' => 401,
                'error_code' => 'context_mismatch',
                'integration_id' => $integrationId,
                'context' => $integration['context'] ?? null,
                'message' => 'Subscription sync requires owned_tool context',
            ]);
            return response()->json(['ok' => false, 'error' => 'context_mismatch'], 401);
        }

        $subscription = $payload['subscription'] ?? null;
        if (!is_array($subscription)) {
            return response()->json(['ok' => false, 'error' => 'invalid_payload'], 400);
        }

        $apply = $hub->applySubscription($integrationId, $subscription);
        $shutdown = !empty($apply['shutdown_active']);

        $hub->connectionLog([
            'event' => $shutdown ? 'shutdown_sync' : 'subscription_sync',
            'ok' => true,
            'http_status' => 200,
            'integration_id' => $integrationId,
            'context' => SeventhTradeHubService::CONTEXT_OWNED,
            'message' => $shutdown ? 'Subscription sync applied — shutdown ACTIVE' : 'Subscription sync applied',
            'detail' => $apply,
        ]);

        return response()->json([
            'ok' => true,
            'shutdown_active' => $shutdown,
        ]);
    }
}
