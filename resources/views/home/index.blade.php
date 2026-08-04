@php
    if ($settings->redirect_url != null || !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
    $activeNav = 'home';
@endphp
@extends('layouts.base')
@section('title', 'Home')

@section('content')
@php
    $heroSlides = [
        [
            'eyebrow' => 'Welcome to '.$settings->site_name,
            'title_html' => 'YOUR <span class="text-primary italic">BANK</span> YOUR WAY',
            'body' => 'A bank account that gives you more. Rewards checking from '.$settings->site_name.' offers the flexibility and convenience you deserve in a global marketplace.',
            'image' => asset('assets/images/hero-home-desktop.jpg'),
            'image_mobile' => asset('assets/images/hero-home-mobile.jpg'),
            'cta_primary' => ['label' => 'Login', 'url' => url('login')],
            'cta_secondary' => ['label' => 'Sign Up', 'url' => url('register')],
        ],
        [
            'eyebrow' => 'Personal Banking',
            'title_html' => 'Banking Built Around <span class="text-primary italic">Your Life</span>',
            'body' => 'Everyday checking, high-yield savings, and tools that keep your household finances clear—online and on mobile.',
            'image' => asset('assets/images/hero-personal.jpg'),
            'cta_primary' => ['label' => 'Open Account', 'url' => url('register')],
            'cta_secondary' => ['label' => 'Personal Banking', 'url' => url('personal')],
        ],
        [
            'eyebrow' => 'Lending',
            'title_html' => 'Flexible Loans for <span class="text-primary italic">What\'s Next</span>',
            'body' => 'Personal, auto, and business lending with transparent terms—so you can fund goals without the guesswork.',
            'image' => asset('assets/images/hero-loans.jpg'),
            'cta_primary' => ['label' => 'Apply Now', 'url' => url('register')],
            'cta_secondary' => ['label' => 'Explore Loans', 'url' => url('loans')],
        ],
        [
            'eyebrow' => 'Credit Cards',
            'title_html' => 'Cards That Work <span class="text-primary italic">Harder</span>',
            'body' => 'From everyday spending to travel rewards—see card options designed for how you spend and pay.',
            'image' => asset('assets/images/hero-investments.jpg'),
            'cta_primary' => ['label' => 'View Cards', 'url' => url('cards')],
            'cta_secondary' => ['label' => 'Get Started', 'url' => url('register')],
        ],
    ];
    // Clones for seamless endless loop: [last] + slides + [first]
    $heroTrack = array_merge(
        [array_merge(end($heroSlides), ['_clone' => true])],
        $heroSlides,
        [array_merge($heroSlides[0], ['_clone' => true])]
    );
@endphp
<div class="flex flex-col w-full">
    {{-- Hero slider --}}
    <section
        id="home-hero-slider"
        class="hero-slider relative h-[calc(100vh-104px)] md:h-[calc(100vh-131px)] min-h-[520px] overflow-hidden select-none bg-inverse-surface cursor-grab active:cursor-grabbing"
        x-data="homeHeroSlider({{ count($heroSlides) }})"
        @keydown.left.window="prev()"
        @keydown.right.window="next()"
    >
        <div
            class="flex h-full will-change-transform"
            :class="(dragging || jumping) ? '' : 'transition-transform duration-500 ease-out'"
            :style="'transform: translate3d(' + (-(track * 100) + dragPct) + '%,0,0)'"
            @pointerdown="onPointerDown($event)"
            @pointermove="onPointerMove($event)"
            @pointerup="onPointerUp($event)"
            @pointercancel="onPointerUp($event)"
            @transitionend="onTransitionEnd($event)"
        >
            @foreach ($heroTrack as $i => $slide)
            <article class="relative h-full w-full min-w-full flex-shrink-0 flex items-center justify-center overflow-hidden">
                <div class="absolute inset-0 z-0">
                    <img
                        src="{{ $slide['image'] }}"
                        alt="{{ $slide['eyebrow'] }}"
                        class="no-reveal w-full h-full object-cover pointer-events-none {{ !empty($slide['image_mobile']) ? 'hidden md:block' : '' }}"
                        draggable="false"
                    >
                    @if (!empty($slide['image_mobile']))
                    <img
                        src="{{ $slide['image_mobile'] }}"
                        alt="{{ $slide['eyebrow'] }}"
                        class="no-reveal w-full h-full object-cover pointer-events-none md:hidden"
                        draggable="false"
                    >
                    @endif
                    <div class="absolute inset-0 bg-black/55"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-black/20"></div>
                </div>
                <div class="relative z-10 max-w-[1200px] mx-auto px-4 md:px-gutter w-full text-center flex flex-col items-center py-12">
                    <span class="font-label-bold text-primary uppercase tracking-[0.2em] mb-stack-md">{{ $slide['eyebrow'] }}</span>
                    <h1 class="text-hero-large text-on-primary mb-stack-lg uppercase tracking-tighter max-w-4xl">
                        {!! $slide['title_html'] !!}
                    </h1>
                    <p class="font-body-md md:font-body-lg text-on-primary/90 max-w-[650px] mb-stack-lg leading-relaxed px-2">
                        {{ $slide['body'] }}
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 md:gap-stack-md w-auto px-4 sm:px-0">
                        <a href="{{ $slide['cta_primary']['url'] }}" class="inline-flex justify-center bg-primary hover:bg-primary-container text-on-primary px-8 sm:px-10 py-3.5 sm:py-4 font-label-bold text-sm tracking-widest transition-all shadow-xl hover:-translate-y-1 text-center min-w-[140px] max-w-[220px]">
                            {{ $slide['cta_primary']['label'] }}
                        </a>
                        <a href="{{ $slide['cta_secondary']['url'] }}" class="inline-flex justify-center bg-bank-charcoal/90 hover:bg-on-background text-on-primary px-8 sm:px-10 py-3.5 sm:py-4 font-label-bold text-sm tracking-widest transition-all shadow-xl hover:-translate-y-1 text-center min-w-[140px] max-w-[220px] border border-on-primary/20">
                            {{ $slide['cta_secondary']['label'] }}
                        </a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        {{-- Controls --}}
        <div class="absolute inset-x-0 bottom-6 md:bottom-8 z-20 flex items-center justify-center gap-4 pointer-events-none">
            <button type="button" @click="prev()" class="pointer-events-auto hidden sm:inline-flex h-11 w-11 items-center justify-center border border-on-primary/40 text-on-primary hover:bg-on-primary hover:text-inverse-surface transition-colors" aria-label="Previous slide">
                <span class="material-symbols-outlined">chevron_left</span>
            </button>
            <div class="pointer-events-auto flex items-center gap-2">
                @foreach ($heroSlides as $i => $slide)
                <button
                    type="button"
                    @click="go({{ $i }})"
                    class="h-2.5 rounded-full transition-all duration-300"
                    :class="index === {{ $i }} ? 'w-8 bg-primary' : 'w-2.5 bg-on-primary/40 hover:bg-on-primary/70'"
                    aria-label="Go to slide {{ $i + 1 }}"
                ></button>
                @endforeach
            </div>
            <button type="button" @click="next()" class="pointer-events-auto hidden sm:inline-flex h-11 w-11 items-center justify-center border border-on-primary/40 text-on-primary hover:bg-on-primary hover:text-inverse-surface transition-colors" aria-label="Next slide">
                <span class="material-symbols-outlined">chevron_right</span>
            </button>
        </div>

        <p class="absolute left-4 bottom-6 z-20 hidden lg:block font-caption uppercase tracking-widest text-on-primary/50 pointer-events-none">
            Swipe or drag
        </p>
    </section>

    {{-- Banking Solutions --}}
    <section class="py-12 md:py-24 bg-surface-container-lowest px-4 md:px-0">
        <div class="max-w-[1200px] mx-auto md:px-gutter">
            <div class="flex flex-col md:flex-row md:justify-between md:items-end mb-8 md:mb-16 gap-4">
                <div class="max-w-xl">
                    <span class="font-label-bold text-primary uppercase tracking-widest block mb-2">Our expertise</span>
                    <h2 class="text-headline-lg-mobile md:text-headline-lg text-on-background uppercase">Tailored Banking Solutions</h2>
                </div>
                <div class="h-[1px] flex-grow mx-12 bg-outline-variant/30 hidden md:block"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                @foreach ([
                    ['icon' => 'person', 'title' => 'Personal Banking', 'desc' => 'Manage your wealth with precision. From high-yield savings to bespoke mortgage solutions designed for your lifestyle.', 'url' => url('personal')],
                    ['icon' => 'corporate_fare', 'title' => 'Business Banking', 'desc' => 'Empowering enterprises with institutional-grade credit lines, treasury management, and global trade finance.', 'url' => url('business')],
                    ['icon' => 'devices', 'title' => 'Digital Banking', 'desc' => 'Banking without borders. Access your portfolio 24/7 with our secure, encrypted mobile and web platforms.', 'url' => url('apps')],
                ] as $card)
                <div class="group p-6 md:p-10 bg-surface border border-outline-variant/50 hover:border-primary transition-colors flex flex-col h-full">
                    <span class="material-symbols-outlined text-primary text-4xl md:text-5xl mb-6 group-hover:scale-110 transition-transform origin-left">{{ $card['icon'] }}</span>
                    <h3 class="font-headline-md text-on-surface mb-4">{{ $card['title'] }}</h3>
                    <p class="font-body-md text-on-surface-variant mb-8 flex-grow">{{ $card['desc'] }}</p>
                    <a href="{{ $card['url'] }}" class="flex items-center gap-2 font-label-bold text-primary uppercase tracking-widest group/btn">
                        Learn More
                        <span class="material-symbols-outlined text-sm group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why Choose --}}
    <section class="py-12 md:py-24 bg-surface-container-low overflow-hidden px-4 md:px-0">
        <div class="max-w-[1200px] mx-auto md:px-gutter flex flex-col lg:flex-row items-center gap-10 md:gap-20">
            <div class="relative w-full lg:w-1/2">
                <div class="absolute -top-6 -left-6 w-32 h-32 border-t-4 border-l-4 border-primary/20 hidden md:block"></div>
                <div class="relative z-10 aspect-[4/3] md:aspect-[4/5] overflow-hidden shadow-2xl">
                    <img class="w-full h-full object-cover" alt="Private banker" src="{{ asset('assets/images/home-private-banker.jpg') }}">
                </div>
                <div class="absolute -bottom-10 -right-10 bg-primary p-8 text-on-primary max-w-xs shadow-xl hidden md:block">
                    <p class="font-headline-md italic mb-2">"Stability is not just a promise; it's our legacy."</p>
                    <p class="font-label-bold uppercase text-xs opacity-80 tracking-widest">— Institutional Board</p>
                </div>
            </div>
            <div class="w-full lg:w-1/2">
                <span class="font-label-bold text-primary uppercase tracking-widest block mb-4">Trust & Reliability</span>
                <h2 class="text-headline-lg-mobile md:text-headline-lg text-on-surface mb-8 md:mb-12 uppercase tracking-tight leading-tight">Why Thousands Choose {{ $settings->site_name }}</h2>
                <div class="space-y-6 md:space-y-8">
                    @foreach ([
                        ['icon' => 'shield', 'title' => 'Secure Online Banking', 'desc' => 'Multi-factor authentication and real-time fraud monitoring to protect every transaction.'],
                        ['icon' => 'trending_up', 'title' => 'Competitive Interest Rates', 'desc' => 'Market-leading rates on CDs and savings accounts to accelerate your wealth accumulation.'],
                        ['icon' => 'support_agent', 'title' => '24/7 Priority Support', 'desc' => 'Dedicated account managers available around the clock for global assistance.'],
                        ['icon' => 'public', 'title' => 'International Services', 'desc' => 'Seamless cross-border transfers and multi-currency accounts for the global citizen.'],
                    ] as $feat)
                    <div class="flex gap-4 md:gap-6 group">
                        <div class="flex-shrink-0 w-12 h-12 bg-surface-container-highest flex items-center justify-center group-hover:bg-primary transition-colors duration-300">
                            <span class="material-symbols-outlined text-primary group-hover:text-on-primary">{{ $feat['icon'] }}</span>
                        </div>
                        <div>
                            <h4 class="font-label-bold text-on-surface uppercase mb-1">{{ $feat['title'] }}</h4>
                            <p class="font-body-sm text-on-surface-variant">{{ $feat['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Global transfers --}}
    <section class="py-12 md:py-24 bg-surface px-4 md:px-0">
        <div class="max-w-[1200px] mx-auto md:px-gutter flex flex-col lg:flex-row items-center gap-10 md:gap-16">
            <div class="w-full lg:w-1/2 order-2 lg:order-1">
                <span class="font-label-bold text-primary uppercase tracking-widest block mb-4">Worldwide reach</span>
                <h2 class="text-headline-lg-mobile md:text-headline-lg text-on-surface mb-6 uppercase tracking-tight leading-tight">Send Money Across Borders with Confidence</h2>
                <p class="font-body-md text-on-surface-variant mb-8 leading-relaxed">
                    Move funds to family, partners, and suppliers in major markets. Competitive FX pricing, transparent fees, and real-time status so you always know where your transfer stands.
                </p>
                <ul class="space-y-4 mb-10">
                    @foreach ([
                        'Same-day processing on qualifying corridors',
                        'Multi-currency accounts for frequent travelers and businesses',
                        'Encrypted rails with continuous fraud monitoring',
                    ] as $point)
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-xl shrink-0">check_circle</span>
                        <span class="font-body-sm text-on-surface">{{ $point }}</span>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ url('personal') }}" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-container text-on-primary px-8 py-4 font-label-bold text-sm tracking-widest transition-all">
                    Explore Transfers
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
            <div class="w-full lg:w-1/2 order-1 lg:order-2">
                <div class="relative aspect-[4/3] overflow-hidden shadow-2xl">
                    <img class="w-full h-full object-cover" alt="Global financial district" src="{{ asset('assets/images/contact-nyc.jpg') }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-bank-charcoal/50 to-transparent"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Mobile banking --}}
    <section class="py-12 md:py-24 bg-surface-container-lowest overflow-hidden px-4 md:px-0">
        <div class="max-w-[1200px] mx-auto md:px-gutter flex flex-col lg:flex-row items-center gap-10 md:gap-16">
            <div class="w-full lg:w-1/2">
                <div class="relative aspect-[4/5] max-w-md mx-auto lg:mx-0 overflow-hidden shadow-2xl">
                    <img class="w-full h-full object-cover" alt="Mobile banking app" src="{{ asset('assets/images/personal-mobile-app.jpg') }}">
                </div>
            </div>
            <div class="w-full lg:w-1/2">
                <span class="font-label-bold text-primary uppercase tracking-widest block mb-4">Banking on the go</span>
                <h2 class="text-headline-lg-mobile md:text-headline-lg text-on-surface mb-6 uppercase tracking-tight leading-tight">Your Full Branch, In Your Pocket</h2>
                <p class="font-body-md text-on-surface-variant mb-8 leading-relaxed">
                    Check balances, pay bills, deposit checks, and authorize wires from a secure mobile experience built for clarity—not clutter. Biometric unlock and instant alerts keep you in control.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-10">
                    @foreach ([
                        ['icon' => 'fingerprint', 'title' => 'Biometric Login', 'desc' => 'Face ID and fingerprint for faster, safer access.'],
                        ['icon' => 'notifications_active', 'title' => 'Live Alerts', 'desc' => 'Know about every debit, deposit, and login.'],
                        ['icon' => 'account_balance_wallet', 'title' => 'Instant Pay', 'desc' => 'Send to contacts and pay merchants in seconds.'],
                        ['icon' => 'photo_camera', 'title' => 'Mobile Deposit', 'desc' => 'Capture checks and credit funds without a trip.'],
                    ] as $item)
                    <div class="flex gap-3">
                        <span class="material-symbols-outlined text-primary text-2xl shrink-0">{{ $item['icon'] }}</span>
                        <div>
                            <h3 class="font-label-bold text-on-surface uppercase text-sm mb-1">{{ $item['title'] }}</h3>
                            <p class="font-body-sm text-on-surface-variant">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <a href="{{ url('apps') }}" class="inline-flex items-center gap-2 border-2 border-on-surface text-on-surface hover:bg-on-surface hover:text-on-primary px-8 py-4 font-label-bold text-sm tracking-widest transition-all">
                    Get the App
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="relative py-12 md:py-24 bg-bank-charcoal overflow-hidden px-4 md:px-0">
        <div class="max-w-[1200px] mx-auto md:px-gutter relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8 md:gap-12 bg-on-background/30 p-8 md:p-12 lg:p-20 border border-on-primary/10">
                <div class="max-w-2xl text-center md:text-left">
                    <h2 class="text-headline-lg-mobile md:text-headline-lg text-on-primary mb-6 uppercase tracking-tight">Your Financial Future <br><span class="text-primary">Starts Here</span></h2>
                    <p class="font-body-lg text-on-primary opacity-80">Join a network of elite professionals and institutions who trust {{ $settings->site_name }} for their most critical financial endeavors.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                    <a href="{{ url('register') }}" class="bg-primary hover:bg-primary-container text-on-primary px-10 py-5 font-label-bold text-sm tracking-[0.15em] transition-all whitespace-nowrap text-center">OPEN AN ACCOUNT</a>
                    <a href="{{ url('contact') }}" class="border-2 border-on-primary text-on-primary hover:bg-on-primary hover:text-bank-charcoal px-10 py-5 font-label-bold text-sm tracking-[0.15em] transition-all whitespace-nowrap text-center">CONTACT US</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
