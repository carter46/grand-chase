@extends('layouts.app')
@section('content')
    @include('admin.topmenu')
    @include('admin.sidebar')
    <div class="main-panel">
        <div class="content  ">
            <div class="page-inner">
                <div class="admin-page-head">
                    <div>
                        <h1 class="title1 mb-1">Deposits</h1>
                        <p class="text-muted mb-0">Review and process client deposits</p>
                    </div>
                </div>
                <x-danger-alert />
                <x-success-alert />
                <div class="mb-5 row">

                    <div class="col-12 card shadow p-4 ">
                        <div class="admin-desktop-table admin-table-fit">
                            <table id="ShipTable" class="table table-hover admin-compact-table">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th class="text-right"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deposits as $deposit)
                                        <tr>
                                            <td data-order="{{ $deposit->id }}">
                                                <span class="admin-cell-stack">
                                                    <strong>{{ optional($deposit->duser)->name ?? 'N/A' }}</strong>
                                                    <small>#{{ $deposit->id }} &middot; {{ optional($deposit->duser)->email }}</small>
                                                </span>
                                            </td>
                                            <td data-order="{{ $deposit->amount }}">
                                                <span class="admin-cell-stack">
                                                    <strong>{{ $settings->currency }}{{ number_format($deposit->amount) }}</strong>
                                                    <small>{{ $deposit->payment_mode }}</small>
                                                </span>
                                            </td>
                                            <td data-order="{{ $deposit->created_at->timestamp }}">
                                                <span class="admin-cell-stack">
                                                    <span><span class="badge {{ $deposit->status == 'Processed' ? 'badge-success' : 'badge-danger' }}">{{ $deposit->status }}</span></span>
                                                    <small>{{ $deposit->created_at->format('M j, Y g:i A') }}</small>
                                                </span>
                                            </td>
                                            <td class="text-right text-nowrap">
                                                @if ($deposit->status != 'Processed')
                                                    <a class="btn btn-primary btn-sm"
                                                        href="{{ url('admin/dashboard/pdeposit') }}/{{ $deposit->id }}">Process</a>
                                                @endif
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-light btn-sm admin-kebab" type="button"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Actions">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="{{ route('viewdepositimage', $deposit->id) }}">
                                                            <i class="fa fa-eye mr-2"></i> View payment proof
                                                        </a>
                                                        <a class="dropdown-item text-danger" href="{{ url('admin/dashboard/deldeposit') }}/{{ $deposit->id }}"
                                                            onclick="return confirm('Delete this deposit?');">
                                                            <i class="fa fa-trash mr-2"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="admin-mobile-list">
                            @foreach ($deposits as $deposit)
                                <x-admin.mobile-list-card
                                    :title="optional($deposit->duser)->name ?? 'N/A'"
                                    :subtitle="optional($deposit->duser)->email"
                                    :badge="$deposit->status"
                                    :badge-class="$deposit->status == 'Processed' ? 'badge-success' : 'badge-danger'"
                                    :meta="[
                                        ['label' => 'ID', 'value' => $deposit->id],
                                        ['label' => 'Amount', 'value' => $settings->currency . number_format($deposit->amount)],
                                        ['label' => 'Method', 'value' => $deposit->payment_mode],
                                        ['label' => 'Date', 'value' => $deposit->created_at->toDayDateTimeString()],
                                    ]"
                                >
                                    <a href="{{ route('viewdepositimage', $deposit->id) }}" class="btn btn-info btn-sm">View</a>
                                    <a href="{{ url('admin/dashboard/deldeposit') }}/{{ $deposit->id }}" class="btn btn-danger btn-sm">Delete</a>
                                    @if ($deposit->status != 'Processed')
                                        <a class="btn btn-primary btn-sm" href="{{ url('admin/dashboard/pdeposit') }}/{{ $deposit->id }}">Process</a>
                                    @endif
                                </x-admin.mobile-list-card>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
