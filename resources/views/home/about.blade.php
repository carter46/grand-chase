@php
    if ($settings->redirect_url != null || !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
@endphp
@extends('layouts.base')
@section('title', 'About Us')

@section('content')
<section class="relative bg-inverse-surface py-16 md:py-24">
    <div class="max-w-[1200px] mx-auto px-4 md:px-gutter">
        <span class="font-label-bold text-primary uppercase tracking-widest">About Us</span>
        <h1 class="text-hero-small text-on-primary mt-4 uppercase">Digital Banking, Humanized</h1>
        <p class="font-body-lg text-on-primary/70 mt-4 max-w-2xl">We've developed to become one of the most well-known digital banking providers, dedicated to reinventing, simplifying, and humanizing the banking experience.</p>
    </div>
</section>

<section class="py-16 md:py-24 bg-surface">
    <div class="max-w-[1200px] mx-auto px-4 md:px-gutter grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="grid grid-cols-2 gap-4">
            <img src="{{ asset('temp/custom/assets/img/about/about-img-1.jpg') }}" alt="About" class="w-full h-48 object-cover border border-outline-variant/30">
            <img src="{{ asset('temp/custom/assets/img/about/about-img-2.jpg') }}" alt="About" class="w-full h-48 object-cover border border-outline-variant/30 mt-8">
            <img src="{{ asset('temp/custom/assets/img/about/about-img-3.jpg') }}" alt="About" class="w-full h-48 object-cover border border-outline-variant/30">
            <img src="{{ asset('temp/custom/assets/img/about/about-img-4.jpg') }}" alt="About" class="w-full h-48 object-cover border border-outline-variant/30 mt-8">
        </div>
        <div>
            <h2 class="font-headline-md text-on-surface uppercase mb-6">Built on Trust & Innovation</h2>
            @foreach ([
                ['title' => 'Powerful Mobile & Online App', 'desc' => 'Our mobile app service is quick and easy to use, and you can get it from your app store.'],
                ['title' => 'Transparency & Speed', 'desc' => 'Our digital banking services are transparent and quick, and we are building a reliable network.'],
                ['title' => 'Multi-User Capabilities', 'desc' => 'Enterprise-grade access controls for families and businesses who need shared financial visibility.'],
            ] as $item)
            <div class="flex gap-4 mb-6 pb-6 border-b border-outline-variant/20 last:border-0">
                <span class="material-symbols-outlined text-primary">verified</span>
                <div>
                    <h3 class="font-label-bold text-on-surface uppercase mb-1">{{ $item['title'] }}</h3>
                    <p class="font-body-sm text-on-surface-variant">{{ $item['desc'] }}</p>
                </div>
            </div>
            @endforeach
            <a href="{{ url('register') }}" class="inline-block bg-primary hover:bg-primary-container text-on-primary px-8 py-4 font-label-bold uppercase tracking-widest">Start With Us</a>
        </div>
    </div>
</section>
@endsection
