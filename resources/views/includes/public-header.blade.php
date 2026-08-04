@php
    $activeNav = $activeNav ?? null;
    $navItems = [
        'home' => ['label' => 'Home', 'url' => url('/')],
        'personal' => ['label' => 'Personal Banking', 'url' => url('personal')],
        'business' => ['label' => 'Business Banking', 'url' => url('business')],
        'loans' => ['label' => 'Loans', 'url' => url('loans')],
        'investments' => ['label' => 'Investments', 'url' => url('investments')],
        'cards' => ['label' => 'Cards', 'url' => url('cards')],
        'contact' => ['label' => 'Contact', 'url' => url('contact')],
    ];
    $navClass = function ($key) use ($activeNav) {
        if ($activeNav === $key) {
            return 'border-b-4 border-primary text-on-primary-container md:border-b-4 md:border-primary md:text-on-primary-container';
        }
        return 'text-on-primary/80 hover:text-primary md:text-on-primary md:hover:text-primary';
    };
@endphp

<header class="fixed top-0 w-full z-50 overflow-visible" x-data="{ drawerOpen: false }" @keydown.escape.window="drawerOpen = false">
    {{-- Top bar --}}
    <div class="bg-surface-container-lowest h-14 border-b border-outline-variant/30 flex items-center shadow-sm overflow-visible">
        <div class="max-w-[1200px] mx-auto w-full px-4 md:px-gutter flex justify-between items-center overflow-visible">
            @include('includes.public-brand')
            <div class="flex items-center gap-stack-md overflow-visible">
                <div id="google_translate_slot_desktop" class="hidden md:block min-w-[140px] max-w-[220px] overflow-visible"></div>
                <button type="button" class="md:hidden p-2 text-on-surface" @click="drawerOpen = true" aria-label="Open menu">
                    <span class="material-symbols-outlined">menu</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Desktop nav --}}
    <div class="hidden md:block bg-inverse-surface h-[75px] shadow-lg">
        <div class="max-w-[1200px] mx-auto w-full px-gutter h-full flex justify-between items-center">
            <nav class="flex h-full items-center gap-4 lg:gap-stack-lg overflow-x-auto">
                @foreach ($navItems as $key => $item)
                    <a href="{{ $item['url'] }}"
                       class="font-label-bold uppercase tracking-widest transition-colors h-full flex items-center pt-1 text-xs lg:text-sm whitespace-nowrap {{ $navClass($key) }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
            <a href="{{ url('login') }}" class="shrink-0 bg-primary hover:bg-primary-container text-on-primary px-6 lg:px-8 py-3 font-label-bold text-sm tracking-widest transition-all rounded-[0px]">
                ONLINE BANKING
            </a>
        </div>
    </div>

    {{-- Mobile nav bar --}}
    <div class="md:hidden flex h-12 bg-bank-charcoal items-stretch">
        <div class="flex-1"></div>
        <a href="{{ url('login') }}" class="flex items-center justify-center px-6 bg-bank-orange text-white font-label-bold uppercase h-full text-sm tracking-wider">
            Online Banking
        </a>
    </div>

    {{-- Mobile drawer --}}
    <div x-show="drawerOpen" x-cloak class="md:hidden fixed inset-0 z-[60]" aria-modal="true" role="dialog">
        <div class="absolute inset-0 bg-black/50" @click="drawerOpen = false"></div>
        <div class="absolute top-0 right-0 h-full w-[min(320px,85vw)] bg-surface-container-lowest shadow-2xl flex flex-col"
             x-show="drawerOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            <div class="flex items-center justify-between h-14 px-4 border-b border-outline-variant/30">
                <span class="font-label-bold uppercase text-on-surface">Menu</span>
                <button type="button" class="p-2 text-on-surface" @click="drawerOpen = false" aria-label="Close menu">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="px-4 py-3 border-b border-outline-variant/30">
                <p class="font-label-md text-on-surface-variant uppercase tracking-wider text-xs mb-2">Language</p>
                <div id="google_translate_slot_mobile" class="w-full overflow-visible"></div>
            </div>
            <nav class="flex flex-col p-4 gap-1 flex-1 overflow-y-auto">
                @foreach ($navItems as $key => $item)
                    <a href="{{ $item['url'] }}"
                       @click="drawerOpen = false"
                       class="font-label-bold uppercase tracking-wider py-3 px-2 border-b border-outline-variant/20 {{ $activeNav === $key ? 'text-primary' : 'text-on-surface hover:text-primary' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
                <a href="{{ url('login') }}" @click="drawerOpen = false" class="mt-4 bg-primary text-on-primary text-center py-3 font-label-bold uppercase tracking-widest">
                    Online Banking
                </a>
                <a href="{{ url('register') }}" @click="drawerOpen = false" class="mt-2 border border-on-surface text-on-surface text-center py-3 font-label-bold uppercase tracking-widest">
                    Sign Up
                </a>
            </nav>
        </div>
    </div>
</header>

{{-- Single translate mount; JS relocates between desktop slot and mobile drawer --}}
<div id="google_translate_element" class="sr-only" aria-hidden="true"></div>
