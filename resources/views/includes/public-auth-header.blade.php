@php
    $contactPhone = $settings->whatsapp ?? $settings->phone ?? null;
    $contactEmail = $settings->contact_email ?? null;
@endphp
<header class="fixed top-0 w-full z-50 bg-surface-container-lowest border-b border-outline-variant/30 shadow-sm">
    <div class="max-w-[1200px] mx-auto w-full px-4 md:px-gutter h-16 flex justify-between items-center">
        @include('includes.public-brand')
        <nav class="flex items-center gap-4 md:gap-6">
            <a href="{{ url('/') }}" class="font-label-md text-on-surface-variant hover:text-primary transition-colors hidden sm:inline">Home</a>
            @if (!request()->routeIs('login'))
                <a href="{{ url('login') }}" class="font-label-bold text-sm uppercase tracking-widest text-on-surface hover:text-primary transition-colors">Login</a>
            @endif
            @if (!request()->routeIs('register'))
                <a href="{{ url('register') }}" class="bg-primary hover:bg-primary-container text-on-primary px-5 py-2.5 font-label-bold text-sm tracking-widest transition-all">Register</a>
            @endif
        </nav>
    </div>
</header>
