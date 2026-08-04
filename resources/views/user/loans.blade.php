@extends('layouts.dash2')
@section('title', $title)
@section('content')

<div x-data="{ 
    showFilterModal: false,
    showExportModal: false,
    status: '',
    orderBy: 'desc',
    perPage: '10',
    dateRange: ''
}">
    <!-- Alerts -->
    <x-danger-alert />
    <x-success-alert />
    <x-error-alert />

    <!-- Page Header with Breadcrumbs -->
    <div class="flex flex-col mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Loan History</h1>
            <div class="flex items-center text-sm text-gray-500">
                <a href="{{ route('dashboard') }}" class="hover:text-primary-600">Dashboard</a>
                <i data-lucide="chevron-right" class="h-4 w-4 mx-2"></i>
                <span class="font-medium text-gray-700">Loan History</span>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Card Header -->
        <div class="relative bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center mb-4 md:mb-0">
                    <div class="bg-white/20 backdrop-blur-sm p-2 rounded-full mr-3">
                        <i data-lucide="history" class="h-6 w-6 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Your Loan Applications</h2>
                        <p class="text-white/80 text-sm">Track and manage your loan requests</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button 
                        @click="showFilterModal = true" 
                        class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg transition-colors"
                    >
                        <i data-lucide="filter" class="h-4 w-4 mr-2"></i>
                        Filter
                    </button>
                    <button 
                        @click="showExportModal = true" 
                        class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white rounded-lg transition-colors"
                    >
                        <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                        Export
                    </button>
                </div>
            </div>
            
            <!-- Wave decoration at the bottom -->
            <div class="absolute left-0 right-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none" class="h-6 w-full text-white fill-current">
                    <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25"></path>
                    <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5"></path>
                    <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"></path>
                </svg>
            </div>
        </div>
            
        <!-- Card Content -->
        <div class="p-6">
            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
                    </div>
                    <input 
                        type="search" 
                        class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                        placeholder="Search by loan purpose or amount..." 
                        wire:model="search"
                    />
                </div>
            </div>
            
            <!-- Loan Applications Table -->
            <div class="overflow-x-auto" wire:loading.class.delay="opacity-50" wire:target="search, status, orderBy, perPage, date, loadMore">
                <table class="min-w-full bg-white rounded-lg overflow-hidden">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Purpose
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Duration
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date Applied
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($loans as $loan)
                            <tr class="hover:bg-gray-50 transition-colors cursor-pointer">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                        <i data-lucide="landmark" class="h-5 w-5 text-gray-600"></i>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $settings->currency }}{{ number_format($loan->amount, 2) }} {{ $settings->s_currency }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 truncate max-w-[200px]" title="{{ $loan->purpose }}">
                                        {{ $loan->purpose }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($loan->duration == 1)
                                            {{ $loan->duration }} Month
                                        @elseif($loan->duration <= 12)
                                            {{ $loan->duration }} Months
                                        @else
                                            {{ floor($loan->duration / 12) }} {{ floor($loan->duration / 12) > 1 ? 'Years' : 'Year' }} 
                                            @if($loan->duration % 12 > 0)
                                                {{ $loan->duration % 12 }} {{ ($loan->duration % 12) > 1 ? 'Months' : 'Month' }}
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($loan->active == 'Processed')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $loan->active }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ $loan->active }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($loan->created_at)->toDayDateTimeString() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                            <i data-lucide="file-question" class="h-8 w-8 text-gray-400"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium text-lg">No loan applications found</p>
                                        <p class="text-gray-400 text-sm mt-1">Try adjusting your search or filter criteria</p>
                                        <a href="{{ route('loan') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                            <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                                            Apply for a Loan
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination (if applicable) -->
            <div class="mt-5">
                <!-- Add pagination here if needed -->
            </div>
        </div>
    </div>
    
    <!-- Filter Modal -->
    <div 
        x-show="showFilterModal" 
        x-cloak
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
            @click="showFilterModal = false"
        ></div>
        
        <div 
            class="relative bg-white rounded-lg w-full max-w-md mx-4 shadow-xl transform transition-all"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <div class="p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Filter Loans</h3>
                    <button @click="showFilterModal = false" class="text-gray-400 hover:text-gray-500">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
                
                <div class="space-y-5">
                    <div>
                        <label for="dateRange" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                        <input 
                            type="text" 
                            id="dateRange" 
                            class="block w-full px-3 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Select date range"
                            x-model="dateRange"
                            wire:model="date"
                        />
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <div class="relative">
                            <select 
                                id="status" 
                                x-model="status"
                                wire:model="status"
                                class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            >
                                <option value="">All statuses</option>
                                <option value="PROCESSED">Processed</option>
                                <option value="PENDING">Pending</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <i data-lucide="chevron-down" class="h-4 w-4 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="orderBy" class="block text-sm font-medium text-gray-700 mb-1">Sort by</label>
                        <div class="relative">
                            <select 
                                id="orderBy" 
                                x-model="orderBy"
                                wire:model="orderBy"
                                class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            >
                                <option value="asc">Oldest first</option>
                                <option value="desc">Newest first</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <i data-lucide="chevron-down" class="h-4 w-4 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="perPage" class="block text-sm font-medium text-gray-700 mb-1">Items per page</label>
                        <div class="relative">
                            <select 
                                id="perPage" 
                                x-model="perPage"
                                wire:model="perPage"
                                class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            >
                                <option value="10">10 items</option>
                                <option value="25">25 items</option>
                                <option value="50">50 items</option>
                                <option value="100">100 items</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <i data-lucide="chevron-down" class="h-4 w-4 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button 
                        @click="showFilterModal = false" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        Cancel
                    </button>
                    <button 
                        wire:click="$refresh"
                        @click="showFilterModal = false" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Export Modal -->
    <div 
        x-show="showExportModal" 
        x-cloak
        class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
            @click="showExportModal = false"
        ></div>
        
        <div 
            class="relative bg-white rounded-lg w-full max-w-md mx-4 shadow-xl transform transition-all"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <div class="p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Export Loans</h3>
                    <button @click="showExportModal = false" class="text-gray-400 hover:text-gray-500">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
                
                <form wire:submit.prevent="save(Object.fromEntries(new FormData($event.target)))">
                    @csrf
                    
                    <div class="space-y-5">
                        <div>
                            <label for="exportType" class="block text-sm font-medium text-gray-700 mb-1">File Format</label>
                            <div class="relative">
                                <select 
                                    id="exportType" 
                                    name="exportType"
                                    required
                                    class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                >
                                    <option value="">Select file type</option>
                                    <option value="csv">CSV</option>
                                    <option value="excel">Excel</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i data-lucide="chevron-down" class="h-4 w-4 text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="exportAs" class="block text-sm font-medium text-gray-700 mb-1">Export as</label>
                            <div class="relative">
                                <select 
                                    id="exportAs" 
                                    name="exportAs"
                                    required
                                    class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-lg appearance-none focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                >
                                    <option value="">How do you want to receive this file?</option>
                                    <option value="download">Download file</option>
                                    <option value="email">Send file to email</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <i data-lucide="chevron-down" class="h-4 w-4 text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button 
                            type="submit"
                            class="w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 flex items-center justify-center"
                        >
                            <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                            <span wire:loading.remove wire:target="save">Export</span>
                            <span wire:loading wire:target="save">Exporting file...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();
    });
</script>
@endpush

@endsection