function homeHeroSlider(total) {
    return {
        // logical slide 0..total-1 (for dots)
        index: 0,
        // track position includes leading clone (+1)
        track: 1,
        total: total,
        dragging: false,
        jumping: false,
        dragPct: 0,
        startX: 0,
        width: 1,
        autoTimer: null,
        init() {
            this.restartAuto();
        },
        restartAuto() {
            clearInterval(this.autoTimer);
            this.autoTimer = setInterval(() => {
                if (!this.dragging && !this.jumping) this.next();
            }, 7000);
        },
        syncIndexFromTrack() {
            if (this.track === 0) this.index = this.total - 1;
            else if (this.track === this.total + 1) this.index = 0;
            else this.index = this.track - 1;
        },
        go(logical) {
            this.jumping = false;
            this.track = logical + 1;
            this.index = logical;
            this.dragPct = 0;
            this.restartAuto();
        },
        next() {
            if (this.jumping) return;
            this.track = this.track + 1;
            this.syncIndexFromTrack();
            this.dragPct = 0;
            this.restartAuto();
        },
        prev() {
            if (this.jumping) return;
            this.track = this.track - 1;
            this.syncIndexFromTrack();
            this.dragPct = 0;
            this.restartAuto();
        },
        onTransitionEnd(e) {
            if (e && e.target !== e.currentTarget) return;
            // Seamless loop: snap from clones to real slides without animation
            if (this.track === this.total + 1) {
                this.jumping = true;
                this.track = 1;
                this.index = 0;
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => { this.jumping = false; });
                });
            } else if (this.track === 0) {
                this.jumping = true;
                this.track = this.total;
                this.index = this.total - 1;
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => { this.jumping = false; });
                });
            }
        },
        onPointerDown(e) {
            if (e.pointerType === 'mouse' && e.button !== 0) return;
            if (e.target.closest('a, button')) return;
            this.dragging = true;
            this.startX = e.clientX;
            this.width = this.$el.offsetWidth || 1;
            this.dragPct = 0;
            e.currentTarget.setPointerCapture(e.pointerId);
            clearInterval(this.autoTimer);
        },
        onPointerMove(e) {
            if (!this.dragging) return;
            var dx = e.clientX - this.startX;
            this.dragPct = (dx / this.width) * 100;
        },
        onPointerUp(e) {
            if (!this.dragging) return;
            this.dragging = false;
            try { e.currentTarget.releasePointerCapture(e.pointerId); } catch (err) {}
            var threshold = 12;
            if (this.dragPct <= -threshold) this.next();
            else if (this.dragPct >= threshold) this.prev();
            this.dragPct = 0;
            this.restartAuto();
        }
    };
}
</script>
@endsection
