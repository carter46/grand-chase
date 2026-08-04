@extends('layouts.dash2')
@section('title', 'Card Transactions')

@section('content')
<!-- Breadcrumbs + Page Title -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-primary-600">Dashboard</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2 text-gray-400"></i>
                <a href="{{ route('cards') }}" class="text-sm text-gray-500 hover:text-primary-600">Cards</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2 text-gray-400"></i>
                <span class="text-sm font-medium text-gray-700">Transactions</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Card Transactions</h1>
            <p class="text-gray-500 mt-1">
                View all transactions for your {{ ucfirst(str_replace('_', ' ', $card->card_type)) }} {{ ucfirst($card->card_level) }} card ending in {{ $card->last_four }}
            </p>
        </div>
    </div>
</div>

<!-- Transaction Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i data-lucide="trending-up" class="h-6 w-6 text-green-600"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 truncate">Total Spending</p>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $card->currency }} {{ number_format($totalSpending, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i data-lucide="credit-card" class="h-6 w-6 text-blue-600"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 truncate">Total Transactions</p>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $transactions->total() }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                    <i data-lucide="calendar" class="h-6 w-6 text-indigo-600"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500 truncate">Last Activity</p>
                    <h3 class="text-lg font-semibold text-gray-900">
                        @if($lastActivity)
                            {{ \Carbon\Carbon::parse($lastActivity->transaction_date)->format('M d, Y') }}
                        @else
                            No activity yet
                        @endif
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">Filters</h2>
    </div>
    <div class="p-6">
        <form action="{{ route('cards.transactions', $card) }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
            <div class="w-full md:w-1/4">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                <select id="type" name="type" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    <option value="">All Types</option>
                    <option value="purchase" {{ request()->get('type') == 'purchase' ? 'selected' : '' }}>Purchases</option>
                    <option value="refund" {{ request()->get('type') == 'refund' ? 'selected' : '' }}>Refunds</option>
                    <option value="funding" {{ request()->get('type') == 'funding' ? 'selected' : '' }}>Funding</option>
                </select>
            </div>
            
            <div class="w-full md:w-1/4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ request()->get('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request()->get('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="declined" {{ request()->get('status') == 'declined' ? 'selected' : '' }}>Declined</option>
                </select>
            </div>
            
            <div class="w-full md:w-1/4">
                <label for="date_start" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" id="date_start" name="date_start" value="{{ request()->get('date_start') }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
            </div>
            
            <div class="w-full md:w-1/4">
                <label for="date_end" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" id="date_end" name="date_end" value="{{ request()->get('date_end') }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
            </div>
            
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i data-lucide="filter" class="h-4 w-4 mr-2"></i> Filter
                </button>
                
                <a href="{{ route('cards.transactions', $card) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i data-lucide="x" class="h-4 w-4 mr-2"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Transactions Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="border-b border-gray-200 px-6 py-4">
        <h2 class="text-lg font-medium text-gray-900">Transaction History</h2>
    </div>
    
    @if(count($transactions) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merchant</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $transaction->description }}</div>
                                <div class="text-xs text-gray-500">Ref: {{ Str::limit($transaction->transaction_reference, 10) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $transaction->merchant_name ?: 'N/A' }}</div>
                                @if($transaction->merchant_category)
                                    <div class="text-xs text-gray-500">{{ $transaction->merchant_category }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-medium rounded-full 
                                    @if($transaction->transaction_type == 'purchase') bg-red-100 text-red-800 
                                    @elseif($transaction->transaction_type == 'refund') bg-green-100 text-green-800 
                                    @elseif($transaction->transaction_type == 'funding') bg-blue-100 text-blue-800 
                                    @else bg-gray-100 text-gray-800 
                                    @endif">
                                    {{ ucfirst($transaction->transaction_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-medium rounded-full 
                                    @if($transaction->status == 'completed') bg-green-100 text-green-800 
                                    @elseif($transaction->status == 'pending') bg-yellow-100 text-yellow-800 
                                    @elseif($transaction->status == 'declined') bg-red-100 text-red-800 
                                    @else bg-gray-100 text-gray-800 
                                    @endif">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                <span class="@if($transaction->transaction_type == 'purchase') text-red-600 
                                              @elseif(in_array($transaction->transaction_type, ['refund', 'funding'])) text-green-600 
                                              @else text-gray-900 @endif">
                                    @if($transaction->transaction_type == 'purchase')
                                        -{{ $card->currency }} {{ number_format(abs($transaction->amount), 2) }}
                                    @elseif(in_array($transaction->transaction_type, ['refund', 'funding']))
                                        +{{ $card->currency }} {{ number_format(abs($transaction->amount), 2) }}
                                    @else
                                        {{ $card->currency }} {{ number_format($transaction->amount, 2) }}
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
    @else
        <div class="py-12 flex flex-col items-center justify-center text-center px-6">
            <div class="bg-gray-50 rounded-full p-3 mb-4">
                <i data-lucide="credit-card" class="h-8 w-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No Transactions Found</h3>
            <p class="text-gray-500 text-sm mt-2 max-w-md">
                @if(request()->has('type') || request()->has('status') || request()->has('date_start') || request()->has('date_end'))
                    No transactions match your current filters. Try adjusting your search criteria.
                @else
                    This card has no transaction history yet. Transactions will appear here once you start using the card.
                @endif
            </p>
            @if(request()->has('type') || request()->has('status') || request()->has('date_start') || request()->has('date_end'))
                <div class="mt-4">
                    <a href="{{ route('cards.transactions', $card) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i data-lucide="filter-x" class="h-4 w-4 mr-2"></i> Clear All Filters
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>

<!-- Export Options -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="border-b border-gray-200 px-6 py-4">
        <h2 class="text-lg font-medium text-gray-900">Export Transactions</h2>
    </div>
    <div class="p-6">
        <p class="text-gray-600 mb-4">Download your transaction history in your preferred format.</p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('cards.transactions', ['card' => $card->id, 'export' => 'csv'] + request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i data-lucide="file-text" class="h-4 w-4 mr-2"></i> CSV
            </a>
            <a href="{{ route('cards.transactions', ['card' => $card->id, 'export' => 'pdf'] + request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i data-lucide="file" class="h-4 w-4 mr-2"></i> PDF
            </a>
            <a href="{{ route('cards.transactions', ['card' => $card->id, 'export' => 'excel'] + request()->query()) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i data-lucide="file-spreadsheet" class="h-4 w-4 mr-2"></i> Excel
            </a>
        </div>
    </div>
</div>
@endsection 