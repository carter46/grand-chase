@php
    if ($settings->redirect_url != null || !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
@endphp
@extends('layouts.base')
@section('title', 'Terms of Service')

@section('content')
<section class="relative min-h-[calc(100vh-104px)] md:min-h-[calc(100vh-131px)] flex items-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ asset('assets/images/loans-architecture.jpg') }}')"></div>
        <div class="absolute inset-0 bg-inverse-surface/75"></div>
    </div>
    <div class="relative z-10 max-w-[1200px] mx-auto px-4 md:px-gutter w-full py-12 md:py-16">
        <span class="font-label-bold text-primary uppercase tracking-widest">{{ $settings->site_name }} Policy</span>
        <h1 class="text-hero-small text-on-primary mt-4 uppercase">Terms of Service</h1>
        <p class="font-body-lg text-on-primary/70 mt-4 max-w-2xl">Learn more about how {{ $settings->site_name }} protects and uses your personal information.</p>
    </div>
</section>

<section class="py-16 md:py-24 bg-surface">
    <div class="max-w-[900px] mx-auto px-4 md:px-gutter">
        <div class="bg-white border border-surface-container-highest p-8 md:p-12 prose prose-sm md:prose-base max-w-none font-body-md text-on-surface-variant">
            <h2 class="font-headline-md text-on-surface uppercase mb-4">Our Terms Of Data</h2>
            <p>We are {{ $settings->site_name }}, the data controller.@if ($settings->site_address) You can contact our Data Protection Officer (DPO) at {{ $settings->site_address }} if you have any questions.@endif</p>
            <p>This Privacy Statement explains how we obtain, use and keep your personal data safe in relation to the {{ $settings->site_name }} website.</p>
            <p>Your personal data is data which by itself or with other data available to us can be used to identify you.</p>
            <p>We're committed to keeping your personal information safe in accordance with applicable data protection laws.</p>
            <h3 class="font-label-bold text-on-surface uppercase mt-8 mb-3">The types of personal data we collect and use</h3>
            <p>The types of personal data we capture and use will depend on what you are doing on the website. Examples may include full name and contact information, date of birth, financial details, records of products and services you've obtained, biometric data where applicable, and information from credit reference agencies.</p>
            <p>If you become a customer we'll also use your data to manage the account, policy or service you've applied for and we'll provide you with a separate data protection statement as part of the application process.</p>
        </div>
    </div>
</section>
@endsection
