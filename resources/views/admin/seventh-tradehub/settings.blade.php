@extends('layouts.app')
@section('content')
    @include('admin.topmenu')
    @include('admin.sidebar')
    @php
        $text = Auth('admin')->User()->dashboard_style == 'dark' ? 'light' : 'dark';
        $demo = $summary['demo'] ?? [];
        $owned = $summary['owned'] ?? [];
        $shutdown = $summary['shutdown'] ?? ['active' => false, 'reason' => ''];
        $endpoints = $summary['endpoints'] ?? [];
    @endphp
    <div class="main-panel">
        <div class="content">
            <div class="page-inner">
                <div class="mt-2 mb-4">
                    <h1 class="title1">7th Trade Hub Integration</h1>
                    <p class="text-muted">Protocol v1 — Demo + Owned credentials, SSO, subscription shutdown.</p>
                </div>
                <x-danger-alert />
                <x-success-alert />

                @if ($canClaim)
                    <div class="alert alert-warning">
                        <strong>No platform super admin yet.</strong>
                        Claim this role for Hub settings (separate from site Super Admin type). No credentials are hardcoded.
                        <form method="POST" action="{{ route('admin.seventh-tradehub.claim') }}" class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm">Claim platform super admin</button>
                        </form>
                    </div>
                @endif

                @if ($isPlatformSa && $summary)
                    @if (!empty($shutdown['active']))
                        <div class="alert alert-danger">
                            <strong>Shutdown ACTIVE</strong> — {{ $shutdown['reason'] ?? '' }}
                        </div>
                    @else
                        <div class="alert alert-secondary">
                            Shutdown not active — {{ $shutdown['reason'] ?? 'n/a' }}
                        </div>
                    @endif

                    <div class="card shadow p-3 mb-4">
                        <h4>Shared Hub URL</h4>
                        <form method="POST" action="{{ route('admin.seventh-tradehub.save') }}">
                            @csrf
                            <input type="hidden" name="context" value="demo">
                            <input type="hidden" name="enabled" value="{{ !empty($demo['enabled']) ? 1 : 0 }}">
                            <input type="hidden" name="integration_id" value="{{ $demo['integration_id'] ?? '' }}">
                            <input type="hidden" name="client_id" value="{{ $demo['client_id'] ?? '' }}">
                            <input type="hidden" name="expected_user_email" value="{{ $demo['expected_user_email'] ?? '' }}">
                            <input type="hidden" name="expected_admin_email" value="{{ $demo['expected_admin_email'] ?? '' }}">
                            <div class="form-group">
                                <label>Hub base URL</label>
                                <input type="url" name="hub_url" class="form-control" value="{{ $summary['hub_url'] ?? '' }}"
                                    placeholder="https://7th-tradehub.online">
                                <small class="text-muted">Env <code>SEVENTH_TRADEHUB_HUB_URL</code> overrides this when set.</small>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Save Hub URL</button>
                        </form>
                        <hr>
                        <p class="mb-1"><strong>Merchant endpoints (fixed Protocol v1 paths):</strong></p>
                        <ul class="small mb-0">
                            <li>Health: <code>{{ $endpoints['health'] ?? '' }}</code></li>
                            <li>SSO consume: <code>{{ $endpoints['consume'] ?? '' }}</code></li>
                            <li>Subscription sync: <code>{{ $endpoints['subscription_sync'] ?? '' }}</code></li>
                        </ul>
                    </div>

                    <div class="row">
                        @foreach ([['demo', $demo, 'Demo'], ['owned_tool', $owned, 'Owned']] as $card)
                            @php
                                $ctx = $card[0];
                                $row = $card[1];
                                $label = $card[2];
                            @endphp
                            <div class="col-md-6 mb-4">
                                <div class="card shadow p-3 h-100">
                                    <h4>{{ $label }} integration</h4>
                                    <p class="small text-muted">
                                        Capabilities: {{ implode(', ', $row['capabilities'] ?? []) }}
                                    </p>
                                    <p class="small">
                                        Status:
                                        @if (!empty($row['configured']))
                                            <span class="badge badge-success">Ready</span>
                                        @else
                                            <span class="badge badge-warning">{{ $row['operational']['reason'] ?? 'Incomplete' }}</span>
                                        @endif
                                    </p>
                                    <form method="POST" action="{{ route('admin.seventh-tradehub.save') }}">
                                        @csrf
                                        <input type="hidden" name="context" value="{{ $ctx }}">
                                        <div class="form-group">
                                            <label><input type="checkbox" name="enabled" value="1" {{ !empty($row['enabled']) ? 'checked' : '' }}> Enabled</label>
                                        </div>
                                        <div class="form-group">
                                            <label>Integration ID</label>
                                            <input type="text" name="integration_id" class="form-control" value="{{ $row['integration_id'] ?? '' }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Client ID</label>
                                            <input type="text" name="client_id" class="form-control" value="{{ $row['client_id'] ?? '' }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Client Secret {{ !empty($row['has_client_secret']) ? '(saved •••••••• — paste to replace)' : '' }}</label>
                                            <input type="password" name="client_secret" class="form-control" autocomplete="new-password" placeholder="Paste secret">
                                        </div>
                                        <div class="form-group">
                                            <label>Webhook Secret {{ !empty($row['has_webhook_secret']) ? '(saved ••••••••)' : '' }}</label>
                                            <input type="password" name="webhook_secret" class="form-control" autocomplete="new-password" placeholder="Optional for ping / credential sync">
                                        </div>
                                        @if ($ctx === 'demo')
                                            <div class="form-group">
                                                <label>Expected demo user email</label>
                                                <input type="email" name="expected_user_email" class="form-control" value="{{ $row['expected_user_email'] ?? '' }}">
                                            </div>
                                        @endif
                                        <div class="form-group">
                                            <label>Expected admin email</label>
                                            <input type="email" name="expected_admin_email" class="form-control" value="{{ $row['expected_admin_email'] ?? '' }}">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm">Save {{ $label }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.seventh-tradehub.webhook-ping') }}" class="mt-2 d-inline">
                                        @csrf
                                        <input type="hidden" name="context" value="{{ $ctx }}">
                                        <button type="submit" class="btn btn-secondary btn-sm">Test webhook</button>
                                    </form>
                                    @if ($ctx === 'owned_tool')
                                        <form method="POST" action="{{ route('admin.seventh-tradehub.poll') }}" class="mt-2 d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-info btn-sm">Pull subscription</button>
                                        </form>
                                        <hr>
                                        <form method="POST" action="{{ route('admin.seventh-tradehub.sync-password') }}">
                                            @csrf
                                            <label class="small">Sync owned admin password to Hub</label>
                                            <div class="input-group">
                                                <input type="password" name="password" class="form-control form-control-sm" placeholder="Local admin password">
                                                <div class="input-group-append">
                                                    <button class="btn btn-sm btn-outline-primary" type="submit">Sync</button>
                                                </div>
                                            </div>
                                        </form>
                                    @endif
                                    @if (!empty($row['readiness']['checks']))
                                        <hr>
                                        <p class="small mb-1"><strong>Local readiness</strong></p>
                                        <ul class="small mb-0">
                                            @foreach ($row['readiness']['checks'] as $check)
                                                <li>
                                                    {{ $check['label'] ?? '' }}:
                                                    {{ $check['email'] ?? '' }} —
                                                    {{ $check['message'] ?? '' }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card shadow p-3">
                                <h4>Create demo user</h4>
                                <p class="small text-muted">Hidden from Manage Users. Required for Hub “Login as User”.</p>
                                <form method="POST" action="{{ route('admin.seventh-tradehub.create-demo-user') }}">
                                    @csrf
                                    <div class="form-group">
                                        <input type="text" name="name" class="form-control" placeholder="Full name" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" class="form-control" placeholder="Email (must match Hub)" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" class="form-control" placeholder="Password" required minlength="6">
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm">Create demo user</button>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow p-3">
                                <h4>Create demo / owned admin</h4>
                                <p class="small text-muted">Regular admin (not platform SA). Used for Hub admin SSO.</p>
                                <form method="POST" action="{{ route('admin.seventh-tradehub.create-demo-admin') }}">
                                    @csrf
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <input type="text" name="firstName" class="form-control" placeholder="First name" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <input type="text" name="lastName" class="form-control" placeholder="Last name" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" class="form-control" placeholder="Email (must match Hub)" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="phone" class="form-control" placeholder="Phone">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" class="form-control" placeholder="Password" required minlength="6">
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm">Create admin</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow p-3 mb-5">
                        <h4>Connection logs</h4>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>When</th>
                                        <th>Event</th>
                                        <th>OK</th>
                                        <th>HTTP</th>
                                        <th>Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($logs as $log)
                                        <tr>
                                            <td class="small">{{ $log['created_at'] ?? '' }}</td>
                                            <td class="small">{{ $log['event'] ?? '' }}</td>
                                            <td>{{ !empty($log['ok']) ? 'yes' : 'no' }}</td>
                                            <td>{{ $log['http_status'] ?? '' }}</td>
                                            <td class="small">{{ $log['message'] ?? '' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5">No logs yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
