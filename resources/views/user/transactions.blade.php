@extends('layouts.dash2')
@section('title', $title)
@section('content')

<!-- Debug Info Section -->
<div class="hidden bg-blue-50 border border-blue-200 rounded-md p-4 mb-6" x-data="{showDebug: false}" x-show="showDebug">
    <div class="flex justify-between items-center mb-2">
        <h3 class="text-sm font-semibold text-blue-800">Debug Information</h3>
        <button @click="showDebug = false" class="text-blue-500 hover:text-blue-700">
            <i data-lucide="x" class="h-4 w-4"></i>
        </button>
    </div>
    <div class="text-xs text-blue-800 space-y-1">
        <p>Route URL: {{ route('export.transactions') }}</p>
        <p>CSRF Token: <span id="csrf-token-debug">{{ csrf_token() }}</span></p>
        <p>Meta tag present: <span id="meta-tag-debug">Unknown</span></p>
        <div>
            <p class="font-semibold">Test connection:</p>
            <button 
                id="test-connection-btn"
                class="px-2 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600 mt-1">
                Test Export Endpoint
            </button>
            <p id="test-connection-result" class="mt-1"></p>
        </div>
    </div>
</div>

<div x-data="transactionApp()">
    <!-- Alerts -->
    <x-danger-alert />
    <x-success-alert />

    <!-- Page Header with Breadcrumbs -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Transactions</h1>
            <div class="flex items-center text-sm text-gray-500">
                <a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
                <span class="font-medium text-gray-700">Transactions</span>
            </div>
        </div>
        <div class="flex mt-4 md:mt-0 space-x-3">
            <button 
                @click="showFilterModal = true" 
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-colors">
                <i data-lucide="filter" class="h-4 w-4 mr-2"></i> Filter
            </button>
            <button 
                @click="showExportModal = true" 
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none transition-colors">
                <i data-lucide="download" class="h-4 w-4 mr-2"></i> Export
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-6">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
            </div>
            <input 
                type="search" 
                x-model="search" 
                @input="console.log('Searching for:', search)" 
                class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all" 
                placeholder="Search by transaction reference..." 
            />
        </div>
    </div>

    <!-- Transactions Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scope</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($withdrawals as $withdrawal)
                    <tr class="hover:bg-gray-50 transition-colors" id="kt_trx_110853544343568433_button">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($withdrawal->type != 'Credit')
                            <div class="h-10 w-10 bg-red-100 rounded-full flex items-center justify-center">
                                <i data-lucide="minus" class="h-5 w-5 text-red-600"></i>
                            </div>
                            @else
                            <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i data-lucide="plus" class="h-5 w-5 text-green-600"></i>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $settings->currency }}{{ $withdrawal->amount }} {{ $settings->s_currency }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($withdrawal->type == 'Credit')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Credit
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Debit
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($withdrawal->status == 'Pending')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                            @elseif($withdrawal->status == 'On-hold')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                On-hold
                            </span>
                            @elseif($withdrawal->status == 'Rejected')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Rejected
                            </span>
                            @elseif($withdrawal->status == 'Processed')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Processed
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                {{ $withdrawal->status }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{$withdrawal->txn_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $withdrawal->Description }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $withdrawal->payment_mode }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($withdrawal->created_at)->toDayDateTimeString() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="{{ route('previewtransfer', ['id' => $withdrawal->id]) }}" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-primary-700 hover:text-primary-800 hover:bg-primary-50 transition-colors">
                                <i data-lucide="file-text" class="h-3.5 w-3.5 mr-1"></i>
                                Receipt
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    
                    @foreach($deposits as $withdrawal)
                    <tr class="hover:bg-gray-50 transition-colors" id="{{ $withdrawal->id }}{{ $withdrawal->txn_id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($withdrawal->type == 'Credit')
                            <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i data-lucide="plus" class="h-5 w-5 text-green-600"></i>
                            </div>
                            @else
                            <div class="h-10 w-10 bg-red-100 rounded-full flex items-center justify-center">
                                <i data-lucide="minus" class="h-5 w-5 text-red-600"></i>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $settings->currency }}{{ number_format($withdrawal->amount, 2, '.', ',') }} {{ $settings->s_currency }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Credit
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($withdrawal->status == 'Pending')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Completed
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $withdrawal->txn_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $withdrawal->Description }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $withdrawal->payment_mode }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($withdrawal->created_at)->toDayDateTimeString() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="{{ route('previewtransfer', ['id' => $withdrawal->id]) }}" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-primary-700 hover:text-primary-800 hover:bg-primary-50 transition-colors">
                                <i data-lucide="file-text" class="h-3.5 w-3.5 mr-1"></i>
                                Receipt
                            </a>
                        </td>
                    </tr>
                    @endforeach

                    @if(count($withdrawals) == 0 && count($deposits) == 0)
                    <tr>
                        <td colspan="9" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="inbox" class="h-16 w-16 text-gray-300 mb-4"></i>
                                <p class="text-lg font-medium text-gray-600">No transactions found</p>
                                <p class="text-sm text-gray-500 mt-1 mb-4">Try adjusting your search or filter parameters</p>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Filter Modal -->
    <div 
        x-show="showFilterModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="filter-title" 
        role="dialog" 
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="showFilterModal" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" 
                @click="showFilterModal = false" 
                aria-hidden="true">
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div 
                x-show="showFilterModal" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-full max-w-md mx-auto sm:p-6">
                
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button
                        @click="showFilterModal = false"
                        type="button"
                        class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <i data-lucide="x" class="h-6 w-6"></i>
                    </button>
                </div>
                
                <div class="text-center mb-5">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-primary-100 mb-4">
                        <i data-lucide="filter" class="h-8 w-8 text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900" id="filter-title">Filter Transactions</h3>
                    <p class="mt-1 text-sm text-gray-500">Customize your transaction view</p>
                </div>
                
                <div class="mt-5 space-y-4">
                    <div>
                        <label for="date-range" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="date-from" class="block text-xs text-gray-500 mb-1">From</label>
                                <input 
                                    type="date" 
                                    id="date-from" 
                                    x-model="dateFrom"
                                    class="block w-full border border-gray-200 rounded-lg p-2.5 focus:ring-primary-500 focus:border-primary-500"
                                />
                            </div>
                            <div>
                                <label for="date-to" class="block text-xs text-gray-500 mb-1">To</label>
                                <input 
                                    type="date" 
                                    id="date-to" 
                                    x-model="dateTo"
                                    class="block w-full border border-gray-200 rounded-lg p-2.5 focus:ring-primary-500 focus:border-primary-500"
                                />
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select 
                            id="status"
                            x-model="status"
                            class="block w-full border border-gray-200 rounded-lg p-2.5 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Select status</option>
                            <option value="COMPLETED">Completed</option>
                            <option value="PENDING">Pending</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="order-by" class="block text-sm font-medium text-gray-700 mb-1">Sort by</label>
                        <select 
                            id="order-by"
                            x-model="orderBy"
                            class="block w-full border border-gray-200 rounded-lg p-2.5 focus:ring-primary-500 focus:border-primary-500">
                            <option value="asc">Oldest first (ASC)</option>
                            <option value="desc">Newest first (DESC)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="per-page" class="block text-sm font-medium text-gray-700 mb-1">Per page</label>
                        <select 
                            id="per-page"
                            x-model="perPage"
                            class="block w-full border border-gray-200 rounded-lg p-2.5 focus:ring-primary-500 focus:border-primary-500">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button 
                        @click="showFilterModal = false"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                        Cancel
                    </button>
                    <button 
                        @click="applyFilters()"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none">
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div 
        x-show="showExportModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="export-title" 
        role="dialog" 
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="showExportModal" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" 
                @click="showExportModal = false" 
                aria-hidden="true">
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div 
                x-show="showExportModal" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-full max-w-md mx-auto sm:p-6">
                
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button
                        @click="showExportModal = false"
                        type="button"
                        class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <i data-lucide="x" class="h-6 w-6"></i>
                    </button>
                </div>
                
                <div class="text-center mb-5">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-primary-100 mb-4">
                        <i data-lucide="download" class="h-8 w-8 text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900" id="export-title">Export Transactions</h3>
                    <p class="mt-1 text-sm text-gray-500">Download or receive your transaction data</p>
                </div>
                
                <div class="mt-5 space-y-4">
                    <div>
                        <label for="export-type" class="block text-sm font-medium text-gray-700 mb-1">File Format</label>
                        <select 
                            id="export-type"
                            x-model="exportType"
                            class="block w-full border border-gray-200 rounded-lg p-2.5 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Select file type</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="export-as" class="block text-sm font-medium text-gray-700 mb-1">Export as</label>
                        <select 
                            id="export-as"
                            x-model="exportAs"
                            class="block w-full border border-gray-200 rounded-lg p-2.5 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">How do you want to receive this file?</option>
                            <option value="view">Preview statement</option>
                            <option value="download">Download file</option>
                            <option value="email">Send file to email</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="statement-type" class="block text-sm font-medium text-gray-700 mb-1">Statement Style</label>
                        <div class="grid grid-cols-2 gap-3 mt-2">
                            <div 
                                @click="statementStyle = 'modern'" 
                                :class="{'ring-2 ring-primary-500 bg-primary-50': statementStyle === 'modern'}"
                                class="border rounded-lg p-3 cursor-pointer hover:bg-gray-50 transition-colors">
                                <div class="text-xs font-medium mb-1">Modern</div>
                                <div class="h-12 bg-gray-200 rounded flex items-center justify-center">
                                    <i data-lucide="layout-dashboard" class="h-5 w-5 text-gray-500"></i>
                                </div>
                            </div>
                            <div 
                                @click="statementStyle = 'classic'" 
                                :class="{'ring-2 ring-primary-500 bg-primary-50': statementStyle === 'classic'}"
                                class="border rounded-lg p-3 cursor-pointer hover:bg-gray-50 transition-colors">
                                <div class="text-xs font-medium mb-1">Classic</div>
                                <div class="h-12 bg-gray-200 rounded flex items-center justify-center">
                                    <i data-lucide="file-text" class="h-5 w-5 text-gray-500"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button 
                        @click="exportData()"
                        type="button"
                        id="export-button"
                        class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none">
                        <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                        <span>Export Transactions</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Alpine.js component
    document.addEventListener('alpine:init', function() {
        Alpine.data('transactionApp', function() {
            return {
                showFilterModal: false,
                showExportModal: false,
                showDebug: false,
                dateFrom: '',
                dateTo: '',
                status: '',
                orderBy: 'desc',
                perPage: '10',
                exportType: '',
                exportAs: '',
                search: '',
                statementStyle: 'modern',
                
                applyFilters() {
                    // Here you would handle the filtering logic with Alpine.js
                    this.showFilterModal = false;
                },
                
                exportData() {
                    // Check if both exportType and exportAs are selected
                    if(!this.exportType || !this.exportAs) {
                        this.errorMessage = 'Please select both export type and delivery method';
                        this.showError = true;
                        this.loading = false;
                        return;
                    }
                    
                    console.log('Export options:', this.exportType, this.exportAs, this.statementStyle);
                    
                    // Handle view option - open in new tab
                    if (this.exportAs === 'view') {
                        const url = new URL("{{ route('user.transactions.export') }}", window.location.origin);
                        
                        // Add parameters
                        url.searchParams.append('exportType', this.exportType);
                        url.searchParams.append('exportAs', this.exportAs);
                        url.searchParams.append('statementStyle', this.statementStyle);
                        
                        // Add filters if they exist
                        if (this.dateFrom) url.searchParams.append('startDate', this.dateFrom);
                        if (this.dateTo) url.searchParams.append('endDate', this.dateTo);
                        if (this.status) url.searchParams.append('status', this.status);
                        if (this.orderBy) url.searchParams.append('orderBy', this.orderBy);
                        
                        // Open in new tab with specific dimensions
                        const windowFeatures = 'width=800,height=1000,resizable=yes,scrollbars=yes,status=yes';
                        window.open(url.toString(), '_blank', windowFeatures);
                        
                        // Reset state
                        this.loading = false;
                        this.showModal = false;
                        return;
                    }
                    
                    // Disable the export button while processing
                    const exportButton = document.querySelector('#export-button');
                    exportButton.disabled = true;
                    exportButton.innerHTML = '<i data-lucide="loader" class="h-4 w-4 mr-2 animate-spin"></i><span>Processing...</span>';
                    
                    try {
                        // Get the CSRF token
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        // Prepare form data
                        const formData = new FormData();
                        formData.append('exportType', this.exportType);
                        formData.append('exportAs', this.exportAs);
                        
                        // Add filter values if they exist
                        if (this.dateFrom) formData.append('dateFrom', this.dateFrom);
                        if (this.dateTo) formData.append('dateTo', this.dateTo);
                        if (this.status) formData.append('status', this.status);
                        formData.append('orderBy', this.orderBy);
                        formData.append('statementStyle', this.statementStyle);
                        
                        // Handle based on export method
                        if (this.exportAs === 'download') {
                            // For direct downloads, create a form and submit it directly
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route("export.transactions") }}';
                            form.style.display = 'none';
                            
                            // Add CSRF token
                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken;
                            form.appendChild(csrfInput);
                            
                            // Add form fields
                            for (const pair of formData.entries()) {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = pair[0];
                                input.value = pair[1];
                                form.appendChild(input);
                            }
                            
                            // Add form to body and submit
                            document.body.appendChild(form);
                            
                            // Submit the form and handle potential errors
                            try {
                                form.submit();
                                console.log('Form submitted successfully');
                            } catch (err) {
                                console.error('Form submission error:', err);
                                this.showExportError();
                            }
                            
                            // Clean up
                            setTimeout(() => {
                                document.body.removeChild(form);
                                this.showExportModal = false;
                                exportButton.disabled = false;
                                exportButton.innerHTML = '<i data-lucide="download" class="h-4 w-4 mr-2"></i><span>Export Transactions</span>';
                                lucide.createIcons(); // Reinitialize icons
                            }, 1000);
                        } else {
                            // For email exports, use fetch API
                            console.log('Sending email export request to:', '{{ route("export.transactions") }}');
                            
                            fetch('{{ route("export.transactions") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                },
                                body: formData
                            })
                            .then(response => {
                                console.log('Response status:', response.status);
                                if (!response.ok) {
                                    throw new Error('Network response was not ok: ' + response.status);
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Export response:', data);
                                if (data.success) {
                                    // Show success message
                                    const successAlert = document.querySelector('.success-alert');
                                    if (successAlert) {
                                        const alertMessage = successAlert.querySelector('.alert-message');
                                        if (alertMessage) {
                                            alertMessage.textContent = 'Your transaction export has been sent to your email.';
                                            successAlert.classList.remove('hidden');
                                            setTimeout(() => {
                                                successAlert.classList.add('hidden');
                                            }, 5000);
                                        }
                                    } else {
                                        alert('Your transaction export has been sent to your email.');
                                    }
                                    this.showExportModal = false;
                                } else {
                                    this.showExportError(data.message || 'An error occurred while exporting your transactions.');
                                }
                            })
                            .catch(error => {
                                console.error('Export error:', error);
                                this.showExportError();
                            })
                            .finally(() => {
                                // Re-enable the export button
                                exportButton.disabled = false;
                                exportButton.innerHTML = '<i data-lucide="download" class="h-4 w-4 mr-2"></i><span>Export Transactions</span>';
                                lucide.createIcons(); // Reinitialize icons
                            });
                        }
                    } catch (error) {
                        console.error('Export functionality error:', error);
                        this.showExportError();
                        
                        // Re-enable the export button
                        exportButton.disabled = false;
                        exportButton.innerHTML = '<i data-lucide="download" class="h-4 w-4 mr-2"></i><span>Export Transactions</span>';
                        lucide.createIcons(); // Reinitialize icons
                    }
                },
                
                showExportError(message = 'An error occurred while exporting your transactions.') {
                    // Show error message
                    const dangerAlert = document.querySelector('.danger-alert');
                    if (dangerAlert) {
                        const alertMessage = dangerAlert.querySelector('.alert-message');
                        if (alertMessage) {
                            alertMessage.textContent = message;
                            dangerAlert.classList.remove('hidden');
                            setTimeout(() => {
                                dangerAlert.classList.add('hidden');
                            }, 5000);
                        }
                    } else {
                        alert(message);
                    }
                }
            };
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Basic date input handling with native inputs
        const dateFromInput = document.getElementById('date-from');
        const dateToInput = document.getElementById('date-to');
        
        if (dateFromInput && dateToInput) {
            // Set default dates (last 30 days)
            const today = new Date();
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(today.getDate() - 30);
            
            dateToInput.valueAsDate = today;
            dateFromInput.valueAsDate = thirtyDaysAgo;
        }
        
        // Debug tools
        // Add a keyboard shortcut for debug panel (Ctrl+Shift+D)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'D') {
                const debugElement = document.querySelector('.bg-blue-50');
                if (debugElement) {
                    debugElement.classList.toggle('hidden');
                }
            }
        });
        
        // Check for CSRF meta tag
        const metaTagDebug = document.getElementById('meta-tag-debug');
        if (metaTagDebug) {
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            metaTagDebug.textContent = csrfMeta ? 'Yes' : 'No';
        }
        
        // Test connection button
        const testConnectionBtn = document.getElementById('test-connection-btn');
        if (testConnectionBtn) {
            testConnectionBtn.addEventListener('click', function() {
                const resultElem = document.getElementById('test-connection-result');
                resultElem.textContent = 'Testing connection...';
                resultElem.className = 'mt-1 text-blue-600';
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    resultElem.textContent = 'Error: CSRF token not found!';
                    resultElem.className = 'mt-1 text-red-600';
                    return;
                }
                
                // Make a simple ping request to the export endpoint
                fetch('{{ route("export.transactions") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        test: true
                    })
                })
                .then(response => {
                    if (response.ok) {
                        resultElem.textContent = 'Success! Endpoint is accessible. Status: ' + response.status;
                        resultElem.className = 'mt-1 text-green-600';
                    } else {
                        resultElem.textContent = 'Error: Status ' + response.status;
                        resultElem.className = 'mt-1 text-red-600';
                    }
                    return response.text();
                })
                .then(text => {
                    console.log('Response text:', text);
                })
                .catch(error => {
                    resultElem.textContent = 'Error: ' + error.message;
                    resultElem.className = 'mt-1 text-red-600';
                });
            });
        }
    });
</script>

@endsection