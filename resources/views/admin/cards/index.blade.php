@extends('layouts.app')
@section('content')
    @include('admin.topmenu')
    @include('admin.sidebar')
    <div class="main-panel">
        <div class="content">
            <div class="page-inner">
                <div class="admin-page-head">
                    <div>
                        <h1 class="title1 mb-1">{{ $title }}</h1>
                        <p class="text-muted mb-0">All virtual cards issued to clients</p>
                    </div>
                    <a href="{{ route('admin.cards.pending') }}" class="btn btn-primary">
                        <i class="fa fa-clock mr-1"></i> Pending applications
                    </a>
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
                            <div class="card-body">
                                <div class="admin-desktop-table admin-table-fit">
                                    <table class="table table-hover admin-compact-table">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Card</th>
                                                <th>Balance</th>
                                                <th>Status</th>
                                                <th class="text-right"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($cards as $card)
                                                <tr>
                                                    <td>
                                                        <span class="admin-user-cell">
                                                            <img src="{{ profile_photo_url(optional($card->user)->profile_photo_path, optional($card->user)->name ?? 'User') }}" alt="">
                                                            <span class="admin-cell-stack">
                                                                <strong>{{ $card->user ? $card->user->name : 'N/A' }}</strong>
                                                                <small>{{ $card->user ? $card->user->email : 'N/A' }}</small>
                                                            </span>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="admin-cell-stack">
                                                            <span class="mask-card-number text-nowrap">**** {{ substr($card->card_number, -4) }}</span>
                                                            <small>{{ ucfirst(str_replace('_', ' ', $card->card_type)) }} &middot; {{ ucfirst($card->card_level) }}</small>
                                                        </span>
                                                    </td>
                                                    <td class="text-nowrap">{{ $card->currency }}{{ number_format($card->balance, 2) }}</td>
                                                    <td>
                                                        <span class="admin-cell-stack">
                                                        <span>
                                                        @if ($card->status == 'active')
                                                            <span class="badge badge-success">Active</span>
                                                        @elseif ($card->status == 'inactive')
                                                            <span class="badge badge-warning">Inactive</span>
                                                        @elseif ($card->status == 'pending')
                                                            <span class="badge badge-info">Pending</span>
                                                        @elseif ($card->status == 'blocked')
                                                            <span class="badge badge-danger">Blocked</span>
                                                        @elseif ($card->status == 'rejected')
                                                            <span class="badge badge-danger">Rejected</span>
                                                        @else
                                                            <span class="badge badge-secondary">{{ $card->status }}</span>
                                                        @endif
                                                        </span>
                                                        <small>{{ $card->created_at->format('M d, Y') }}</small>
                                                        </span>
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="dropdown">
                                                            <button class="btn btn-light btn-sm admin-kebab" type="button" id="dropdownMenuButton{{ $card->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Actions">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton{{ $card->id }}">
                                                                <a class="dropdown-item" href="{{ route('admin.cards.view', $card->id) }}">
                                                                    <i class="fa fa-eye"></i> View Details
                                                                </a>
                                                                
                                                                @if ($card->status == 'pending')
                                                                    <a class="dropdown-item text-success" href="{{ route('admin.cards.approve', $card->id) }}">
                                                                        <i class="fa fa-check-circle"></i> Approve
                                                                    </a>
                                                                    <a class="dropdown-item text-danger" href="{{ route('admin.cards.reject', $card->id) }}">
                                                                        <i class="fa fa-times-circle"></i> Reject
                                                                    </a>
                                                                @endif
                                                                
                                                                @if ($card->status == 'active')
                                                                    <a class="dropdown-item text-warning" href="{{ route('admin.cards.block', $card->id) }}">
                                                                        <i class="fa fa-lock"></i> Block Card
                                                                    </a>
                                                                @endif
                                                                
                                                                @if ($card->status == 'blocked' || $card->status == 'inactive')
                                                                    <a class="dropdown-item text-success" href="{{ route('admin.cards.unblock', $card->id) }}">
                                                                        <i class="fa fa-unlock"></i> Unblock Card
                                                                    </a>
                                                                @endif
                                                                
                                                                <div class="dropdown-divider"></div>
                                                                
                                                                <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#deleteModal{{ $card->id }}">
                                                                    <i class="fa fa-trash"></i> Delete Card
                                                                </a>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Delete Modal -->
                                                        <div class="modal fade" id="deleteModal{{ $card->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $card->id }}" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="deleteModalLabel{{ $card->id }}">Confirm Delete</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        Are you sure you want to delete this card? This action cannot be undone.
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                        <a href="{{ route('admin.cards.delete', $card->id) }}" class="btn btn-danger">Delete</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">No virtual cards found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="admin-mobile-list">
                                    @forelse ($cards as $vcard)
                                        @php
                                            $cardBadge = [
                                                'active' => 'badge-success',
                                                'inactive' => 'badge-warning',
                                                'pending' => 'badge-info',
                                                'blocked' => 'badge-danger',
                                                'rejected' => 'badge-danger',
                                            ][$vcard->status] ?? 'badge-secondary';
                                        @endphp
                                        <x-admin.mobile-list-card
                                            :title="$vcard->user ? $vcard->user->name : 'N/A'"
                                            :subtitle="$vcard->user ? $vcard->user->email : 'N/A'"
                                            :badge="ucfirst($vcard->status)"
                                            :badge-class="$cardBadge"
                                            :avatar="profile_photo_url(optional($vcard->user)->profile_photo_path, optional($vcard->user)->name ?? 'User')"
                                            :meta="[
                                                ['label' => 'Card', 'value' => '**** **** **** ' . substr($vcard->card_number, -4)],
                                                ['label' => 'Type', 'value' => ucfirst(str_replace('_', ' ', $vcard->card_type))],
                                                ['label' => 'Level', 'value' => ucfirst($vcard->card_level)],
                                                ['label' => 'Balance', 'value' => $vcard->currency . number_format($vcard->balance, 2)],
                                                ['label' => 'Created', 'value' => $vcard->created_at->format('M d, Y')],
                                            ]"
                                        >
                                            <a class="btn btn-primary btn-sm" href="{{ route('admin.cards.view', $vcard->id) }}">View</a>
                                            @if ($vcard->status == 'pending')
                                                <a class="btn btn-success btn-sm" href="{{ route('admin.cards.approve', $vcard->id) }}">Approve</a>
                                                <a class="btn btn-danger btn-sm" href="{{ route('admin.cards.reject', $vcard->id) }}">Reject</a>
                                            @endif
                                            @if ($vcard->status == 'active')
                                                <a class="btn btn-warning btn-sm" href="{{ route('admin.cards.block', $vcard->id) }}">Block</a>
                                            @endif
                                            @if ($vcard->status == 'blocked' || $vcard->status == 'inactive')
                                                <a class="btn btn-success btn-sm" href="{{ route('admin.cards.unblock', $vcard->id) }}">Unblock</a>
                                            @endif
                                        </x-admin.mobile-list-card>
                                    @empty
                                        <p class="text-center mb-0">No virtual cards found.</p>
                                    @endforelse
                                </div>
                                <div class="mt-3">
                                    {{ $cards->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 