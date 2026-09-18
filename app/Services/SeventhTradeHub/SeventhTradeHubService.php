<?php

namespace App\Services\SeventhTradeHub;

use App\Models\Admin;
use App\Models\Settings;
use App\Models\User;
use App\Services\AdminDatabaseAutoMigrate;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Throwable;

/**
 * 7th Trade Hub Protocol v1 — Laravel port of Axion Trust Bank seventh-tradehub.php.
 *
 * Auth mapping (grand-chase):
 * - Demo user SSO → User where is_demo_user=1
 * - Admin SSO → Admin where is_super_admin=0
 * - Platform SA → Admin is_super_admin=1 (SSO refused)
 */
class SeventhTradeHubService
{
    const CONTEXT_DEMO = 'demo';
    const CONTEXT_OWNED = 'owned_tool';

    /** @var bool */
    private static $schemaChecked = false;

    /** @var bool */
    private static $connectionLogsReady = false;

    /** @var bool */
    private static $credentialOutboxReady = false;

    /** @var bool Request-scoped reconcile guard (Axion static $done parity). */
    private static $reconcileAttemptedThisRequest = false;

    const MAX_TRUST_AGE_SECONDS = 86400; // 24h
    const RECONCILE_INTERVAL_SECONDS = 900; // 15m

    /**
     * Ensure Hub tables / context rows exist (best-effort).
     *
     * @return void
     */
    public function ensureSchema()
    {
        if (self::$schemaChecked) {
            return;
        }

        try {
            if (!Schema::hasTable('seventh_tradehub_integrations')) {
                // Public traffic must not migrate. Admin Settings / admin.automigrate create tables.
                // Do not mark checked — next request after migrate can seed context rows.
                return;
            }
            self::$schemaChecked = true;
            $this->ensureContextRows();
            $this->ensureConnectionLogsTable();
            $this->ensureConfigTable();
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub ensureSchema: ' . $e->getMessage());
        }
    }

    /**
     * Tiny table for shared Hub URL (avoids settings row-size limit).
     *
     * @return void
     */
    public function ensureConfigTable()
    {
        try {
            if (!Schema::hasTable('seventh_tradehub_config')) {
                Schema::create('seventh_tradehub_config', function ($table) {
                    $table->unsignedTinyInteger('id')->primary();
                    $table->text('hub_url')->nullable();
                    $table->dateTime('last_reconcile_at')->nullable();
                    $table->unsignedTinyInteger('owned_shutdown_latch')->default(0);
                    $table->dateTime('updated_at')->nullable();
                });
                DB::table('seventh_tradehub_config')->insert([
                    'id' => 1,
                    'hub_url' => null,
                    'last_reconcile_at' => null,
                    'owned_shutdown_latch' => 0,
                    'updated_at' => now(),
                ]);

                return;
            }

            if (!Schema::hasColumn('seventh_tradehub_config', 'last_reconcile_at')) {
                Schema::table('seventh_tradehub_config', function ($table) {
                    $table->dateTime('last_reconcile_at')->nullable()->after('hub_url');
                });
            }
            if (!Schema::hasColumn('seventh_tradehub_config', 'owned_shutdown_latch')) {
                Schema::table('seventh_tradehub_config', function ($table) {
                    $table->unsignedTinyInteger('owned_shutdown_latch')->default(0)->after('last_reconcile_at');
                });
            }
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub ensureConfigTable: ' . $e->getMessage());
        }
    }

