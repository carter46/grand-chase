@php
    if ($settings->redirect_url != null || !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
@endphp
@extends('layouts.base')
@section('title', 'Privacy Policy')

@section('content')
<section class="relative bg-inverse-surface py-16 md:py-24">
    <div class="max-w-[1200px] mx-auto px-4 md:px-gutter">
        <span class="font-label-bold text-primary uppercase tracking-widest">{{ $settings->site_name }}</span>
        <h1 class="font-headline-lg-mobile md:font-headline-xl text-on-primary mt-4 uppercase">Privacy Policy</h1>
        <p class="font-body-lg text-on-primary/70 mt-4 max-w-2xl">How we collect, use, and safeguard your personal information.</p>
    </div>
</section>

<section class="py-16 md:py-24 bg-surface">
    <div class="max-w-[900px] mx-auto px-4 md:px-gutter">
        <div class="bg-white border border-surface-container-highest p-8 md:p-12 font-body-md text-on-surface-variant leading-relaxed">
            @if(isset($terms) && $terms)
                {!! $terms->description !!}
            @else
                <p>Privacy policy content is not yet configured.</p>
            @endif
        </div>
    </div>
</section>
@endsection
