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
                <div class="mt-2 mb-4 admin-detail-head">
                    <h1 class="title1"> {{ $user->name }} Loans</h1>
                    <div class="btn-group">
                        <a class="btn btn-primary btn-sm" href="{{ route('viewuser', $user->id) }}"> <i
                                class="fa fa-arrow-left"></i> back</a>
                    </div>
                </div>
                <x-danger-alert />
                <x-success-alert />
                <div class="mb-5 row">
                    <div class="col card p-3 shadow ">
                        <div class="admin-desktop-table bs-example widget-shadow table-responsive" data-example-id="hoverable-table">
                            <span style="margin:3px;">
                                <table id="ShipTable" class="table table-hover ">
                                    <thead>
                                        <tr>
                                            {{-- <th>Client name</th> --}}
                                            
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Duration</th>
                                            <th>Created on</th>
        
                                            <th>Purpose</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($plans as $plan)
                                            <tr>
                                                {{-- <td>{{$plan->duser->name}}</td> --}}
                                                
                                                <td>{{ $settings->currency }}{{ number_format($plan->amount) }}</td>
                                                <td>
                                                    @if ($plan->active != 'Pending')
                                                        <span class="badge badge-success">{{ $plan->active }}</span>
                                                    @else
                                                        <span class="badge badge-warning">{{ $plan->active }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $plan->inv_duration }}</td>
                                                <td>{{ \Carbon\Carbon::parse($plan->created_at)->toDayDateTimeString() }}
                                                </td>
                                                
                                                </td>
                                                <td>
                                                    <a href="{{ route('deleteplan', $plan->id) }}"
                                                        class="m-1 btn btn-info btn-sm"> Delete Plan</a>
                                                    @if ($plan->active == 'Processed')
                                                        <a href="{{ route('markas', ['id' => $plan->id, 'status' => 'Pending']) }}"
                                                            class="m-1 btn btn-danger btn-sm">Mark as Pending</a>
                                                    @else
                                                        <a href="{{ route('markas', ['id' => $plan->id, 'status' => 'Processed']) }}"
                                                            class="m-1 btn btn-success btn-sm">Mark as Processed</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                        </div>
                        <div class="admin-mobile-list">
                            @foreach ($plans as $plan)
                                <x-admin.mobile-list-card
                                    :title="$settings->currency . number_format($plan->amount)"
                                    :subtitle="$plan->purpose"
                                    :badge="$plan->active"
                                    :badge-class="$plan->active != 'Pending' ? 'badge-success' : 'badge-warning'"
                                    :meta="[
                                        ['label' => 'Duration', 'value' => $plan->inv_duration],
                                        ['label' => 'Created', 'value' => \Carbon\Carbon::parse($plan->created_at)->toDayDateTimeString()],
                                    ]"
                                >
                                    <a href="{{ route('deleteplan', $plan->id) }}" class="btn btn-info btn-sm">Delete</a>
                                    @if ($plan->active == 'Processed')
                                        <a href="{{ route('markas', ['id' => $plan->id, 'status' => 'Pending']) }}" class="btn btn-danger btn-sm">Mark Pending</a>
                                    @else
                                        <a href="{{ route('markas', ['id' => $plan->id, 'status' => 'Processed']) }}" class="btn btn-success btn-sm">Mark Processed</a>
                                    @endif
                                </x-admin.mobile-list-card>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
