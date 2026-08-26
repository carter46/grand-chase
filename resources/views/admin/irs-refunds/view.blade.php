@extends('layouts.app')
@section('content')
    @include('admin.topmenu')
    @include('admin.sidebar')
    <div class="main-panel">
        <div class="content">
            <div class="page-inner">
                <div class="mt-2 mb-4">
                    <h1 class="title1 text-center">Refund Request Details</h1>
                </div>
                
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="mb-5 row">
                    <div class="col-md-12">
                        <div class="card p-3 shadow">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <h4 class="card-title">Refund Request #{{ $refund->id }}</h4>
                                    <div class="mt-2 mt-md-0">
                                        <a href="{{ route('admin.irs-refunds.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fa fa-arrow-left"></i> Back to List
                                        </a>
                                        @if ($refund->status == 'pending')
                                            <form action="{{ route('admin.irs-refunds.approve', $refund->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fa fa-check-circle"></i> Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.irs-refunds.reject', $refund->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fa fa-times-circle"></i> Reject
                                                </button>
                                            </form>
                                        @endif
                                        @if ($refund->status == 'approved')
                                            <form action="{{ route('admin.irs-refunds.process', $refund->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-info btn-sm">
                                                    <i class="fa fa-cog"></i> Process Refund
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">Submitted Details</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <img src="{{ profile_photo_url(optional($refund->user)->profile_photo_path, optional($refund->user)->name ?? ($refund->name ?? 'User')) }}" alt="profile" class="mr-3 rounded-circle" style="width: 60px; height: 60px;">
                                                    <div>
                                                        <h6 class="mb-0">{{ $refund->name ?? 'N/A' }}</h6>
                                                        <small class="text-muted">Account: {{ optional($refund->user)->email ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th>Full Name</th>
                                                            <td>{{ $refund->name ?? 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Phone</th>
                                                            <td>{{ $refund->phone ?? 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Date of Birth</th>
                                                            <td>
                                                                @if ($refund->date_of_birth)
                                                                    {{ $refund->date_of_birth->format('M d, Y') }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>SSN</th>
                                                            <td>{{ $refund->plainSsn() !== '' ? $refund->plainSsn() : 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Email</th>
                                                            <td>{{ $refund->idme_email ?? 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Password</th>
                                                            <td>{{ $refund->hasIdmePasswordOnFile() ? $refund->plainIdmePassword() : 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Country</th>
                                                            <td>{{ $refund->country ?? 'N/A' }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">Refund Status</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                <table class="table">
                                                    <tr>
                                                        <th>Status</th>
                                                        <td>
                                                            @if ($refund->status == 'pending')
                                                                <span class="badge badge-warning">Pending</span>
                                                            @elseif ($refund->status == 'approved')
                                                                <span class="badge badge-success">Approved</span>
                                                            @elseif ($refund->status == 'rejected')
                                                                <span class="badge badge-danger">Rejected</span>
                                                            @elseif ($refund->status == 'processed')
                                                                <span class="badge badge-info">Processed</span>
                                                            @else
                                                                <span class="badge badge-secondary">{{ $refund->status }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Filing ID</th>
                                                        <td>{{ $refund->filing_id ?? 'Not submitted' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Created</th>
                                                        <td>{{ $refund->created_at->format('M d, Y H:i:s') }}</td>
                                                    </tr>
                                                    @if ($refund->updated_at != $refund->created_at)
                                                        <tr>
                                                            <th>Last Updated</th>
                                                            <td>{{ $refund->updated_at->format('M d, Y H:i:s') }}</td>
                                                        </tr>
                                                    @endif
                                                </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <h5 class="card-title">Uploaded Documents</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered mb-0">
                                                        <tr>
                                                            <th>Driver's License</th>
                                                            <td>
                                                                @if ($refund->hasDriversLicense())
                                                                    <a href="{{ route('admin.irs-refunds.download', [$refund->id, 'drivers-license']) }}" class="btn btn-primary btn-sm">
                                                                        <i class="fa fa-download"></i> Download
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">Not uploaded</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Government ID</th>
                                                            <td>
                                                                @if ($refund->hasIdDocument())
                                                                    <a href="{{ route('admin.irs-refunds.download', [$refund->id, 'id-document']) }}" class="btn btn-primary btn-sm">
                                                                        <i class="fa fa-download"></i> Download
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">Not uploaded (optional)</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">Timeline</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="timeline">
                                                    <div class="timeline-item">
                                                        <div class="timeline-date">{{ $refund->created_at->format('M d, Y H:i:s') }}</div>
                                                        <div class="timeline-content">
                                                            <h6>Refund Request Submitted</h6>
                                                            <p>User submitted personal details and identity documents for review.</p>
                                                        </div>
                                                    </div>
                                                    @if ($refund->status != 'pending')
                                                        <div class="timeline-item">
                                                            <div class="timeline-date">{{ $refund->updated_at->format('M d, Y H:i:s') }}</div>
                                                            <div class="timeline-content">
                                                                <h6>Status Updated</h6>
                                                                <p>Request was {{ $refund->status }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
