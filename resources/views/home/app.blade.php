@php
    if ($settings->redirect_url != null || !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
@endphp
@extends('layouts.base')
@section('title', 'Download App')

@section('content')
<section class="relative bg-inverse-surface py-16 md:py-24">
    <div class="max-w-[1200px] mx-auto px-4 md:px-gutter">
        <span class="font-label-bold text-primary uppercase tracking-widest">Mobile</span>
        <h1 class="text-hero-small text-on-primary mt-4 uppercase">Download App</h1>
        <p class="font-body-lg text-on-primary/70 mt-4 max-w-2xl">Our digital banking platform is trustworthy, fast, and available wherever you are.</p>
    </div>
</section>

<section class="py-16 md:py-24 bg-surface">
    <div class="max-w-[1200px] mx-auto px-4 md:px-gutter">
        <div class="bg-white border border-surface-container-highest p-8 mb-12 flex flex-col md:flex-row gap-8 items-center">
            <img src="{{ asset('temp/custom/images/105536.png') }}" alt="App" width="120">
            <div>
                <h2 class="font-headline-md text-on-surface uppercase mb-2">Mobile Banking</h2>
                <p class="font-body-md text-on-surface-variant">App availability may vary by region. Contact support for assistance with mobile access.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach ([['icon' => 'phone_iphone', 'title' => 'Apple iOS', 'url' => 'apps'], ['icon' => 'android', 'title' => 'Google Android', 'url' => 'apps'], ['icon' => 'smart_toy', 'title' => 'Voice Assistants', 'url' => 'contact']] as $platform)
            <a href="{{ url($platform['url']) }}" class="group p-6 bg-white border border-surface-container-highest hover:border-primary transition-colors">
                <span class="material-symbols-outlined text-primary text-4xl mb-4">{{ $platform['icon'] }}</span>
                <h3 class="font-label-bold text-on-surface uppercase">{{ $platform['title'] }}</h3>
            </a>
            @endforeach
        </div>

        <div class="mt-16 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <img src="{{ asset('temp/custom/assets/img/app-screen.png') }}" alt="App screen" class="w-full max-w-md mx-auto">
            <div>
                <span class="font-label-bold text-primary uppercase tracking-widest">Our App</span>
                <h2 class="font-headline-md text-on-surface uppercase mt-2 mb-4">Banking On The Go</h2>
                <p class="font-body-md text-on-surface-variant mb-6">Use your mobile device for transactions, loan requests, and credit card management with bank-grade security.</p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ url('apps') }}"><img src="{{ asset('temp/custom/assets/img/about/play-store.png') }}" alt="Play Store" class="h-12"></a>
                    <a href="{{ url('apps') }}"><img src="{{ asset('temp/custom/assets/img/about/app-store.png') }}" alt="App Store" class="h-12"></a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