    /**
     * Shared Hub base URL (env override, then settings id=1).
     *
     * @return string
     */
    public function hubUrl()
    {
        $env = trim((string) (env('SEVENTH_TRADEHUB_HUB_URL') ?: getenv('SEVENTH_TRADEHUB_HUB_URL') ?: ''));
        if ($env !== '') {
            return rtrim($env, '/');
        }

        try {
            if (Schema::hasTable('seventh_tradehub_config')) {
                $url = trim((string) (DB::table('seventh_tradehub_config')->where('id', 1)->value('hub_url') ?? ''));
                if ($url !== '') {
                    return rtrim($url, '/');
                }
            }
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub hubUrl config: ' . $e->getMessage());
        }

        // Legacy fallback if an older install stored it on settings
        try {
            if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'seventh_tradehub_hub_url')) {
                $settings = Settings::where('id', 1)->first();
                if ($settings) {
                    $url = trim((string) ($settings->seventh_tradehub_hub_url ?? ''));
                    if ($url !== '') {
                        return rtrim($url, '/');
                    }
                }
            }
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub hubUrl: ' . $e->getMessage());
        }

        return '';
    }

    /**
     * @param string $url
     * @return bool
     */
    public function saveHubUrl($url)
    {
        $url = rtrim(trim((string) $url), '/');
        try {
            if (!Schema::hasTable('seventh_tradehub_config')) {
                $this->ensureConfigTable();
            }

            $payload = [
                'hub_url' => $url !== '' ? $url : null,
                'updated_at' => now(),
            ];

            if (DB::table('seventh_tradehub_config')->where('id', 1)->exists()) {
                DB::table('seventh_tradehub_config')->where('id', 1)->update($payload);
            } else {
                DB::table('seventh_tradehub_config')->insert(array_merge(['id' => 1], $payload));
            }

            return true;
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub saveHubUrl: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * @param string $context
     * @return array<string, mixed>|null
     */
    public function getByContext($context)
    {
        if (!in_array($context, [self::CONTEXT_DEMO, self::CONTEXT_OWNED], true)) {
            return null;
        }
        $this->ensureSchema();
        try {
            $row = DB::table('seventh_tradehub_integrations')->where('context', $context)->first();

            return $row ? (array) $row : null;
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub getByContext: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * @param string $integrationId
     * @return array<string, mixed>|null
     */
    public function getByIntegrationId($integrationId)
    {
        $integrationId = trim((string) $integrationId);
        if ($integrationId === '') {
            return null;
        }
        $this->ensureSchema();
        try {
            $row = DB::table('seventh_tradehub_integrations')->where('integration_id', $integrationId)->first();

            return $row ? (array) $row : null;
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub getByIntegrationId: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Ensure both demo and owned_tool context rows exist.
     *
     * @return void
     */
    public function ensureContextRows()
    {
        $this->ensureContextRow(self::CONTEXT_DEMO);
        $this->ensureContextRow(self::CONTEXT_OWNED);
    }

    /**
     * @param string $context
     * @return void
     */
    public function ensureContextRow($context)
    {
        if (!in_array($context, [self::CONTEXT_DEMO, self::CONTEXT_OWNED], true)) {
            return;
        }
        try {
            if (!Schema::hasTable('seventh_tradehub_integrations')) {
                return;
            }
            $exists = DB::table('seventh_tradehub_integrations')->where('context', $context)->exists();
            if ($exists) {
                return;
            }
            DB::table('seventh_tradehub_integrations')->insert([
                'context' => $context,
                'enabled' => 0,
                'updated_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub ensureContextRow: ' . $e->getMessage());
        }
    }

    /**
     * Encode Hub secrets without depending on ENCRYPTION_KEY or OpenSSL.
     * Format: sth0:<standard-base64>
     *
     * @param string $plain
     * @return string
     */
    public function sealSecret($plain)
    {
        $plain = trim((string) $plain);
        if ($plain === '') {
            return '';
        }

        return 'sth0:' . base64_encode($plain);
    }

    /**
     * Unseal a Hub secret. Supports sth0, legacy sth1, and plain fallbacks.
     *
     * @param string|null $stored
     * @return string
     */
    public function unsealSecret($stored)
    {
        $stored = trim((string) $stored);
        if ($stored === '') {
            return '';
        }

        if (strpos($stored, 'sth0:') === 0) {
            $raw = base64_decode(substr($stored, 5), true);

            return ($raw === false) ? '' : $raw;
        }

        if (strpos($stored, 'sth0.') === 0) {
            $data = strtr(substr($stored, 5), '-_', '+/');
            $pad = strlen($data) % 4;
            if ($pad > 0) {
                $data .= str_repeat('=', 4 - $pad);
            }
            $raw = base64_decode($data, true);

            return ($raw === false) ? '' : $raw;
        }

        if (strpos($stored, 'sth1.') === 0 && function_exists('openssl_decrypt')) {
            $encKey = (string) (env('ENCRYPTION_KEY') ?: (defined('ENCRYPTION_KEY') ? ENCRYPTION_KEY : ''));
            if ($encKey !== '') {
                $parts = explode('.', $stored);
                if (count($parts) === 3) {
                    $ivData = strtr($parts[1], '-_', '+/');
                    $cipherData = strtr($parts[2], '-_', '+/');
                    $pad = strlen($ivData) % 4;
                    if ($pad > 0) {
                        $ivData .= str_repeat('=', 4 - $pad);
                    }
                    $pad = strlen($cipherData) % 4;
                    if ($pad > 0) {
                        $cipherData .= str_repeat('=', 4 - $pad);
                    }
                    $iv = base64_decode($ivData, true);
                    $cipher = base64_decode($cipherData, true);
                    if ($iv !== false && $cipher !== false && strlen($iv) === 16) {
                        $key = hash('sha256', $encKey . '|7th-tradehub-v1', true);
                        $plain = openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
                        if (is_string($plain) && $plain !== '') {
                            return $plain;
                        }
                    }
                }
            }
        }

        return '';
    }

    /**
     * @param string|null $stored
     * @return string
     */
    public function secretFormat($stored)
    {
        $stored = trim((string) $stored);
        if ($stored === '') {
            return 'empty';
        }
        if (strpos($stored, 'sth0:') === 0) {
            return 'sth0';
        }
        if (strpos($stored, 'sth0.') === 0) {
            return 'sth0dot';
        }
        if (strpos($stored, 'sth1.') === 0) {
            return 'sth1';
        }

        return 'legacy';
    }

    /**
     * @param array<string, mixed> $integration
     * @return string
     */
    public function clientSecret(array $integration)
    {
        return $this->unsealSecret($integration['client_secret_enc'] ?? '');
    }

    /**
     * @param array<string, mixed> $integration
     * @return string
     */
    public function webhookSecret(array $integration)
    {
        return $this->unsealSecret($integration['webhook_secret_enc'] ?? '');
    }

    /**
     * @param array<string, mixed>|null $integration
     * @return array{ok: bool, reason: string}
     */
    public function operationalStatus($integration)
    {
        if (!$integration) {
            return ['ok' => false, 'reason' => 'Integration row not found'];
        }
        if (empty($integration['enabled'])) {
            return ['ok' => false, 'reason' => 'Integration is disabled — enable it and Save'];
        }
        $id = trim((string) ($integration['integration_id'] ?? ''));
        if ($id === '') {
            return ['ok' => false, 'reason' => 'Integration ID is missing'];
        }
        $clientId = trim((string) ($integration['client_id'] ?? ''));
        if ($clientId === '') {
            return ['ok' => false, 'reason' => 'Client ID is missing'];
        }
        $enc = trim((string) ($integration['client_secret_enc'] ?? ''));
        if ($enc === '') {
            return ['ok' => false, 'reason' => 'Client Secret has not been saved — paste it and Save again'];
        }
        $secret = $this->clientSecret($integration);
        if ($secret === '') {
            return [
                'ok' => false,
                'reason' => 'Client Secret cannot be read from storage. Paste Client Secret again and Save (do not leave the field blank).',
            ];
        }

        return ['ok' => true, 'reason' => 'ready'];
    }

    /**
     * @param array<string, mixed>|null $integration
     * @return bool
     */
    public function isIntegrationOperational($integration)
    {
        return $this->operationalStatus($integration)['ok'] === true;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function canonicalize($value)
    {
        if (is_array($value)) {
            if ($value === [] || array_keys($value) === range(0, count($value) - 1)) {
                return '[' . implode(',', array_map([$this, 'canonicalize'], $value)) . ']';
            }
            ksort($value);
            $parts = [];
            foreach ($value as $key => $item) {
                $parts[] = $this->canonicalize((string) $key) . ':' . $this->canonicalize($item);
            }

            return '{' . implode(',', $parts) . '}';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if ($value === null) {
            return 'null';
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        if (!is_string($value)) {
            throw new InvalidArgumentException('Unsupported type for canonicalization.');
        }

        return '"' . addcslashes($value, "\\\"\n\r\t") . '"';
    }

    /**
     * @param array<string, mixed> $payload
     * @param string $clientSecret
     * @return bool
     */
    public function verifyPayload(array $payload, $clientSecret)
    {
        $signature = $payload['signature'] ?? null;
        if (!is_string($signature) || $signature === '') {
            return false;
        }
        if (($payload['protocol'] ?? null) !== '7th-tradehub') {
            return false;
        }
        if ((int) ($payload['version'] ?? 0) !== 1) {
            return false;
        }
        $copy = $payload;
        unset($copy['signature']);
        ksort($copy);
        $expected = hash_hmac('sha256', $this->canonicalize($copy), $clientSecret);

        return hash_equals($expected, $signature);
    }

    /**
     * @param array<string, mixed> $payload
     * @param string $clientSecret
     * @return array<string, mixed>
     */
    public function signPayload(array $payload, $clientSecret)
    {
        $payload['protocol'] = '7th-tradehub';
        $payload['version'] = 1;
        unset($payload['signature']);
        ksort($payload);
        $payload['signature'] = hash_hmac('sha256', $this->canonicalize($payload), $clientSecret);

        return $payload;
    }

    /**
     * @param array<string, mixed> $payload
     * @return bool
     */
    public function assertionExpired(array $payload)
    {
        $expiresAt = trim((string) ($payload['expires_at'] ?? ''));
        if ($expiresAt === '') {
            return true;
        }
        try {
            $exp = new DateTimeImmutable($expiresAt);

            return $exp < new DateTimeImmutable('now', $exp->getTimezone());
        } catch (Throwable $e) {
            return true;
        }
    }

    /**
     * Verify inbound Hub health/sync request.
     *
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $integration
     * @param array<string, string>|null $headers Optional header map (defaults to current request)
     * @return array{ok: bool, error: ?string, code: ?int, integration: ?array}
     */
    public function verifyInboundRequest(array $payload, array $integration, $headers = null)
    {
        $event = $this->guessProtocolEvent();
        $integrationId = trim((string) ($integration['integration_id'] ?? ''));
        $context = trim((string) ($integration['context'] ?? ''));
        $logBase = [
            'event' => $event,
            'integration_id' => $integrationId,
            'context' => $context,
        ];

        if (!$this->isIntegrationOperational($integration)) {
            return $this->inboundReject('integration_disabled', 401, $logBase, 'Rejected: integration disabled or incomplete credentials (enable Owned/Demo and Save secrets)');
        }

        $payloadIntegrationId = trim((string) ($payload['integration_id'] ?? ''));
        $rowIntegrationId = trim((string) ($integration['integration_id'] ?? ''));
        if ($payloadIntegrationId === '' || !hash_equals($rowIntegrationId, $payloadIntegrationId)) {
            return $this->inboundReject('unknown_integration', 404, $logBase, 'Rejected: payload integration_id does not match local row', [
                'payload_integration_id' => $payloadIntegrationId,
            ]);
        }

        $headerIntegrationId = $this->header('X-7TH-Integration-Id', $headers);
        if ($headerIntegrationId === '' || !hash_equals($rowIntegrationId, $headerIntegrationId)) {
            return $this->inboundReject('integration_id_mismatch', 401, $logBase, 'Rejected: X-7TH-Integration-Id header mismatch');
        }

        $headerClientId = $this->header('X-7TH-Client-Id', $headers);
        $rowClientId = trim((string) ($integration['client_id'] ?? ''));
        if ($headerClientId === '' || !hash_equals($rowClientId, $headerClientId)) {
            return $this->inboundReject('client_id_mismatch', 401, $logBase, 'Rejected: X-7TH-Client-Id header mismatch');
        }

        $payloadContext = trim((string) ($payload['context'] ?? ''));
        $rowContext = trim((string) ($integration['context'] ?? ''));
        if ($payloadContext === '' || !hash_equals($rowContext, $payloadContext)) {
            return $this->inboundReject('context_mismatch', 401, $logBase, 'Rejected: context mismatch (demo vs owned_tool)', [
                'payload_context' => $payloadContext,
                'row_context' => $rowContext,
            ]);
        }

        if ($this->assertionExpired($payload)) {
            return $this->inboundReject('expired_assertion', 401, $logBase, 'Rejected: assertion expired');
        }

        $secret = $this->clientSecret($integration);
        if (!$this->verifyPayload($payload, $secret)) {
            return $this->inboundReject('invalid_signature', 401, $logBase, 'Rejected: invalid HMAC signature (Client Secret mismatch)');
        }

        $nonceResult = $this->recordNonce($integration, $payload);
        if (empty($nonceResult['ok'])) {
            return $this->inboundReject(
                (string) ($nonceResult['error'] ?? 'replay_detected'),
                401,
                $logBase,
                'Rejected: replay detected'
            );
        }

        return [
            'ok' => true,
            'error' => null,
            'code' => null,
            'integration' => $integration,
        ];
    }

    /**
     * @param array<string, mixed> $integration
     * @param array<string, mixed> $payload
     * @return array{ok: bool, error: ?string}
     */
    public function recordNonce(array $integration, array $payload)
    {
        $requestId = trim((string) ($payload['request_id'] ?? ''));
        $nonce = trim((string) ($payload['nonce'] ?? ''));
        $integrationId = trim((string) ($integration['integration_id'] ?? ''));
        if ($requestId === '' || $nonce === '' || $integrationId === '') {
            return ['ok' => true, 'error' => null];
        }
        try {
            if (!Schema::hasTable('seventh_tradehub_nonces')) {
                return ['ok' => true, 'error' => null];
            }
            $existing = DB::table('seventh_tradehub_nonces')
                ->where('integration_id', $integrationId)
                ->where('request_id', $requestId)
                ->exists();
            if ($existing) {
                return ['ok' => false, 'error' => 'replay_detected'];
            }
            DB::table('seventh_tradehub_nonces')->insert([
                'integration_id' => $integrationId,
                'request_id' => $requestId,
                'nonce' => $nonce,
                'seen_at' => now(),
            ]);
        } catch (Throwable $e) {
            $msg = $e->getMessage();
            // Unique race: treat as replay (safer than Axion fail-open).
            if (
                stripos($msg, 'Duplicate') !== false
                || stripos($msg, 'UNIQUE') !== false
                || stripos($msg, '1062') !== false
                || (method_exists($e, 'getCode') && (string) $e->getCode() === '23000')
            ) {
                return ['ok' => false, 'error' => 'replay_detected'];
            }
            Log::warning('SeventhTradeHub recordNonce: ' . $msg);
        }

        return ['ok' => true, 'error' => null];
    }

    /**
     * @param string $context
     * @return array<int, string>
     */
    public function capabilitiesForContext($context)
    {
        if ($context === self::CONTEXT_DEMO) {
            return ['health', 'demo_user_login', 'demo_admin_login'];
        }
        if ($context === self::CONTEXT_OWNED) {
            return [
                'health',
                'subscription_sync',
                'shutdown_on_expiry',
                'owned_admin_login',
                'admin_credential_sync',
            ];
        }

        return ['health'];
    }

    /**
     * @param string $token
     * @param array<string, mixed> $integration
     * @return array{valid: bool, data: ?array, http_code: int, error: ?string}
     */
    public function validateToken($token, array $integration)
    {
        $token = trim((string) $token);
        if ($token === '' || !$this->isIntegrationOperational($integration)) {
            return ['valid' => false, 'data' => null, 'http_code' => 403, 'error' => 'integration_disabled'];
        }

        $hubUrl = $this->hubUrl();
        if ($hubUrl === '') {
            return ['valid' => false, 'data' => null, 'http_code' => 503, 'error' => 'hub_url_missing'];
        }

        $clientId = trim((string) ($integration['client_id'] ?? ''));
        $clientSecret = $this->clientSecret($integration);

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'X-7TH-Client-Id' => $clientId,
                    'X-7TH-Client-Secret' => $clientSecret,
                ])
                ->asJson()
                ->post($hubUrl . '/api/site-integrations/v1/demo/tokens/validate', [
                    'token' => $token,
                ]);

            $code = $response->status();
            $body = $response->json();
            if (!is_array($body)) {
                $body = null;
            }

            if ($code === 422) {
                return ['valid' => false, 'data' => $body, 'http_code' => 422, 'error' => 'invalid_token'];
            }
            if ($code === 401) {
                return ['valid' => false, 'data' => $body, 'http_code' => 401, 'error' => 'invalid_credentials'];
            }
            if ($code !== 200 || !is_array($body) || ($body['valid'] ?? false) !== true) {
                return ['valid' => false, 'data' => $body, 'http_code' => $code, 'error' => 'validate_failed'];
            }

            return ['valid' => true, 'data' => $body, 'http_code' => 200, 'error' => null];
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub validateToken: ' . $e->getMessage());

            return ['valid' => false, 'data' => null, 'http_code' => 502, 'error' => 'hub_unreachable'];
        }
    }

    /**
     * @param array<string, mixed> $validated
     * @return bool
     */
    public function validateResponseIsFresh(array $validated)
    {
        if (($validated['protocol'] ?? null) !== '7th-tradehub') {
            return false;
        }
        if ((int) ($validated['version'] ?? 0) !== 1) {
            return false;
        }
        $expiresAt = trim((string) ($validated['expires_at'] ?? ''));
        if ($expiresAt === '') {
            return true;
        }
        try {
            $exp = new DateTimeImmutable($expiresAt);

            return $exp >= new DateTimeImmutable('now', $exp->getTimezone());
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Resolve Hub SSO identity to local User or Admin (grand-chase mapping).
     *
     * @param string $email
     * @param string $hubRole user|admin
     * @param string $context
     * @return array{type: ?string, model: mixed, error: ?string}
     */
    public function resolveLocalIdentity($email, $hubRole, $context)
    {
        $email = trim((string) $email);
        $hubRole = strtolower(trim((string) $hubRole));
        if ($email === '') {
            return ['type' => null, 'model' => null, 'error' => 'missing_email'];
        }

        if ($hubRole === 'admin') {
            $admin = $this->findAdminByEmail($email);
            if (!$admin) {
                return ['type' => null, 'model' => null, 'error' => 'user_not_found'];
            }
            if ($this->isInactiveStatus($admin->status ?? null)) {
                return ['type' => null, 'model' => null, 'error' => 'user_inactive'];
            }
            if (!empty($admin->is_super_admin)) {
                return ['type' => null, 'model' => null, 'error' => 'super_admin_not_allowed'];
            }

            return ['type' => 'admin', 'model' => $admin, 'error' => null];
        }

        if ($hubRole === 'user') {
            if ($context !== self::CONTEXT_DEMO) {
                return ['type' => null, 'model' => null, 'error' => 'owned_user_sso_not_supported'];
            }
            $user = $this->findUserByEmail($email);
            if (!$user) {
                return ['type' => null, 'model' => null, 'error' => 'user_not_found'];
            }
            if ($this->isInactiveStatus($user->status ?? ($user->account_status ?? null))) {
                return ['type' => null, 'model' => null, 'error' => 'user_inactive'];
            }
            if (empty($user->is_demo_user)) {
                return ['type' => null, 'model' => null, 'error' => 'not_demo_user'];
            }

            return ['type' => 'user', 'model' => $user, 'error' => null];
        }

        return ['type' => null, 'model' => null, 'error' => 'unknown_role'];
    }

    /**
     * Apply monotonic subscription update from Hub sync/poll.
     *
     * @param string $integrationId
     * @param array<string, mixed> $subscription
     * @return array{applied: bool, skipped: bool, shutdown_active: bool, reason?: string}
     */
    public function applySubscription($integrationId, array $subscription)
    {
        $integrationId = trim((string) $integrationId);
        if ($integrationId === '') {
            return ['applied' => false, 'skipped' => true, 'shutdown_active' => false, 'reason' => 'empty_integration_id'];
        }

        $this->ensureSchema();

        $incomingUpdated = trim((string) ($subscription['updated_at'] ?? ''));
        $incomingExpires = trim((string) ($subscription['expires_at'] ?? ''));
        $status = $this->normalizeSubscriptionStatus($subscription['status'] ?? 'pending_setup');
        if ($status === '') {
            $status = 'pending_setup';
        }
        $toolId = isset($subscription['tool_id']) ? (int) $subscription['tool_id'] : null;
        $publicId = trim((string) ($subscription['public_id'] ?? ''));

        // Monotonic "offline wins": merchant-guide offline statuses or past expires_at.
        $incomingOffline = $this->subscriptionIsOffline([
            'status' => $status,
            'expires_at' => $incomingExpires,
        ]);

        $existing = $this->getSubscription($integrationId);
        if ($existing) {
            $storedUpdated = trim((string) ($existing['updated_at'] ?? ''));
            $storedOffline = $this->subscriptionIsOffline($existing);

            if (!$incomingOffline) {
                if ($storedUpdated !== '' && $incomingUpdated !== '') {
                    $storedDt = $this->parseUtcTimestamp($storedUpdated);
                    $incomingDt = $this->parseUtcTimestamp($incomingUpdated);
                    if ($storedDt && $incomingDt && $incomingDt < $storedDt) {
                        Log::info(
                            'SeventhTradeHub applySubscription: skipped stale update for ' . $integrationId .
                            ' incoming=' . $incomingUpdated . ' stored=' . $storedUpdated
                        );

                        return [
                            'applied' => false,
                            'skipped' => true,
                            'shutdown_active' => $this->isOwnedSiteShutdown(),
                            'reason' => 'stale_updated_at',
                        ];
                    }
                }

                if ($storedOffline) {
                    if ($storedUpdated === '' || $incomingUpdated === '') {
                        return [
                            'applied' => false,
                            'skipped' => true,
                            'shutdown_active' => true,
                            'reason' => 'refuse_unexpire_missing_updated_at',
                        ];
                    }
                    $storedDt = $this->parseUtcTimestamp($storedUpdated);
                    $incomingDt = $this->parseUtcTimestamp($incomingUpdated);
                    if (!$storedDt || !$incomingDt) {
                        return [
                            'applied' => false,
                            'skipped' => true,
                            'shutdown_active' => true,
                            'reason' => 'refuse_unexpire_bad_updated_at',
                        ];
                    }
                    if ($incomingDt <= $storedDt) {
                        return [
                            'applied' => false,
                            'skipped' => true,
                            'shutdown_active' => true,
                            'reason' => 'refuse_unexpire_not_newer',
                        ];
                    }
                }
            } elseif ($storedUpdated !== '' && $incomingUpdated !== '') {
                $storedDt = $this->parseUtcTimestamp($storedUpdated);
                $incomingDt = $this->parseUtcTimestamp($incomingUpdated);
                if ($storedDt && $incomingDt && $incomingDt < $storedDt) {
                    Log::info(
                        'SeventhTradeHub applySubscription: applying offline despite older updated_at for ' .
                        $integrationId . ' incoming=' . $incomingUpdated . ' stored=' . $storedUpdated
                    );
                }
            }
        }

        $expiresForDb = null;
        $updatedForDb = null;
        if ($incomingExpires !== '') {
            $expDt = $this->parseUtcTimestamp($incomingExpires);
            $expiresForDb = $expDt ? $expDt->format('Y-m-d H:i:s') : $incomingExpires;
        }
        if ($incomingUpdated !== '') {
            $updDt = $this->parseUtcTimestamp($incomingUpdated);
            $updatedForDb = $updDt ? $updDt->format('Y-m-d H:i:s') : $incomingUpdated;
        }
        if ($incomingOffline) {
            $nowUtc = new DateTimeImmutable('now', new DateTimeZone('UTC'));
            $updDt = $updatedForDb !== null ? $this->parseUtcTimestamp((string) $updatedForDb) : null;
            if (!$updDt || $updDt < $nowUtc) {
                $updatedForDb = $nowUtc->format('Y-m-d H:i:s');
            }
        }

        try {
            if (!Schema::hasTable('seventh_tradehub_subscriptions')) {
                if ($incomingOffline) {
                    $this->setOwnedShutdownLatch(true);
                }

                return [
                    'applied' => false,
                    'skipped' => false,
                    'shutdown_active' => $this->isOwnedSiteShutdown(),
                    'reason' => 'db_write_failed',
                ];
            }

            $lastSyncUtc = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');

            DB::table('seventh_tradehub_subscriptions')->updateOrInsert(
                ['integration_id' => $integrationId],
                [
                    'tool_id' => $toolId,
                    'public_id' => $publicId !== '' ? $publicId : null,
                    'status' => $status,
                    'expires_at' => $expiresForDb,
                    'updated_at' => $updatedForDb,
                    'last_sync_at' => $lastSyncUtc,
                ]
            );
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub applySubscription: ' . $e->getMessage());
            if ($incomingOffline) {
                $this->setOwnedShutdownLatch(true);
            }

            return [
                'applied' => false,
                'skipped' => false,
                'shutdown_active' => $this->isOwnedSiteShutdown(),
                'reason' => 'db_exception',
            ];
        }

        $this->setOwnedShutdownLatch($incomingOffline);
        $shutdown = $incomingOffline || $this->isOwnedSiteShutdown();
        Log::info(
            'SeventhTradeHub applySubscription: applied integration=' . $integrationId .
            ' status=' . $status .
            ' expires_at=' . ($expiresForDb ?? '') .
            ' incoming_offline=' . ($incomingOffline ? '1' : '0') .
            ' shutdown_active=' . ($shutdown ? '1' : '0')
        );

        return [
            'applied' => true,
            'skipped' => false,
            'shutdown_active' => $shutdown,
            'reason' => $incomingOffline ? 'ok_offline' : 'ok',
        ];
    }

    /**
     * @param string $integrationId
     * @return array<string, mixed>|null
     */
    public function getSubscription($integrationId)
    {
        $integrationId = trim((string) $integrationId);
        if ($integrationId === '') {
            return null;
        }
        $this->ensureSchema();
        try {
            if (!Schema::hasTable('seventh_tradehub_subscriptions')) {
                return null;
            }
            $row = DB::table('seventh_tradehub_subscriptions')->where('integration_id', $integrationId)->first();

            return $row ? (array) $row : null;
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub getSubscription: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Merchant-guide offline Hub statuses (authenticated / application sites).
     * `pending_setup` alone is NOT offline here — only active is “fully online”,
     * but Hub’s merchant guide + PHP sample gate on these four + past expires_at.
     * Unknown non-active statuses still fail closed.
     *
     * @return list<string>
     */
    public function offlineSubscriptionStatuses()
    {
        return ['expired', 'suspended', 'cancelled', 'inactive'];
    }

    /**
     * @param string|null $status
     * @return string
     */
    public function normalizeSubscriptionStatus($status)
    {
        return strtolower(trim((string) $status));
    }

    /**
     * Whether a subscription payload / row should shut the site down.
     * Offline when Hub status is expired|suspended|cancelled|inactive,
     * unknown non-active (not pending_setup), or expires_at is past.
     *
     * @param array<string, mixed>|null $subscription
     * @return bool
     */
    public function subscriptionIsOffline($subscription)
    {
        if (!$subscription) {
            return false;
        }
        $status = $this->normalizeSubscriptionStatus($subscription['status'] ?? '');
        if (in_array($status, $this->offlineSubscriptionStatuses(), true)) {
            return true;
        }
        // Unknown Hub values (not active / pending_setup) → fail closed
        if ($status !== '' && $status !== 'active' && $status !== 'pending_setup') {
            return true;
        }
        $expiresAt = trim((string) ($subscription['expires_at'] ?? ''));
        if ($expiresAt === '') {
            return false;
        }
        $exp = $this->parseUtcTimestamp($expiresAt);
        if (!$exp) {
            return false;
        }

        return $exp < new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }

    /**
     * Fail-closed: local `active` older than max trust age is not trusted.
     *
     * @param array<string, mixed>|null $subscription
     * @return bool
     */
    public function subscriptionTrustExpired($subscription)
    {
        if (!$subscription || $this->subscriptionIsOffline($subscription)) {
            return false;
        }
        $lastSync = trim((string) ($subscription['last_sync_at'] ?? ''));
        if ($lastSync === '') {
            return true;
        }
        $syncDt = $this->parseUtcTimestamp($lastSync);
        if (!$syncDt) {
            return true;
        }
        $age = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->getTimestamp() - $syncDt->getTimestamp();

        return $age > self::MAX_TRUST_AGE_SECONDS;
    }

    /**
     * @deprecated Prefer subscriptionIsOffline — kept for callers that still say "expired".
     *
     * @param array<string, mixed>|null $subscription
     * @return bool
     */
    public function subscriptionIsExpired($subscription)
    {
        return $this->subscriptionIsOffline($subscription);
    }

    /**
     * Status string for regular-admin offline CTA.
     * Maps past expires_at / trust-stale active to `expired`.
     *
     * @param array<string, mixed>|null $subscription
     * @return string
     */
    public function resolveOfflineDisplayStatus($subscription)
    {
        if (!$subscription) {
            return '';
        }
        $status = $this->normalizeSubscriptionStatus($subscription['status'] ?? '');
        if ($status !== '' && $status !== 'active') {
            return $status;
        }
        $expiresAt = trim((string) ($subscription['expires_at'] ?? ''));
        if ($expiresAt !== '') {
            $exp = $this->parseUtcTimestamp($expiresAt);
            if ($exp && $exp < new DateTimeImmutable('now', new DateTimeZone('UTC'))) {
                return 'expired';
            }
        }
        if ($status === 'active' && $this->subscriptionTrustExpired($subscription)) {
            return 'expired';
        }

        return $status;
    }

    /**
     * Current owned subscription display status for UI / logging.
     *
     * @return string
     */
    public function ownedSubscriptionStatus()
    {
        $owned = $this->getByContext(self::CONTEXT_OWNED);
        if (!$owned) {
            return '';
        }
        $integrationId = trim((string) ($owned['integration_id'] ?? ''));
        if ($integrationId === '') {
            return '';
        }
        $sub = $this->getSubscription($integrationId);
        if (!$sub) {
            return '';
        }

        return $this->resolveOfflineDisplayStatus($sub);
    }

    /**
     * Status-specific Hub CTA copy for regular admins after password/2FA login.
     *
     * @param string|null $status
     * @return array{status: string, message: string, cta_href: string, cta_label: string, hub_url: string}
     */
    public function adminOfflineCopy($status = null)
    {
        $status = $this->normalizeSubscriptionStatus($status ?: $this->ownedSubscriptionStatus());
        $hubUrl = $this->hubUrl();
        if ($hubUrl === '') {
            $hubUrl = 'https://7th-tradehub.online';
        }
        $hubUrl = rtrim($hubUrl, '/');

        $message = 'This website subscription is offline. Contact 7th Trade Hub support for help.';
        $ctaHref = $hubUrl . '/help';
        $ctaLabel = 'Open Help Center';

        switch ($status) {
            case 'expired':
                $message = 'Your website subscription has expired. Sign in to your 7th Trade Hub account to renew this website subscription.';
                $ctaHref = $hubUrl . '/login';
                $ctaLabel = 'Sign in to 7th Trade Hub';
                break;
            case 'cancelled':
                $message = 'This website subscription has been cancelled. Contact 7th Trade Hub support for help.';
                break;
            case 'suspended':
                $message = 'This website has been suspended. Contact 7th Trade Hub support for help.';
                break;
            case 'inactive':
                $message = 'This website is inactive. Contact 7th Trade Hub support for help.';
                break;
            case 'pending_setup':
                $message = 'This website subscription is not active yet. Sign in to your 7th Trade Hub account to finish setup.';
                $ctaHref = $hubUrl . '/login';
                $ctaLabel = 'Sign in to 7th Trade Hub';
                break;
        }

        return [
            'status' => $status,
            'message' => $message,
            'cta_href' => $ctaHref,
            'cta_label' => $ctaLabel,
            'hub_url' => $hubUrl,
        ];
    }

    /**
     * Owned shutdown gate:
     * - Unconfigured / Owned disabled → site stays open
     * - Offline status / past expires_at / trust age exceeded → shut down (+ latch)
     * - No local subscription → honor latch only (Axion-style; avoids breaking mid-setup)
     *
     * @return bool
     */
    public function isOwnedSiteShutdown()
    {
        try {
            if (!Schema::hasTable('seventh_tradehub_integrations')) {
                return false;
            }

            $owned = DB::table('seventh_tradehub_integrations')
                ->where('context', self::CONTEXT_OWNED)
                ->first();

            if (!$owned || empty($owned->enabled)) {
                return false;
            }

            $integrationId = trim((string) ($owned->integration_id ?? ''));
            if ($integrationId === '') {
                return false;
            }

            if (!Schema::hasTable('seventh_tradehub_subscriptions')) {
                return $this->ownedShutdownLatchIsSet();
            }

            $sub = DB::table('seventh_tradehub_subscriptions')
                ->where('integration_id', $integrationId)
                ->first();

            if (!$sub) {
                return $this->ownedShutdownLatchIsSet();
            }

            $subArr = (array) $sub;
            if ($this->subscriptionIsOffline($subArr) || $this->subscriptionTrustExpired($subArr)) {
                $this->setOwnedShutdownLatch(true);

                return true;
            }

            $this->setOwnedShutdownLatch(false);

            return false;
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub isOwnedSiteShutdown: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Soft latch when Hub said offline but local row write failed, or after confirmed offline.
     * Cleared when a fresh online snapshot applies. Stored in seventh_tradehub_config (no file latch).
     *
     * @return bool
     */
    protected function ownedShutdownLatchIsSet()
    {
        try {
            $this->ensureConfigTable();
            if (!Schema::hasTable('seventh_tradehub_config') || !Schema::hasColumn('seventh_tradehub_config', 'owned_shutdown_latch')) {
                return false;
            }

            return (int) (DB::table('seventh_tradehub_config')->where('id', 1)->value('owned_shutdown_latch') ?? 0) === 1;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * @param bool $active
     * @return void
     */
    protected function setOwnedShutdownLatch($active)
    {
        try {
            $this->ensureConfigTable();
            if (!Schema::hasTable('seventh_tradehub_config') || !Schema::hasColumn('seventh_tradehub_config', 'owned_shutdown_latch')) {
                return;
            }
            $payload = [
                'owned_shutdown_latch' => $active ? 1 : 0,
                'updated_at' => now(),
            ];
            if (DB::table('seventh_tradehub_config')->where('id', 1)->exists()) {
                DB::table('seventh_tradehub_config')->where('id', 1)->update($payload);
            } else {
                DB::table('seventh_tradehub_config')->insert(array_merge([
                    'id' => 1,
                    'hub_url' => null,
                    'last_reconcile_at' => null,
                ], $payload));
            }
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub setOwnedShutdownLatch: ' . $e->getMessage());
        }
    }

    /**
     * Throttled Hub GET when local owned state is missing, offline-by-clock, or past max trust age.
     * Push remains primary; cron poll remains optional backup.
     *
     * @return void
     */
    public function maybeReconcileOwnedSubscription()
    {
        if (self::$reconcileAttemptedThisRequest || app()->runningInConsole()) {
            return;
        }
        self::$reconcileAttemptedThisRequest = true;

        try {
            $owned = $this->getByContext(self::CONTEXT_OWNED);
            if (!$owned || empty($owned['enabled']) || !$this->isIntegrationOperational($owned)) {
                return;
            }

            $integrationId = trim((string) ($owned['integration_id'] ?? ''));
            if ($integrationId === '') {
                return;
            }

            $sub = $this->getSubscription($integrationId);
            $needs = !$sub
                || $this->subscriptionIsOffline($sub)
                || $this->subscriptionTrustExpired($sub);
            if (!$needs) {
                return;
            }

            // Already offline by Hub status (not merely clock-stale active) — push owns that path.
            if ($sub && $this->subscriptionIsOffline($sub) && !$this->subscriptionTrustExpired($sub)) {
                $status = $this->normalizeSubscriptionStatus($sub['status'] ?? '');
                if ($status !== 'active' && $status !== '') {
                    return;
                }
            }

            $lastAttempt = $this->getLastReconcileAttemptAt();
            if ($lastAttempt) {
                $age = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->getTimestamp() - $lastAttempt->getTimestamp();
                if ($age < self::RECONCILE_INTERVAL_SECONDS) {
                    return;
                }
            }

            $this->markReconcileAttempt();
            $this->pollSubscription($owned);
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub maybeReconcileOwnedSubscription: ' . $e->getMessage());
        }
    }

    /**
     * @return DateTimeImmutable|null
     */
    protected function getLastReconcileAttemptAt()
    {
        try {
            $this->ensureConfigTable();
            if (!Schema::hasTable('seventh_tradehub_config') || !Schema::hasColumn('seventh_tradehub_config', 'last_reconcile_at')) {
                return null;
            }
            $raw = trim((string) (DB::table('seventh_tradehub_config')->where('id', 1)->value('last_reconcile_at') ?? ''));
            if ($raw === '') {
                return null;
            }

            return $this->parseUtcTimestamp($raw);
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * @return void
     */
    protected function markReconcileAttempt()
    {
        try {
            $this->ensureConfigTable();
            if (!Schema::hasTable('seventh_tradehub_config')) {
                return;
            }
            $now = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');
            $payload = ['updated_at' => now()];
            if (Schema::hasColumn('seventh_tradehub_config', 'last_reconcile_at')) {
                $payload['last_reconcile_at'] = $now;
            }
            if (DB::table('seventh_tradehub_config')->where('id', 1)->exists()) {
                DB::table('seventh_tradehub_config')->where('id', 1)->update($payload);
            } else {
                DB::table('seventh_tradehub_config')->insert(array_merge([
                    'id' => 1,
                    'hub_url' => null,
                ], $payload));
            }
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub markReconcileAttempt: ' . $e->getMessage());
        }
    }

    /**
     * @return array{active: bool, applicable: bool, reason: string}
     */
    public function shutdownDiagnostic()
    {
        $owned = $this->getByContext(self::CONTEXT_OWNED);
        if (!$owned) {
            return [
                'active' => false,
                'applicable' => false,
                'reason' => 'Not applicable yet — Owned context row is missing (open this page after migrate, or Save Owned once). Demo SSO/health do not need Owned.',
            ];
        }
        if (empty($owned['enabled'])) {
            return [
                'active' => false,
                'applicable' => false,
                'reason' => 'Not applicable — Owned is disabled. Demo-only sites stay fully online; Hub Shutdown Site is ignored until you enable Owned and Save.',
            ];
        }
        $op = $this->operationalStatus($owned);
        if (empty($op['ok'])) {
            return [
                'active' => false,
                'applicable' => true,
                'reason' => 'Owned enabled but not ready: ' . ($op['reason'] ?? 'incomplete credentials') . ' — complete Owned credentials before Hub can push/poll expiry.',
            ];
        }
        $integrationId = trim((string) ($owned['integration_id'] ?? ''));
        $sub = $this->getSubscription($integrationId);
        if (!$sub) {
            return [
                'active' => false,
                'applicable' => true,
                'reason' => 'Owned ready — no local subscription row yet. Use Pull subscription (or wait for Hub sync/poll). Site stays open until Hub writes status.',
            ];
        }
        if ($this->subscriptionIsOffline($sub)) {
            return [
                'active' => true,
                'applicable' => true,
                'reason' => 'Owned gate ACTIVE (status=' . ($sub['status'] ?? '') . ', expires_at=' . ($sub['expires_at'] ?? '') . '). Regular admins get Hub CTAs on login; public sees Session expired.',
            ];
        }
        if ($this->subscriptionTrustExpired($sub)) {
            return [
                'active' => true,
                'applicable' => true,
                'reason' => 'Fail-closed: last_sync_at older than max trust age (' . ($sub['last_sync_at'] ?? 'never') . ').',
            ];
        }

        return [
            'active' => false,
            'applicable' => true,
            'reason' => 'Owned connected — subscription OK (status=' . ($sub['status'] ?? '') . ', expires_at=' . ($sub['expires_at'] ?? 'none') . ', last_sync_at=' . ($sub['last_sync_at'] ?? 'never') . ').',
        ];
    }

    /**
     * @param array<string, mixed> $integration
     * @return array<string, mixed>|null
     */
    public function pollSubscription(array $integration)
    {
        if (!$this->isIntegrationOperational($integration)) {
            return null;
        }
        $hubUrl = $this->hubUrl();
        if ($hubUrl === '') {
            return null;
        }

        $integrationId = trim((string) ($integration['integration_id'] ?? ''));

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'X-7TH-Client-Id' => trim((string) ($integration['client_id'] ?? '')),
                    'X-7TH-Client-Secret' => $this->clientSecret($integration),
                    'X-7TH-Integration-Id' => $integrationId,
                ])
                ->get($hubUrl . '/api/site-integrations/v1/subscription');

            $code = $response->status();
            $raw = $response->body();

            if ($code !== 200 || !is_string($raw)) {
                $this->connectionLog([
                    'direction' => 'outbound',
                    'event' => 'subscription_poll',
                    'ok' => false,
                    'http_status' => $code,
                    'integration_id' => $integrationId,
                    'context' => trim((string) ($integration['context'] ?? '')),
                    'message' => 'Subscription poll failed (HTTP ' . $code . ')',
                ]);

                return null;
            }

            $body = json_decode($raw, true);
            if (!is_array($body)) {
                $this->connectionLog([
                    'direction' => 'outbound',
                    'event' => 'subscription_poll',
                    'ok' => false,
                    'http_status' => $code,
                    'integration_id' => $integrationId,
                    'context' => trim((string) ($integration['context'] ?? '')),
                    'message' => 'Subscription poll returned invalid JSON',
                ]);

                return null;
            }

            $apply = $this->applySubscription($integrationId, $body);
            $diag = $this->shutdownDiagnostic();
            $status = trim((string) ($body['status'] ?? ''));
            $shutdownActive = !empty($diag['active']);
            $msg = 'Subscription poll OK; status=' . ($status !== '' ? $status : 'unknown');
            if (!empty($apply['skipped'])) {
                $msg .= '; apply_skipped=' . ($apply['reason'] ?? 'skipped');
            } elseif (empty($apply['applied'])) {
                $msg .= '; apply_failed=' . ($apply['reason'] ?? 'failed');
            }
            if ($shutdownActive) {
                $msg = 'SHUTDOWN via poll — site gate ACTIVE (' . $msg . ')';
            } elseif (strtolower($status) === 'expired') {
                $msg .= ' — Hub says expired but local gate NOT active: ' . ($diag['reason'] ?? '');
            }

            $this->connectionLog([
                'direction' => 'outbound',
                'event' => $shutdownActive ? 'shutdown_poll' : 'subscription_poll',
                'ok' => true,
                'http_status' => 200,
                'integration_id' => $integrationId,
                'context' => trim((string) ($integration['context'] ?? '')),
                'message' => $msg,
                'detail' => [
                    'apply' => $apply,
                    'shutdown' => $diag,
                    'expires_at' => $body['expires_at'] ?? null,
                ],
            ]);

            return $body;
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub pollSubscription: ' . $e->getMessage());
            $this->connectionLog([
                'direction' => 'outbound',
                'event' => 'subscription_poll',
                'ok' => false,
                'http_status' => 0,
                'integration_id' => $integrationId,
                'context' => trim((string) ($integration['context'] ?? '')),
                'message' => 'Subscription poll failed: ' . $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @param array<string, mixed> $integration
     * @return array{ok: bool, message: string}
     */
    public function webhookPing(array $integration)
    {
        $status = $this->operationalStatus($integration);
        if (!$status['ok']) {
            return ['ok' => false, 'message' => $status['reason']];
        }
        $hubUrl = $this->hubUrl();
        $integrationId = trim((string) ($integration['integration_id'] ?? ''));
        $webhookSecret = $this->webhookSecret($integration);
        if ($hubUrl === '') {
            return ['ok' => false, 'message' => 'Hub URL is missing — set it above and Save'];
        }
        if ($webhookSecret === '') {
            return [
                'ok' => false,
                'message' => 'Test webhook needs a Webhook Secret. Paste it, Save, then try again. Or skip this button and use Hub Check connection (Client Secret only).',
            ];
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'X-7TH-Webhook-Secret' => $webhookSecret,
                ])
                ->asJson()
                ->post($hubUrl . '/webhooks/site-integrations/' . rawurlencode($integrationId), [
                    'event' => 'ping',
                ]);

            $code = $response->status();
            $body = $response->json();

            if ($code >= 200 && $code < 300 && is_array($body) && ($body['ok'] ?? false) === true) {
                $this->connectionLog([
                    'direction' => 'outbound',
                    'event' => 'webhook_ping',
                    'ok' => true,
                    'http_status' => $code,
                    'integration_id' => $integrationId,
                    'context' => trim((string) ($integration['context'] ?? '')),
                    'message' => 'Webhook ping succeeded (Hub acknowledged ping)',
                ]);

                return ['ok' => true, 'message' => 'Webhook ping succeeded'];
            }

            $this->connectionLog([
                'direction' => 'outbound',
                'event' => 'webhook_ping',
                'ok' => false,
                'http_status' => $code,
                'integration_id' => $integrationId,
                'context' => trim((string) ($integration['context'] ?? '')),
                'message' => 'Webhook ping HTTP ' . $code . ' but body was not { ok: true }',
            ]);

            return ['ok' => false, 'message' => 'Hub responded HTTP ' . $code . ' but body was not { ok: true }'];
        } catch (Throwable $e) {
            $this->connectionLog([
                'direction' => 'outbound',
                'event' => 'webhook_ping',
                'ok' => false,
                'http_status' => 0,
                'integration_id' => $integrationId,
                'context' => trim((string) ($integration['context'] ?? '')),
                'message' => 'Webhook ping failed: ' . $e->getMessage(),
            ]);

            return ['ok' => false, 'message' => 'Webhook ping failed: ' . $e->getMessage()];
        }
    }

    /**
     * POST owned.admin_credentials.updated to Hub.
     *
     * @param array{email?: string, password?: string} $changes
     * @param string|null $reuseEventId
     * @return array{ok: bool, http_code: int, deduped?: bool, message?: string, queued?: bool}
     */
    public function notifyOwnedAdminCredentials(array $changes, $reuseEventId = null)
    {
        $owned = $this->getByContext(self::CONTEXT_OWNED);
        if (!$owned || !$this->isIntegrationOperational($owned)) {
            return ['ok' => false, 'http_code' => 0, 'message' => 'owned_not_ready'];
        }

        $email = isset($changes['email']) ? strtolower(trim((string) $changes['email'])) : '';
        $password = isset($changes['password']) ? (string) $changes['password'] : '';
        if ($password !== '' && (strlen($password) < 6 || strlen($password) > 255)) {
            $password = '';
        }
        if ($email === '' && $password === '') {
            return ['ok' => false, 'http_code' => 0, 'message' => 'nothing_to_send'];
        }

        $hubUrl = $this->hubUrl();
        $integrationId = trim((string) ($owned['integration_id'] ?? ''));
        $clientId = trim((string) ($owned['client_id'] ?? ''));
        $clientSecret = $this->clientSecret($owned);
        $webhookSecret = $this->webhookSecret($owned);

        $eventId = $reuseEventId !== null && $reuseEventId !== ''
            ? substr((string) $reuseEventId, 0, 64)
            : bin2hex(random_bytes(16));

        if ($hubUrl === '' || $integrationId === '' || $clientId === '' || $clientSecret === '') {
            return ['ok' => false, 'http_code' => 0, 'message' => 'missing_hub_config'];
        }
        if ($webhookSecret === '') {
            return [
                'ok' => false,
                'http_code' => 0,
                'message' => 'Webhook Secret is required to sync credentials to Hub. Paste it, Save, then try again.',
            ];
        }

        $result = $this->postCredentialSync(
            $hubUrl,
            $integrationId,
            $clientId,
            $clientSecret,
            $webhookSecret,
            $email,
            $password,
            $eventId,
            true
        );
        if (!empty($result['ok'])) {
            $this->credentialSyncDeleteByEventId($eventId);

            return $result;
        }

        $code = (int) ($result['http_code'] ?? 0);
        $retryable = ($code === 0 || $code === 429 || $code >= 500);
        if ($retryable) {
            $this->credentialSyncEnqueue($integrationId, $email, $password, $eventId);
            $result['queued'] = true;
        }

        return $result;
    }

    /**
     * Drain credential sync outbox. Re-sign with fresh TTL, keep same event_id.
     *
     * @param int $limit
     * @return int
     */
    public function drainCredentialSyncOutbox($limit = 10)
    {
        $this->ensureCredentialSyncOutbox();
        $sent = 0;
        try {
            if (!Schema::hasTable('seventh_tradehub_credential_sync_outbox')) {
                return 0;
            }
            $rows = DB::table('seventh_tradehub_credential_sync_outbox')
                ->where('next_attempt_at', '<=', now())
                ->where('attempts', '<', 20)
                ->orderBy('id', 'asc')
                ->limit((int) $limit)
                ->get();

            $owned = $this->getByContext(self::CONTEXT_OWNED);
            if (!$owned || !$this->isIntegrationOperational($owned)) {
                return 0;
            }
            $hubUrl = $this->hubUrl();
            $clientId = trim((string) ($owned['client_id'] ?? ''));
            $clientSecret = $this->clientSecret($owned);
            $webhookSecret = $this->webhookSecret($owned);
            if ($hubUrl === '' || $clientId === '' || $clientSecret === '' || $webhookSecret === '') {
                return 0;
            }

            foreach ($rows as $row) {
                $row = (array) $row;
                $eventId = (string) ($row['event_id'] ?? '');
                $integrationId = trim((string) ($row['integration_id'] ?? ''));
                $email = trim((string) ($row['email'] ?? ''));
                $password = $this->unsealSecret($row['password_enc'] ?? '');
                if ($eventId === '' || $integrationId === '') {
                    continue;
                }
                $result = $this->postCredentialSync(
                    $hubUrl,
                    $integrationId,
                    $clientId,
                    $clientSecret,
                    $webhookSecret,
                    $email,
                    $password,
                    $eventId,
                    false
                );
                if (!empty($result['ok'])) {
                    DB::table('seventh_tradehub_credential_sync_outbox')->where('id', (int) $row['id'])->delete();
                    $sent++;
                    continue;
                }
                $attempts = (int) ($row['attempts'] ?? 0);
                $delayMinutes = min(60, (int) pow(2, min($attempts + 1, 5)));
                DB::table('seventh_tradehub_credential_sync_outbox')
                    ->where('id', (int) $row['id'])
                    ->update([
                        'attempts' => $attempts + 1,
                        'next_attempt_at' => now()->addMinutes($delayMinutes),
                        'last_error' => 'http_' . (int) ($result['http_code'] ?? 0),
                    ]);
            }
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub drainCredentialSyncOutbox: ' . $e->getMessage());
        }

        return $sent;
    }

    /**
     * Manual catch-up: verify owned admin password locally, then push email + password to Hub.
     *
     * @param string $plainPassword
     * @return array{ok: bool, message: string, http_code?: int}
     */
    public function manualSyncOwnedAdminPassword($plainPassword)
    {
        $plainPassword = (string) $plainPassword;
        if (strlen($plainPassword) < 6 || strlen($plainPassword) > 255) {
            return ['ok' => false, 'message' => 'Password must be 6–255 characters to sync to Hub'];
        }

        $owned = $this->getByContext(self::CONTEXT_OWNED);
        if (!$owned || !$this->isIntegrationOperational($owned)) {
            return ['ok' => false, 'message' => 'Owned integration is not ready for Hub traffic'];
        }
        if ($this->webhookSecret($owned) === '') {
            return ['ok' => false, 'message' => 'Webhook Secret is required. Paste it under Owned, Save, then sync.'];
        }

        $expected = strtolower(trim((string) ($owned['expected_admin_email'] ?? '')));
        if ($expected === '') {
            return ['ok' => false, 'message' => 'Set Expected Admin Email on the Owned card, Save, then sync'];
        }

        $admin = $this->findAdminByEmail($expected);
        if (!$admin) {
            return ['ok' => false, 'message' => 'No local admin account matches Expected Admin Email'];
        }
        if (!empty($admin->is_super_admin)) {
            return ['ok' => false, 'message' => 'Expected Admin Email must be a regular admin (not super admin)'];
        }

        $hash = (string) ($admin->password ?? '');
        if ($hash === '' || !Hash::check($plainPassword, $hash)) {
            return ['ok' => false, 'message' => 'Password does not match the local owned admin account'];
        }

        $result = $this->notifyOwnedAdminCredentials([
            'email' => $expected,
            'password' => $plainPassword,
        ], null);

        if (!empty($result['ok'])) {
            $extra = !empty($result['deduped']) ? ' (Hub reported deduped)' : '';

            return [
                'ok' => true,
                'message' => 'Admin email and password synced to Hub' . $extra,
                'http_code' => (int) ($result['http_code'] ?? 200),
            ];
        }

        $msg = (string) ($result['message'] ?? 'Hub sync failed');
        if (!empty($result['queued'])) {
            $msg .= ' — queued for retry';
        } elseif (($result['http_code'] ?? 0) > 0) {
            $msg = 'Hub rejected credential sync (HTTP ' . (int) $result['http_code'] . ')';
        }

        return [
            'ok' => false,
            'message' => $msg,
            'http_code' => (int) ($result['http_code'] ?? 0),
        ];
    }

    /**
     * After local admin email/password commit: best-effort Hub sync (never throws).
     *
     * @param \App\Models\Admin|array<string, mixed> $adminBefore
     * @param string|null $emailAfter
     * @param string|null $passwordPlain
     * @return void
     */
    public function maybeSyncOwnedAdminCredentials($adminBefore, $emailAfter = null, $passwordPlain = null)
    {
        try {
            if (!$this->isOwnedAdminCredentialTarget($adminBefore)) {
                return;
            }

            $email = $emailAfter !== null ? strtolower(trim((string) $emailAfter)) : '';
            $password = $passwordPlain !== null ? (string) $passwordPlain : '';

            if ($email === '' && $password === '') {
                return;
            }

            if ($password !== '' && (strlen($password) < 6 || strlen($password) > 255)) {
                Log::warning('SeventhTradeHub maybeSyncOwnedAdminCredentials: password length outside Hub 6–255; skipping password field');
                $password = '';
            }
            if ($email === '' && $password === '') {
                return;
            }

            if ($email !== '') {
                $this->updateOwnedExpectedAdminEmail($email);
            }

            $changes = [];
            if ($email !== '') {
                $changes['email'] = $email;
            }
            if ($password !== '') {
                $changes['password'] = $password;
            }

            $this->notifyOwnedAdminCredentials($changes, null);
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub maybeSyncOwnedAdminCredentials: ' . $e->getMessage());
        }
    }

    /**
     * Whether this Admin should sync credentials to Hub on change.
     *
     * @param \App\Models\Admin|array<string, mixed> $adminBefore
     * @return bool
     */
    public function isOwnedAdminCredentialTarget($adminBefore)
    {
        $row = $adminBefore instanceof Admin ? $adminBefore->toArray() : (array) $adminBefore;

        if (!empty($row['is_super_admin'])) {
            return false;
        }

        $owned = $this->getByContext(self::CONTEXT_OWNED);
        if (!$owned || !$this->isIntegrationOperational($owned)) {
            return false;
        }
        $expected = strtolower(trim((string) ($owned['expected_admin_email'] ?? '')));
        if ($expected === '') {
            return false;
        }
        $email = strtolower(trim((string) ($row['email'] ?? '')));

        return $email !== '' && hash_equals($expected, $email);
    }

    /**
     * @param array{
     *   direction?: string,
     *   event: string,
     *   ok?: bool,
     *   http_status?: int|null,
     *   error_code?: string|null,
     *   integration_id?: string|null,
     *   context?: string|null,
     *   message: string,
     *   detail?: mixed
     * } $entry
     * @return void
     */
    public function connectionLog(array $entry)
    {
        try {
            $direction = trim((string) ($entry['direction'] ?? 'inbound'));
            if (!in_array($direction, ['inbound', 'outbound', 'local'], true)) {
                $direction = 'inbound';
            }
            $event = substr(trim((string) ($entry['event'] ?? 'protocol')), 0, 64);
            if ($event === '') {
                $event = 'protocol';
            }
            $ok = !empty($entry['ok']) ? 1 : 0;
            $httpStatus = isset($entry['http_status']) ? (int) $entry['http_status'] : null;
            $errorCode = isset($entry['error_code']) ? substr(trim((string) $entry['error_code']), 0, 64) : null;
            if ($errorCode === '') {
                $errorCode = null;
            }
            $integrationId = isset($entry['integration_id']) ? substr(trim((string) $entry['integration_id']), 0, 36) : null;
            if ($integrationId === '') {
                $integrationId = null;
            }
            $context = isset($entry['context']) ? substr(trim((string) $entry['context']), 0, 32) : null;
            if ($context === '') {
                $context = null;
            }
            $host = $this->requestHost();
            $message = substr(trim((string) ($entry['message'] ?? '')), 0, 512);
            if ($message === '') {
                $message = $ok ? 'OK' : 'Failed';
            }
            $detail = null;
            if (array_key_exists('detail', $entry) && $entry['detail'] !== null) {
                if (is_string($entry['detail'])) {
                    $detail = $entry['detail'];
                } else {
                    $detail = json_encode($entry['detail'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }
                if (is_string($detail) && strlen($detail) > 4000) {
                    $detail = substr($detail, 0, 3997) . '...';
                }
            }

            $line = sprintf(
                '[%s] %s %s %s host=%s integration=%s %s',
                date('Y-m-d H:i:s'),
                strtoupper($direction),
                $event,
                $ok ? 'OK' : 'FAIL',
                $host !== '' ? $host : '-',
                $integrationId ?? '-',
                $message
            );
            Log::channel('single')->info('seventh-tradehub-connection: ' . $line);

            if (!Schema::hasTable('seventh_tradehub_connection_logs')) {
                $this->ensureConnectionLogsTable();
            }
            if (!Schema::hasTable('seventh_tradehub_connection_logs')) {
                return;
            }

            DB::table('seventh_tradehub_connection_logs')->insert([
                'created_at' => now(),
                'direction' => $direction,
                'event' => $event,
                'ok' => $ok,
                'http_status' => $httpStatus,
                'error_code' => $errorCode,
                'integration_id' => $integrationId,
                'context' => $context,
                'host' => $host !== '' ? $host : null,
                'message' => $message,
                'detail' => $detail,
            ]);

            $count = (int) DB::table('seventh_tradehub_connection_logs')->count();
            if ($count > 400) {
                $keepIds = DB::table('seventh_tradehub_connection_logs')
                    ->orderBy('id', 'desc')
                    ->limit(300)
                    ->pluck('id')
                    ->all();
                if (!empty($keepIds)) {
                    DB::table('seventh_tradehub_connection_logs')->whereNotIn('id', $keepIds)->delete();
                }
            }
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub connectionLog: ' . $e->getMessage());
        }
    }

    /**
     * @param int $limit
     * @return list<array<string, mixed>>
     */
    public function listConnectionLogs($limit = 50)
    {
        $limit = max(1, min(200, (int) $limit));
        try {
            $this->ensureSchema();
            $this->ensureConnectionLogsTable();
            if (!Schema::hasTable('seventh_tradehub_connection_logs')) {
                return [];
            }
            $rows = DB::table('seventh_tradehub_connection_logs')
                ->select([
                    'id', 'created_at', 'direction', 'event', 'ok', 'http_status',
                    'error_code', 'integration_id', 'context', 'host', 'message', 'detail',
                ])
                ->orderBy('id', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($row) {
                    return (array) $row;
                })
                ->all();

            return is_array($rows) ? $rows : [];
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub listConnectionLogs: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Save integration row fields (platform super admin).
     *
     * @param string $context
     * @param array<string, mixed> $data
     * @param int $adminId
     * @return array{ok: bool, error?: string, secret_format?: string, secret_readable?: bool}
     */
    public function saveIntegration($context, array $data, $adminId)
    {
        $adminId = (int) $adminId;
        $sa = Admin::where('id', $adminId)->where('is_super_admin', 1)->first();
        if (!$sa) {
            return ['ok' => false, 'error' => 'Super administrator access required'];
        }
        if (!in_array($context, [self::CONTEXT_DEMO, self::CONTEXT_OWNED], true)) {
            return ['ok' => false, 'error' => 'Invalid context'];
        }

        $this->ensureSchema();
        $this->ensureContextRow($context);

        $enabled = !empty($data['enabled']) ? 1 : 0;
        $integrationId = trim((string) ($data['integration_id'] ?? ''));
        $clientId = trim((string) ($data['client_id'] ?? ''));
        $expectedUser = trim((string) ($data['expected_user_email'] ?? ''));
        $expectedAdmin = trim((string) ($data['expected_admin_email'] ?? ''));

        $existing = $this->getByContext($context) ?: [];
        $clientSecretEnc = $existing['client_secret_enc'] ?? null;
        $webhookSecretEnc = $existing['webhook_secret_enc'] ?? null;

        $newSecret = trim((string) ($data['client_secret'] ?? $data['hub_client_secret'] ?? ''));
        $newWebhook = trim((string) ($data['webhook_secret'] ?? $data['hub_webhook_secret'] ?? ''));

        if ($clientSecretEnc !== null && $this->unsealSecret((string) $clientSecretEnc) === '') {
            $clientSecretEnc = null;
        }
        if ($webhookSecretEnc !== null && $this->unsealSecret((string) $webhookSecretEnc) === '') {
            $webhookSecretEnc = null;
        }

        if ($newSecret !== '') {
            $enc = $this->sealSecret($newSecret);
            if ($enc === '' || $this->unsealSecret($enc) !== $newSecret) {
                return ['ok' => false, 'error' => 'Failed to encode Client Secret'];
            }
            $clientSecretEnc = $enc;
        }

        if ($newWebhook !== '') {
            $enc = $this->sealSecret($newWebhook);
            if ($enc === '' || $this->unsealSecret($enc) !== $newWebhook) {
                return ['ok' => false, 'error' => 'Failed to encode Webhook Secret'];
            }
            $webhookSecretEnc = $enc;
        }

        if ($enabled && ($integrationId === '' || $clientId === '')) {
            return [
                'ok' => false,
                'error' => 'Integration ID and Client ID are required when enabling.',
            ];
        }

        if ($enabled && ($newSecret === '' && $this->unsealSecret((string) $clientSecretEnc) === '')) {
            return [
                'ok' => false,
                'error' => 'Paste the Client Secret into the field and Save. Leaving it blank cannot reuse an old/unreadable secret.',
            ];
        }

        if ($integrationId !== '') {
            $otherContext = $context === self::CONTEXT_DEMO
                ? self::CONTEXT_OWNED
                : self::CONTEXT_DEMO;
            $other = $this->getByContext($otherContext);
            $otherId = trim((string) ($other['integration_id'] ?? ''));
            if ($otherId !== '' && hash_equals($otherId, $integrationId)) {
                return [
                    'ok' => false,
                    'error' => 'Integration ID is already used by the other context. Demo and Owned must use different UUIDs.',
                ];
            }
        }

        try {
            DB::table('seventh_tradehub_integrations')
                ->where('context', $context)
                ->update([
                    'enabled' => $enabled,
                    'integration_id' => $integrationId !== '' ? $integrationId : null,
                    'client_id' => $clientId !== '' ? $clientId : null,
                    'client_secret_enc' => $clientSecretEnc,
                    'webhook_secret_enc' => $webhookSecretEnc,
                    'expected_user_email' => $expectedUser !== '' ? $expectedUser : null,
                    'expected_admin_email' => $expectedAdmin !== '' ? $expectedAdmin : null,
                    'updated_at' => now(),
                    'updated_by' => $adminId,
                ]);

            $stored = DB::table('seventh_tradehub_integrations')
                ->where('context', $context)
                ->select(['client_secret_enc', 'webhook_secret_enc', 'enabled', 'integration_id', 'client_id'])
                ->first();
            if (!$stored) {
                return ['ok' => false, 'error' => 'Save wrote nothing — context row missing after update'];
            }
            $stored = (array) $stored;

            $readSecret = $this->unsealSecret($stored['client_secret_enc'] ?? '');
            if ($enabled && $readSecret === '') {
                return [
                    'ok' => false,
                    'error' => 'Save did not store a readable Client Secret (format=' .
                        $this->secretFormat($stored['client_secret_enc'] ?? '') .
                        ', len=' . strlen((string) ($stored['client_secret_enc'] ?? '')) .
                        '). Paste Client Secret again and Save.',
                ];
            }

            if ($newSecret !== '' && $readSecret !== $newSecret) {
                return [
                    'ok' => false,
                    'error' => 'Client Secret round-trip mismatch after DB write. Contact support with this message.',
                ];
            }

            return [
                'ok' => true,
                'secret_format' => $this->secretFormat($stored['client_secret_enc'] ?? ''),
                'secret_readable' => $readSecret !== '',
            ];
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub saveIntegration: ' . $e->getMessage());
            $msg = $e->getMessage();
            if (stripos($msg, 'Duplicate') !== false || stripos($msg, 'uk_integration_id') !== false) {
                return ['ok' => false, 'error' => 'Integration ID must be unique. Demo and Owned cannot share the same UUID.'];
            }
            if (stripos($msg, "doesn't exist") !== false || stripos($msg, 'Unknown column') !== false) {
                return ['ok' => false, 'error' => 'Hub DB columns missing. Open Admin Settings once as super admin to run migrations, then retry.'];
            }

            return ['ok' => false, 'error' => 'Save failed: ' . $msg];
        }
    }

    /**
     * Public integration summary for admin UI (no secrets).
     *
     * @return array<string, mixed>
     */
    public function adminSummary()
    {
        $hubUrl = $this->hubUrl();
        $demo = $this->getByContext(self::CONTEXT_DEMO);
        $owned = $this->getByContext(self::CONTEXT_OWNED);

        $ownedSub = null;
        if ($owned && !empty($owned['integration_id'])) {
            $ownedSub = $this->getSubscription((string) $owned['integration_id']);
        }

        $siteUrl = rtrim((string) config('app.url'), '/');

        return [
            'hub_url' => $hubUrl,
            'endpoints' => [
                'health' => $siteUrl . '/api/7th-tradehub/v1/health',
                'consume' => $siteUrl . '/auth/7th-tradehub/demo/consume',
                'subscription_sync' => $siteUrl . '/api/7th-tradehub/v1/subscription/sync',
            ],
            'demo' => $this->formatIntegrationForAdmin($demo, self::CONTEXT_DEMO),
            'owned' => $this->formatIntegrationForAdmin($owned, self::CONTEXT_OWNED, $ownedSub),
            'shutdown' => $this->shutdownDiagnostic(),
            'connection_logs' => $this->listConnectionLogs(40),
            'http_available' => true,
        ];
    }

    /**
     * Readiness hints for admin UI (Hub is authoritative for SSO).
     *
     * @param string $context
     * @return array<string, mixed>
     */
    public function identityReadiness($context)
    {
        $integration = $this->getByContext($context);
        if (!$integration) {
            return ['configured' => false, 'checks' => []];
        }

        $checks = [];
        if ($context === self::CONTEXT_DEMO) {
            $checks[] = $this->readinessCheck(
                'demo_user',
                trim((string) ($integration['expected_user_email'] ?? '')),
                'user',
                true
            );
        }
        $checks[] = $this->readinessCheck(
            'admin',
            trim((string) ($integration['expected_admin_email'] ?? '')),
            'admin',
            false
        );

        return [
            'configured' => $this->isIntegrationOperational($integration),
            'enabled' => !empty($integration['enabled']),
            'integration_id' => trim((string) ($integration['integration_id'] ?? '')),
            'checks' => $checks,
        ];
    }

    /**
     * Parse Hub / local subscription timestamps.
     * Naive MySQL datetimes (no offset) are treated as UTC.
     *
     * @param string $value
     * @return DateTimeImmutable|null
     */
    public function parseUtcTimestamp($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        try {
            if (preg_match('/[zZ]|[+-]\d{2}:?\d{2}$/', $value)) {
                return (new DateTimeImmutable($value))->setTimezone(new DateTimeZone('UTC'));
            }

            return new DateTimeImmutable($value, new DateTimeZone('UTC'));
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * @param string|null $enc
     * @return string
     */
    public function maskSecret($enc)
    {
        if ($enc === null || trim((string) $enc) === '') {
            return '';
        }

        return '••••••••';
    }

    // -------------------------------------------------------------------------
    // Internals
    // -------------------------------------------------------------------------

    /**
     * @param string $label
     * @param string $email
     * @param string $expectedRole
     * @param bool $requireDemo
     * @return array<string, mixed>
     */
    protected function readinessCheck($label, $email, $expectedRole, $requireDemo)
    {
        if ($email === '') {
            return ['label' => $label, 'email' => '', 'ok' => null, 'message' => 'Not configured (optional hint)'];
        }

        if ($expectedRole === 'admin') {
            $admin = $this->findAdminByEmail($email);
            if (!$admin) {
                return ['label' => $label, 'email' => $email, 'ok' => false, 'message' => 'No local admin with this email'];
            }
            $ok = empty($admin->is_super_admin);

            return ['label' => $label, 'email' => $email, 'ok' => $ok, 'message' => $ok ? 'Ready' : 'Must be admin (not super admin)'];
        }

        $user = $this->findUserByEmail($email);
        if (!$user) {
            return ['label' => $label, 'email' => $email, 'ok' => false, 'message' => 'No local user with this email'];
        }
        $ok = !empty($user->is_demo_user);

        return ['label' => $label, 'email' => $email, 'ok' => $ok, 'message' => $ok ? 'Ready' : 'Must be demo user'];
    }

    /**
     * @param array<string, mixed>|null $integration
     * @param string $context
     * @param array<string, mixed>|null $subscription
     * @return array<string, mixed>
     */
    protected function formatIntegrationForAdmin($integration, $context, $subscription = null)
    {
        if (!$integration) {
            return ['context' => $context, 'enabled' => false, 'configured' => false];
        }

        return [
            'context' => $context,
            'enabled' => !empty($integration['enabled']),
            'configured' => $this->isIntegrationOperational($integration),
            'operational' => $this->operationalStatus($integration),
            'integration_id' => trim((string) ($integration['integration_id'] ?? '')),
            'client_id' => trim((string) ($integration['client_id'] ?? '')),
            'has_client_secret' => $this->clientSecret($integration) !== '',
            'has_webhook_secret' => $this->webhookSecret($integration) !== '',
            'expected_user_email' => trim((string) ($integration['expected_user_email'] ?? '')),
            'expected_admin_email' => trim((string) ($integration['expected_admin_email'] ?? '')),
            'capabilities' => $this->capabilitiesForContext($context),
            'readiness' => $this->identityReadiness($context),
            'subscription' => $subscription,
            'shutdown_active' => $context === self::CONTEXT_OWNED
                && !empty($integration['enabled'])
                && ($this->subscriptionIsOffline($subscription) || $this->subscriptionTrustExpired($subscription)),
        ];
    }

    /**
     * @param string $email
     * @return \App\Models\User|null
     */
    protected function findUserByEmail($email)
    {
        $email = trim((string) $email);
        if ($email === '') {
            return null;
        }
        $user = User::where('email', $email)->first();
        if ($user) {
            return $user;
        }

        return User::whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
    }

    /**
     * @param string $email
     * @return \App\Models\Admin|null
     */
    protected function findAdminByEmail($email)
    {
        $email = trim((string) $email);
        if ($email === '') {
            return null;
        }
        $admin = Admin::where('email', $email)->first();
        if ($admin) {
            return $admin;
        }

        return Admin::whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
    }

    /**
     * @param mixed $status
     * @return bool
     */
    protected function isInactiveStatus($status)
    {
        $status = strtolower(trim((string) $status));

        return in_array($status, ['deleted', 'inactive', 'blocked', 'suspended'], true);
    }

    /**
     * @param string $email
     * @return void
     */
    protected function updateOwnedExpectedAdminEmail($email)
    {
        $email = strtolower(trim((string) $email));
        if ($email === '') {
            return;
        }
        try {
            DB::table('seventh_tradehub_integrations')
                ->where('context', self::CONTEXT_OWNED)
                ->update([
                    'expected_admin_email' => $email,
                    'updated_at' => now(),
                ]);
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub updateOwnedExpectedAdminEmail: ' . $e->getMessage());
        }
    }

    /**
     * @param string $hubUrl
     * @param string $integrationId
     * @param string $clientId
     * @param string $clientSecret
     * @param string $webhookSecret
     * @param string $email
     * @param string $password
     * @param string $eventId
     * @param bool $inlineRetry
     * @return array{ok: bool, http_code: int, deduped?: bool, message?: string}
     */
    protected function postCredentialSync(
        $hubUrl,
        $integrationId,
        $clientId,
        $clientSecret,
        $webhookSecret,
        $email,
        $password,
        $eventId,
        $inlineRetry
    ) {
        $now = new DateTimeImmutable('now');
        $payload = [
            'integration_id' => $integrationId,
            'context' => self::CONTEXT_OWNED,
            'role' => 'credential_sync',
            'event' => 'owned.admin_credentials.updated',
            'event_id' => substr((string) $eventId, 0, 64),
            'request_id' => bin2hex(random_bytes(16)),
            'nonce' => bin2hex(random_bytes(12)),
            'issued_at' => $now->format(DateTimeInterface::ATOM),
            'expires_at' => $now->modify('+3 minutes')->format(DateTimeInterface::ATOM),
        ];
        if ($email !== '') {
            $payload['identity'] = ['email' => $email];
        }
        if ($password !== '') {
            $payload['credential'] = ['password' => $password];
        }

        $signed = $this->signPayload($payload, $clientSecret);
        $url = rtrim($hubUrl, '/') . '/webhooks/site-integrations/' . rawurlencode($integrationId);
        $attempts = $inlineRetry ? 3 : 1;
        $lastCode = 0;

        for ($i = 0; $i < $attempts; $i++) {
            if ($i > 0) {
                usleep(250000 * $i);
            }
            try {
                $response = Http::timeout(15)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'X-7TH-Webhook-Secret' => $webhookSecret,
                        'X-7TH-Client-Id' => $clientId,
                    ])
                    ->withBody(json_encode($signed, JSON_UNESCAPED_SLASHES), 'application/json')
                    ->post($url);

                $lastCode = $response->status();
                if ($lastCode >= 200 && $lastCode < 300) {
                    $decoded = $response->json();
                    $deduped = is_array($decoded) && !empty($decoded['deduped']);

                    return ['ok' => true, 'http_code' => $lastCode, 'deduped' => $deduped];
                }
                if ($lastCode >= 400 && $lastCode < 500 && $lastCode !== 429) {
                    Log::warning('SeventhTradeHub postCredentialSync: Hub HTTP ' . $lastCode);

                    return ['ok' => false, 'http_code' => $lastCode, 'message' => 'hub_client_error'];
                }
            } catch (Throwable $e) {
                Log::warning('SeventhTradeHub postCredentialSync: ' . $e->getMessage());
                $lastCode = 0;
            }
        }

        return ['ok' => false, 'http_code' => $lastCode, 'message' => 'hub_unreachable'];
    }

    /**
     * @return void
     */
    protected function ensureCredentialSyncOutbox()
    {
        if (self::$credentialOutboxReady) {
            return;
        }
        self::$credentialOutboxReady = true;
        try {
            if (Schema::hasTable('seventh_tradehub_credential_sync_outbox')) {
                return;
            }
            try {
                (new AdminDatabaseAutoMigrate())->run(null);
            } catch (Throwable $e) {
                Log::warning('SeventhTradeHub ensureCredentialSyncOutbox migrate: ' . $e->getMessage());
            }
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub ensureCredentialSyncOutbox: ' . $e->getMessage());
        }
    }

    /**
     * @param string $integrationId
     * @param string $email
     * @param string $password
     * @param string|null $eventId
     * @return void
     */
    protected function credentialSyncEnqueue($integrationId, $email, $password, $eventId)
    {
        $this->ensureCredentialSyncOutbox();
        $eventId = $eventId !== null && $eventId !== ''
            ? substr((string) $eventId, 0, 64)
            : bin2hex(random_bytes(16));
        $passwordEnc = $password !== '' ? $this->sealSecret($password) : null;
        try {
            if (!Schema::hasTable('seventh_tradehub_credential_sync_outbox')) {
                return;
            }
            $existing = DB::table('seventh_tradehub_credential_sync_outbox')
                ->where('event_id', $eventId)
                ->first();
            if ($existing) {
                DB::table('seventh_tradehub_credential_sync_outbox')
                    ->where('event_id', $eventId)
                    ->update([
                        'integration_id' => $integrationId,
                        'email' => $email !== '' ? $email : null,
                        'password_enc' => $passwordEnc,
                        'attempts' => DB::raw('attempts + 1'),
                        'next_attempt_at' => now()->addMinutes(5),
                        'last_error' => 'pending_retry',
                    ]);

                return;
            }
            DB::table('seventh_tradehub_credential_sync_outbox')->insert([
                'event_id' => $eventId,
                'integration_id' => $integrationId,
                'email' => $email !== '' ? $email : null,
                'password_enc' => $passwordEnc,
                'attempts' => 0,
                'next_attempt_at' => now(),
                'created_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub credentialSyncEnqueue: ' . $e->getMessage());
        }
    }

    /**
     * @param string $eventId
     * @return void
     */
    protected function credentialSyncDeleteByEventId($eventId)
    {
        if ($eventId === '') {
            return;
        }
        try {
            $this->ensureCredentialSyncOutbox();
            if (!Schema::hasTable('seventh_tradehub_credential_sync_outbox')) {
                return;
            }
            DB::table('seventh_tradehub_credential_sync_outbox')->where('event_id', $eventId)->delete();
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub credentialSyncDeleteByEventId: ' . $e->getMessage());
        }
    }

    /**
     * @return void
     */
    protected function ensureConnectionLogsTable()
    {
        if (self::$connectionLogsReady) {
            return;
        }
        try {
            if (Schema::hasTable('seventh_tradehub_connection_logs')) {
                self::$connectionLogsReady = true;

                return;
            }
            Schema::create('seventh_tradehub_connection_logs', function ($table) {
                $table->bigIncrements('id');
                $table->dateTime('created_at')->useCurrent();
                $table->string('direction', 16)->default('inbound');
                $table->string('event', 64);
                $table->boolean('ok')->default(0);
                $table->integer('http_status')->nullable();
                $table->string('error_code', 64)->nullable();
                $table->string('integration_id', 36)->nullable();
                $table->string('context', 32)->nullable();
                $table->string('host', 255)->nullable();
                $table->string('message', 512);
                $table->text('detail')->nullable();
                $table->index('created_at');
                $table->index('event');
                $table->index('integration_id');
            });
            self::$connectionLogsReady = true;
        } catch (Throwable $e) {
            Log::warning('SeventhTradeHub ensureConnectionLogsTable: ' . $e->getMessage());
        }
    }

    /**
     * @param string $name
     * @param array<string, string>|null $headers
     * @return string
     */
    protected function header($name, $headers = null)
    {
        if (is_array($headers)) {
            foreach ($headers as $key => $value) {
                if (strcasecmp((string) $key, $name) === 0) {
                    return trim((string) $value);
                }
            }

            return '';
        }

        try {
            if (function_exists('request') && request()) {
                return trim((string) request()->header($name, ''));
            }
        } catch (Throwable $e) {
            // fall through
        }

        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));

        return trim((string) ($_SERVER[$key] ?? ''));
    }

    /**
     * @return string
     */
    protected function requestHost()
    {
        try {
            if (function_exists('request') && request()) {
                $host = trim((string) request()->getHost());
                if ($host !== '') {
                    return $host;
                }
            }
        } catch (Throwable $e) {
            // fall through
        }
        $host = trim((string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? ''));
        if ($host !== '') {
            return $host;
        }
        $parsed = parse_url((string) config('app.url'));

        return trim((string) ($parsed['host'] ?? ''));
    }

    /**
     * @return string
     */
    protected function guessProtocolEvent()
    {
        $uri = '';
        try {
            if (function_exists('request') && request()) {
                $uri = (string) request()->getRequestUri();
            }
        } catch (Throwable $e) {
            $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
        }
        if ($uri === '') {
            $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
        }
        if (stripos($uri, 'subscription/sync') !== false) {
            return 'subscription_sync';
        }
        if (stripos($uri, '/health') !== false) {
            return 'health';
        }
        if (stripos($uri, 'consume') !== false) {
            return 'sso_consume';
        }

        return 'protocol';
    }

    /**
     * @param string $error
     * @param int $code
     * @param array<string, mixed> $logBase
     * @param string $message
     * @param mixed $detail
     * @return array{ok: bool, error: string, code: int, integration: null}
     */
    protected function inboundReject($error, $code, array $logBase, $message, $detail = null)
    {
        $this->connectionLog([
            'direction' => 'inbound',
            'event' => $logBase['event'] ?? 'protocol',
            'ok' => false,
            'http_status' => $code,
            'error_code' => $error,
            'integration_id' => $logBase['integration_id'] ?? null,
            'context' => $logBase['context'] ?? null,
            'message' => $message,
            'detail' => $detail !== null ? $detail : [
                'request_uri' => (string) ($_SERVER['REQUEST_URI'] ?? ''),
                'method' => (string) ($_SERVER['REQUEST_METHOD'] ?? ''),
            ],
        ]);

        return [
            'ok' => false,
            'error' => $error,
            'code' => $code,
            'integration' => null,
        ];
    }
}
