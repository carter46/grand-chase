<?php
if (Auth('admin')->User()->dashboard_style == 'light') {
    $text = 'dark';
    $bg = 'light';
} else {
    $text = 'light';
    $bg = 'dark';
}
?>
@extends('layouts.app')
@section('content')
    @include('admin.topmenu')
    @include('admin.sidebar')
    <div class="main-panel">
        <div class="content ">
            <div class="page-inner">
                <x-danger-alert />
                <x-success-alert />
                <!-- Beginning of  Dashboard Stats  -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="p-3 card ">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 ">
                                        
                                          <img alt="" src="{{$settings->site_address}}/storage/app/public/photos/{{$user->profile_photo_path}}" width="60" height="60" style='border-radius: 50%;'><h1 class=" pl-2 d-inline text-primary">{{ $user->name }} {{ $user->middlename }} {{ $user->lastname }}</h1><span></span>
                                        <div class="d-inline">
                                            <div class="float-right btn-group">
                                                <a class="btn btn-primary btn-sm" href="{{ route('manageusers') }}"> <i
                                                        class="fa fa-arrow-left"></i> back</a> &nbsp;
                                                <button type="button" class="btn btn-secondary dropdown-toggle btn-sm"
                                                    data-toggle="dropdown" data-display="static" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    Actions
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-lg-right">
                                                    
                                                    {{-- @if ($user->trade_mode == 'on')
                                                        <a class="dropdown-item"
                                                            href="{{ url('admin/dashboard/usertrademode') }}/{{ $user->id }}/off">Turn
                                                            off trade</a>
                                                    @else --}}
                                                        {{-- <a class="dropdown-item"
                                                            href="{{ url('admin/dashboard/usertrademode') }}/{{ $user->id }}/on">Turn
                                                            on trade</a>
                                                    @endif --}}
                                                    @if ($user->email_verified_at)
                                                    @else
                                                        <a href="{{ url('admin/dashboard/email-verify') }}/{{ $user->id }}"
                                                            class="dropdown-item">Verify Email</a>
                                                    @endif
                                                    {{-- <a href="#"  data-toggle="modal" data-target="#userAction" class="dropdown-item">Add upgrade Action</a> --}}
                                                {{-- <a href="#"  data-toggle="modal" data-target="#userActionsignal" class="dropdown-item">Add signal Action</a> --}}
                                                    <a href="#" data-toggle="modal" data-target="#topupModal"
                                                        class="dropdown-item">Fund/Debit Account</a>
                                                        <a href="#" data-toggle="modal" data-target="#TradingModal"
                                                        class="dropdown-item">Change Profile Pics</a>
                                                        <a href="#" data-toggle="modal" data-target="#resetpswdModal"
                                                        class="dropdown-item">Reset Password</a>
                                                        @if ($user->account_status != 'active') 
                                                        <a class="dropdown-item"
                                                            href="{{ url('admin/dashboard/undormant') }}/{{ $user->id }}">Turn Off Domarnt Account</a>
                                                    @else
                                                        <a class="dropdown-item"
                                                            href="{{ url('admin/dashboard/dormant') }}/{{ $user->id }}">Turn On Dormant Account </a>
                                                    @endif
                                                    <a href="#" data-toggle="modal" data-target="#clearacctModal"
                                                        class="dropdown-item">Clear Account</a>

                                                    
                                                    <a href="#" data-toggle="modal" data-target="#edituser"
                                                        class="dropdown-item">Edit</a>
                                                    {{-- <a href="{{ route('showusers', $user->id) }}" class="dropdown-item">Add
                                                        Referral</a> --}}
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#sendmailtooneuserModal" class="dropdown-item">Send
                                                        Email</a>

                                                    <a href="#" data-toggle="modal" data-target="#switchuserModal"
                                                        class="dropdown-item text-success">Login as {{ $user->name }}</a>
                                                        <a class="dropdown-item"
                                                        href="{{ route('loginactivity', $user->id) }}">Login Activity</a>
                                                    @if ($user->status == null || $user->status == 'blocked')
                                                        <a class="dropdown-item"
                                                            href="{{ url('admin/dashboard/uunblock') }}/{{ $user->id }}">Unblock</a>
                                                    @else
                                                        <a class="dropdown-item"
                                                            href="{{ url('admin/dashboard/uublock') }}/{{ $user->id }}">Block</a>
                                                    @endif
                                                        <a href="#" data-toggle="modal" data-target="#deleteModal"
                                                        class="dropdown-item text-danger">Delete {{ $user->name }}</a>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 mt-4 border rounded row ">
                                    <div class="col-md-3">
                                        <h5 class="text-bold">Account Balance</h5>
                                        <p>{{ $user->currency ?? $settings->currency }}{{ number_format($user->account_bal) }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Account Limit</h5>
                                        <p>{{ $user->currency ?? $settings->currency }}{{ number_format($user->limit) }} </p>
                                    </div>
                                    
                                    
                                    {{-- <div class="col-md-3">
                                        <h5>User Account Status</h5>
                                        @if ($user->status == 'blocked')
                                            <span class="badge badge-danger">Blocked</span>
                                        @elseif($user->status == 'unhold')
                                        <span class="badge badge-warning">Unhold</span>
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </div> --}}
                                    <div class="col-md-3">
                                        <h5>Loans</h5>
                                        {{-- <span class="text-bold"> <strong>2</strong> </span> --}}
                                        @if ($user->plan != null)
                                            <a class="btn btn-sm btn-primary d-inline"
                                                href="{{ route('user.plans', $user->id) }}">Veiw loans</a>
                                        @else
                                            <p>No Loan</p>
                                        @endif

                                    </div>
                                    <div class="col-md-3">
                                        <h5>KYC</h5>
                                        @php
                                            $kycStatus = strtolower($user->account_verify ?? '');
                                        @endphp
                                        @if ($kycStatus === 'verified')
                                            <span class="badge badge-success">Verified</span>
                                        @elseif ($kycStatus === 'under review')
                                            <span class="badge badge-warning text-dark">Under Review</span>
                                        @elseif ($kycStatus === 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @else
                                            <span class="badge badge-danger">Not Verified Yet</span>
                                        @endif

                                        @if (!empty($latestKyc))
                                            <div class="mt-2">
                                                <a href="{{ route('viewkyc', $latestKyc->id) }}" class="btn btn-sm btn-primary">
                                                    View Latest Submission
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    {{-- <div class="col-md-3">
                                        <h5>Trade Mode</h5>
                                        @if ($user->trade_mode == 'off' || $user->trade_mode == null)
                                            <span class="badge badge-danger">Off</span>
                                        @else
                                            <span class="badge badge-success">On</span>
                                        @endif
                                    </div> --}}
                                </div>
                                <div class="mt-3 row ">
                                    <div class="col-md-12">
                                        <h5>USER INFORMATION</h5>
                                    </div>
                                </div>
                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>Fullname</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->name }} {{ $user->middlename }} {{ $user->lastname }}</h5>
                                    </div>
                                </div>
                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>Email Address</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->email }}</h5>
                                    </div>
                                </div>
                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>Mobile Number</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->phone }}</h5>
                                    </div>
                                </div>


                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>Account Number</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->usernumber }}</h5>
                                    </div>
                                </div>
                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>4 Digit Transaction Pin</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->pin }}</h5>
                                    </div>
                                </div>
                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>IRS Filing No.</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->irs_filing_id }}</h5>
                                    </div>
                                </div>

                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>{{ $settings->code1 }} Code</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->code1 }}</h5>
                                    </div>
                                </div>

                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>{{ $settings->code2 }} Code</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->code2 }}</h5>
                                    </div>
                                </div>

                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>{{ $settings->code3 }} Code</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->code3 }}</h5>
                                    </div>
                                </div>
                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>Date of birth</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->dob }}</h5>
                                    </div>
                                </div>
                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>Nationality</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $user->country }}</h5>
                                    </div>
                                </div>
                                {{-- <div class="p-3 border row ">
                                <div class="col-md-4 border-right">
                                    <h5>Wallet Address</h5>
                                </div>
                                <div class="col-md-8">
                                   <h5>@if ($user->wallet_address)
                                    {{$user->wallet_address}}
                                   @else
                                   Not added yet!
                                   @endif</h5>
                                </div>
                            </div> --}}
                                <div class="p-3 border row ">
                                    <div class="col-md-4 border-right">
                                        <h5>Registered</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ \Carbon\Carbon::parse($user->created_at)->toDayDateTimeString() }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.Users.users_actions')
    @endsection
