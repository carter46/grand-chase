@if(Session::has('message'))
<div class="w-full animate-fade-in-down" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)">
    <div class="max-w-md mx-auto mt-4 mb-6 overflow-hidden bg-white border border-red-100 rounded-xl shadow-md">
        <div class="flex items-stretch">
            <!-- Error Indicator Stripe -->
            <div class="flex-shrink-0 w-2 bg-red-500"></div>
            
            <div class="flex items-center flex-grow p-4">
                <!-- Error Icon -->
                <div class="flex-shrink-0 mr-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
                    </div>
                </div>
                
                <!-- Error Message -->
                <div class="flex-grow">
                    <div class="text-sm font-medium text-red-700">
                        {{ Session::get('message') }}
                    </div>
                </div>
                
                <!-- Close Button -->
                <div class="flex-shrink-0 ml-4">
                    <button @click="show = false" class="p-1 rounded-full text-red-400 hover:bg-red-50">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in-down {
        0% {
            opacity: 0;
            transform: translateY(-20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-down {
        animation: fade-in-down 0.5s ease-out;
    }
</style>
@endif