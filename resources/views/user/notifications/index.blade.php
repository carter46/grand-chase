@extends('layouts.dash2')
@section('title', $title)

@section('content')
<!-- Breadcrumbs + Page Title -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <!-- Action Buttons -->
        <div class="flex space-x-3">
            <form action="{{ route('notifications.read.all') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i data-lucide="check-circle" class="h-4 w-4 mr-2"></i> Mark All as Read
                </button>
            </form>
            <form action="{{ route('notifications.destroy.all') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete all notifications?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i data-lucide="trash-2" class="h-4 w-4 mr-2"></i> Delete All
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Alerts -->
<x-danger-alert />
<x-success-alert />
<x-error-alert />

<!-- Notifications List -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="border-b border-gray-200 px-6 py-4">
        <h3 class="text-lg font-medium text-gray-900">All Notifications</h3>
    </div>

    @if(count($notifications) > 0)
        <div class="divide-y divide-gray-200">
            @foreach($notifications as $notification)
                <div class="p-6 hover:bg-gray-50 transition {{ $notification->is_read ? 'opacity-70' : '' }}">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-full 
                                @if($notification->type == 'success') bg-green-100 text-green-500 
                                @elseif($notification->type == 'warning') bg-yellow-100 text-yellow-500 
                                @elseif($notification->type == 'danger') bg-red-100 text-red-500 
                                @else bg-blue-100 text-blue-500 @endif"
                            >
                                <i data-lucide="{{ $notification->icon ?? 'bell' }}" class="h-6 w-6"></i>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    @if($notification->title)
                                        <h4 class="text-base font-medium text-gray-900">{{ $notification->title }}</h4>
                                    @endif
                                    <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                    <p class="text-xs text-gray-500 mt-2">{{ $notification->created_at->format('M d, Y') }} at {{ $notification->created_at->format('h:i A') }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    @if(!$notification->is_read)
                                        <a href="{{ route('notifications.read', $notification->id) }}" class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                            <i data-lucide="check" class="h-3 w-3 mr-1"></i> Read
                                        </a>
                                    @endif
                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this notification?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md shadow-sm text-xs font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <i data-lucide="trash" class="h-3 w-3 mr-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="py-12 flex flex-col items-center justify-center text-center px-6">
            <div class="bg-gray-50 rounded-full p-3 mb-4">
                <i data-lucide="inbox" class="h-8 w-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No Notifications</h3>
            <p class="text-gray-500 text-sm mt-2 max-w-md">
                You don't have any notifications yet. Notifications will appear here when there are updates related to your account.
            </p>
        </div>
    @endif
</div>
@endsection 