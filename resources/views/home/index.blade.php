@php
    if ($settings->redirect_url != null || !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
@endphp
@extends('layouts.base')
@section('title', 'Home')

@section('content')
<div class="flex flex-col w-full">
    {{-- Hero --}}
    <section class="relative min-h-[500px] md:min-h-[calc(100vh-131px)] md:min-h-[600px] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img alt="Corporate banking" class="w-full h-full object-cover hidden md:block" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCB-Dz14NN9BBan-8l9zSuUSK1bFawS6wMsI5atq2IMGG3SI79bYRI4z3gm882dVYABCZruCMx6DMjL6rNGnC96Vxz88MunqCs5yDhm3RvgiY22C2Y_DC8X6-3d1dG_WVzyNVBO8KDB7zHhqr42Ne7bsviVbgoWodcRxIS-aW8ziDX2HPW2IX3erb6fS5Y2ydTmIURUajWQE0j8AHu8scbmIRMdstyK0jKSCF3I7vwV9tF83RFPIPN3">
            <div class="w-full h-full bg-cover bg-center md:hidden" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuALg1OlifmvMZXAnUE4QnLaX3TJtUkDgJqgIO1Go0WYw0ETJ6W1JifFRXC-7J3LyAra00fTwOpZwVM_Ws56vcUgNMqJ0RgILseT3z16wXKMxhikJ1gpNlKQUy2OIr5EXPSa0UEwgPaHnigfM1l8GNO1tWBaIRJJi1sp3WtWAPmp7aXf0fsUDWEzyUp1RCW5Ap3QQPh5mYVRAIAa_YyNUimucN6O8WmRjr346u6Y6d_JXAnJen1jhKUs')"></div>
            <div class="absolute inset-0 bg-black/55 backdrop-brightness-75"></div>
        </div>
        <div class="relative z-10 max-w-[1200px] mx-auto px-4 md:px-gutter text-center flex flex-col items-center py-12">
            <span class="font-label-bold text-on-primary uppercase tracking-[0.2em] mb-stack-md animate-fade-in-down">Welcome to {{ $settings->site_name }}</span>
            <h1 class="font-headline-lg-mobile md:font-headline-xl text-on-primary md:text-[64px] leading-tight mb-stack-lg uppercase tracking-tighter">
                YOUR <span class="text-primary md:text-primary italic">BANK</span> YOUR WAY
            </h1>
            <p class="font-body-md md:font-body-lg text-on-primary opacity-90 max-w-[650px] mb-stack-lg leading-relaxed px-2">
                A bank account that gives you more. Rewards checking from {{ $settings->site_name }} offers the flexibility and convenience you deserve in a global marketplace.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 md:gap-stack-md w-full sm:w-auto px-4 sm:px-0">
                <a href="{{ url('login') }}" class="bg-primary hover:bg-primary-container text-on-primary px-10 py-4 font-label-bold text-sm tracking-widest transition-all shadow-xl hover:-translate-y-1 text-center">LOGIN</a>
                <a href="{{ url('register') }}" class="bg-bank-charcoal hover:bg-on-background text-on-primary px-10 py-4 font-label-bold text-sm tracking-widest transition-all shadow-xl hover:-translate-y-1 text-center">SIGN UP</a>
            </div>
        </div>
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 hidden md:flex flex-col items-center gap-2 opacity-50 text-on-primary">
            <span class="font-caption uppercase tracking-widest">Explore</span>
            <div class="w-[1px] h-12 bg-gradient-to-b from-on-primary to-transparent"></div>
        </div>
    </section>

    {{-- Banking Solutions --}}
    <section class="py-12 md:py-24 bg-surface-container-lowest px-4 md:px-0">
        <div class="max-w-[1200px] mx-auto md:px-gutter">
            <div class="flex flex-col md:flex-row md:justify-between md:items-end mb-8 md:mb-16 gap-4">
                <div class="max-w-xl">
                    <span class="font-label-bold text-primary uppercase tracking-widest block mb-2">Our expertise</span>
                    <h2 class="font-headline-lg-mobile md:font-headline-lg text-on-background uppercase">Tailored Banking Solutions</h2>
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
                    <img class="w-full h-full object-cover grayscale-[20%] hover:grayscale-0 transition-all duration-700" alt="Private banker" src="https://lh3.googleusercontent.com/aida-public/AB6AXuArPHiwRVNy-GyPVUkOvQkfjgZNQpUbp3ax7ybJYTQstOtyr9YN0hMGkRPADGbwk3MszV6rUA1ac36pn-i7MdhphjGmiIyfNYEYKqhKOOp-5AW-K5d-daHItbeY8Dt2qaljmjhEjP4dPo2ZckhIt2_dc2NMMuFlyHGGWaLXnjeEmkdCCtMpvxx0LvqeGziNIBFgoIsAj1mQh9L-jReTN2AEi_OXadUH4M-hhR5_DcUh1adskOYziwCZ">
                </div>
                <div class="absolute -bottom-10 -right-10 bg-primary p-8 text-on-primary max-w-xs shadow-xl hidden md:block">
                    <p class="font-headline-md italic mb-2">"Stability is not just a promise; it's our legacy."</p>
                    <p class="font-label-bold uppercase text-xs opacity-80 tracking-widest">— Institutional Board</p>
                </div>
            </div>
            <div class="w-full lg:w-1/2">
                <span class="font-label-bold text-primary uppercase tracking-widest block mb-4">Trust & Reliability</span>
                <h2 class="font-headline-lg-mobile md:font-headline-lg text-on-surface mb-8 md:mb-12 uppercase tracking-tight leading-tight">Why Thousands Choose {{ $settings->site_name }}</h2>
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

    {{-- CTA --}}
    <section class="relative py-12 md:py-24 bg-bank-charcoal overflow-hidden px-4 md:px-0">
        <div class="max-w-[1200px] mx-auto md:px-gutter relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8 md:gap-12 bg-on-background/30 p-8 md:p-12 lg:p-20 border border-on-primary/10">
                <div class="max-w-2xl text-center md:text-left">
                    <h2 class="font-headline-lg-mobile md:font-headline-xl text-on-primary mb-6 uppercase tracking-tight">Your Financial Future <br><span class="text-primary">Starts Here</span></h2>
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
