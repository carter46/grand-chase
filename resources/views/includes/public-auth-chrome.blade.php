{{-- Clean auth chrome: brand XOR, Back to Home, language selector --}}
<div class="w-full border-b border-outline-variant/30 bg-surface-container-lowest">
    <div class="max-w-[1200px] mx-auto px-4 md:px-gutter h-14 flex items-center justify-between gap-4">
        @include('includes.public-brand')
        <div class="flex items-center gap-3 sm:gap-4">
            <div id="google_translate_element" class="overflow-visible min-w-[100px]"></div>
            <a href="{{ url('/') }}" class="font-label-bold text-xs sm:text-sm uppercase tracking-wider text-on-surface hover:text-primary whitespace-nowrap">
                Back to Home
            </a>
        </div>
    </div>
</div>
