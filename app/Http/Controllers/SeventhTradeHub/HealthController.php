<?php

namespace App\Http\Controllers\SeventhTradeHub;

use App\Http\Controllers\Controller;
use App\Services\SeventhTradeHub\SeventhTradeHubService;
use Illuminate\Http\Request;

class HealthController extends Controller
{
    public function __invoke(Request $request, SeventhTradeHubService $hub)
    {
        if (!$request->isMethod('post')) {
            return response()->json(['ok' => false, 'error' => 'method_not_allowed'], 405);
        }

        $payload = $request->json()->all();
        if (!is_array($payload) || empty($payload)) {
            $hub->connectionLog([
                'event' => 'health',
                'ok' => false,
                'http_status' => 400,
                'error_code' => 'invalid_payload',
                'message' => 'Invalid JSON payload',
            ]);
            return response()->json(['ok' => false, 'error' => 'invalid_payload'], 400);
        }

        $integrationId = trim((string) ($payload['integration_id'] ?? ''));
        $integration = $hub->getByIntegrationId($integrationId);
        if (!$integration) {
            $hub->connectionLog([
                'event' => 'health',
                'ok' => false,
                'http_status' => 404,
                'error_code' => 'unknown_integration',
                'integration_id' => $integrationId,
                'message' => 'Unknown integration_id',
            ]);
            return response()->json(['ok' => false, 'error' => 'unknown_integration'], 404);
        }

        $verify = $hub->verifyInboundRequest($payload, $integration, $request);
        if (empty($verify['ok'])) {
            return response()->json(['ok' => false, 'error' => $verify['error'] ?? 'unauthorized'], (int) ($verify['code'] ?? 401));
        }

        $context = trim((string) ($integration['context'] ?? ''));
        $hub->connectionLog([
            'event' => 'health',
            'ok' => true,
            'http_status' => 200,
            'integration_id' => $integrationId,
            'context' => $context,
            'message' => 'Health check OK',
        ]);

        return response()->json([
            'ok' => true,
            'capabilities' => $hub->capabilitiesForContext($context),
        ]);
    }
}
