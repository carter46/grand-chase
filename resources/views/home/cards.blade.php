@php
    if ($settings->redirect_url != null || !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
    $activeNav = 'cards';
@endphp
@extends('layouts.base')
@section('title', 'Credit Cards')

@section('content')
<section class="relative min-h-[calc(100vh-104px)] md:min-h-[calc(100vh-131px)] flex items-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ asset('assets/images/hero-investments.jpg') }}')"></div>
        <div class="absolute inset-0 bg-inverse-surface/70"></div>
    </div>
    <div class="relative z-10 max-w-[1200px] mx-auto px-4 md:px-gutter w-full py-12 md:py-16">
        <span class="font-label-bold text-primary uppercase tracking-widest">Cards</span>
        <h1 class="text-hero-small text-on-primary mt-4 uppercase">Credit Cards</h1>
        <p class="font-body-lg text-on-primary/70 mt-4 max-w-2xl">See if you're pre-approved for a credit card from {{ $settings->site_name }}.</p>
    </div>
</section>

<section class="py-16 md:py-24 bg-surface">
    <div class="max-w-[1200px] mx-auto px-4 md:px-gutter">
        <div class="bg-white border border-surface-container-highest p-8 mb-12">
            <h2 class="font-headline-md text-on-surface uppercase mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">credit_card</span> Card Details
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                @foreach ([['img' => '3527271.png', 'label' => 'VISA'], ['img' => '349221.png', 'label' => 'MAESTRO'], ['img' => '349228.png', 'label' => 'AMEX'], ['img' => '349230.png', 'label' => 'DISCOVER']] as $card)
                <div class="p-4 border border-surface-container-highest">
                    <img src="{{ asset('temp/custom/images/'.$card['img']) }}" alt="{{ $card['label'] }}" class="mx-auto h-12 object-contain mb-3">
                    <span class="font-label-bold text-on-surface uppercase text-sm">{{ $card['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center bg-surface-container-low p-8 border border-surface-container-highest">
            <img src="{{ asset('temp/custom/images/1086741.png') }}" alt="Apply for card" class="mx-auto max-w-[200px]">
            <div>
                <h3 class="font-headline-md text-on-surface uppercase mb-4">Apply For Credit Cards</h3>
                <p class="font-body-md text-on-surface-variant mb-6">Welcome to {{ $settings->site_name }}. Apply for credit cards to be delivered to your doorstep.</p>
                <a href="{{ url('login') }}" class="inline-block bg-primary hover:bg-primary-container text-on-primary px-8 py-4 font-label-bold uppercase tracking-widest">Apply Now</a>
            </div>
        </div>
    </div>
</section>
@endsection
