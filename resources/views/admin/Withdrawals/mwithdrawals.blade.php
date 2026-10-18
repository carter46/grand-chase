<?php
if (Auth('admin')->User()->dashboard_style == 'light') {
    $text = 'dark';
} else {
    $text = 'light';
}
?>
@extends('layouts.app')
@section('content')
    @include('admin.topmenu')
    @include('admin.sidebar')
    <div class="main-panel">
        <div class="content ">
            <div class="page-inner">
                <div class="admin-page-head">
                    <div>
                        <h1 class="title1 mb-1">Transfer Transactions</h1>
                        <p class="text-muted mb-0">Review and process client transfers</p>
                    </div>
                </div>
                <x-danger-alert />
                <x-success-alert />
                @php
                    $beneficiaryFields = [
                        'International Wire Transfer' => 'accountname',
                        'PayPal' => 'paypal_email',
                        'Wise Transfer' => 'wise_fullname',
                        'Skrill' => 'skrill_fullname',
                        'Venmo' => 'venmo_username',
                        'Zelle' => 'zelle_name',
                        'Cash App' => 'cash_app_tag',
                        'Revolut' => 'revolut_fullname',
                        'Alipay' => 'alipay_fullname',
                        'WeChat Pay' => 'wechat_name',
                    ];
                    $beneficiaryOf = function ($w) use ($beneficiaryFields) {
                        if ($w->payment_mode == 'Cryptocurrency') {
                            return ($w->crypto_currency ?? 'N/A') . ' Wallet';
                        }
                        $field = $beneficiaryFields[$w->payment_mode] ?? 'accountname';
                        return $w->{$field} ?? 'N/A';
                    };
                    $statusBadge = function ($status) {
                        return $status == 'Processed' ? 'badge-success' : ($status == 'On-hold' ? 'badge-warning' : 'badge-danger');
                    };
                @endphp
                <div class="mb-5 row">
                    <div class="col card p-3 shadow ">
                        <div class="admin-desktop-table admin-table-fit">
                                <table id="ShipTable" class="table table-hover admin-compact-table">
                                    <thead>
                                        <tr>
                                            <th>Account holder</th>
                                            <th>Amount</th>
                                            <th>Beneficiary</th>
                                            <th>Status</th>
                                            <th class="text-right"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($withdrawals as $deposit)
                                            <tr>
                                                <td data-order="{{ $deposit->id }}">
                                                    <span class="admin-cell-stack">
                                                        <strong>{{ optional($deposit->duser)->name ?? 'N/A' }}</strong>
                                                        <small>#{{ $deposit->id }}</small>
                                                    </span>
                                                </td>
                                                <td data-order="{{ $deposit->amount }}">
                                                    <span class="admin-cell-stack">
                                                        <strong>{{ $settings->currency }}{{ number_format($deposit->amount) }}</strong>
                                                        <small>{{ $deposit->payment_mode }}</small>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="admin-cell-stack">
                                                        <span>{{ $beneficiaryOf($deposit) }}</span>
                                                        @if ($deposit->Description)
                                                            <small title="{{ $deposit->Description }}">{{ \Illuminate\Support\Str::limit($deposit->Description, 40) }}</small>
                                                        @endif
                                                    </span>
                                                </td>
                                                <td data-order="{{ \Carbon\Carbon::parse($deposit->created_at)->timestamp }}">
                                                    <span class="admin-cell-stack">
                                                        <span><span class="badge {{ $statusBadge($deposit->status) }}">{{ $deposit->status }}</span></span>
                                                        <small>{{ \Carbon\Carbon::parse($deposit->created_at)->format('M j, Y g:i A') }}</small>
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <a href="{{ route('processwithdraw', $deposit->id) }}"
                                                        class="btn btn-light btn-sm admin-kebab" title="View" aria-label="View"><i class="fa fa-eye"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                        </div>
                        <div class="admin-mobile-list">
                            @foreach ($withdrawals as $deposit)
                                @php
                                    $beneficiary = $beneficiaryOf($deposit);
                                    $wBadge = $statusBadge($deposit->status);
                                @endphp
                                <x-admin.mobile-list-card
                                    :title="optional($deposit->duser)->name ?? 'N/A'"
                                    :subtitle="$settings->currency . number_format($deposit->amount)"
                                    :badge="$deposit->status"
                                    :badge-class="$wBadge"
                                    :meta="[
                                        ['label' => 'ID', 'value' => $deposit->id],
                                        ['label' => 'Type', 'value' => $deposit->payment_mode],
                                        ['label' => 'Beneficiary', 'value' => $beneficiary],
                                        ['label' => 'Description', 'value' => $deposit->Description],
                                        ['label' => 'Date', 'value' => \Carbon\Carbon::parse($deposit->created_at)->toDayDateTimeString()],
                                    ]"
                                >
                                    <a href="{{ route('processwithdraw', $deposit->id) }}" class="btn btn-info btn-sm">View</a>
                                </x-admin.mobile-list-card>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection