<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index,follow">
    <meta name="description" content="{{ $settings->site_name }} — institutional banking with secure online services.">
    <meta property="og:site_name" content="{{ $settings->site_name }}">
    <title>@yield('title', 'Home') - {{ $settings->site_name }}</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/app/public/'.$settings->favicon) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @include('includes.fsm-tailwind-config')

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        @layer base {
            html, body { margin: 0; padding: 0; }
            body { overscroll-behavior: none; font-family: Inter, sans-serif; }
        }
        @keyframes fade-in-down {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down { animation: fade-in-down 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .pt-safe { padding-top: env(safe-area-inset-top); }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom); }

        /* Semantic hero typography */
        .text-hero-large {
            font-family: Inter, sans-serif;
            font-size: 28px;
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .text-hero-medium {
            font-family: Inter, sans-serif;
            font-size: 28px;
            line-height: 1.15;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .text-hero-small {
            font-family: Inter, sans-serif;
            font-size: 24px;
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: -0.01em;
        }
        @media (min-width: 640px) {
            .text-hero-large { font-size: 40px; }
            .text-hero-medium { font-size: 36px; }
            .text-hero-small { font-size: 28px; }
        }
        @media (min-width: 768px) {
            .text-hero-large { font-size: 64px; line-height: 1.1; }
            .text-hero-medium { font-size: 56px; line-height: 1.1; }
            .text-hero-small { font-size: 40px; line-height: 1.15; }
        }

        /* Google Translate — prevent header clipping */
        #google_translate_element,
        #google_translate_slot_desktop,
        #google_translate_slot_mobile {
            overflow: visible !important;
        }
        #google_translate_element.sr-only:not(:empty) {
            position: static;
            width: auto;
            height: auto;
            padding: 0;
            margin: 0;
            overflow: visible;
            clip: auto;
            white-space: normal;
        }
        .goog-te-gadget {
            font-size: 12px !important;
            color: #1a1c1c !important;
        }
        .goog-te-gadget .goog-te-combo {
            max-width: 100%;
            min-width: 120px;
            padding: 4px 8px;
            border: 1px solid #e6bdb2;
            background: #fff;
            color: #1a1c1c;
        }
        .goog-te-banner-frame.skiptranslate { display: none !important; }
        body { top: 0 !important; }
        .goog-logo-link, .goog-te-gadget span { display: none !important; }
        .goog-te-gadget { color: transparent !important; }
        .goog-te-gadget .goog-te-combo { color: #1a1c1c !important; }
    </style>

    @yield('styles')
    @include('includes.live-chat-widget')
</head>
<body class="bg-background font-body-md text-on-background">

@include('includes.public-header')

<main class="w-full pt-[104px] md:pt-[131px] min-h-screen">
    @yield('content')
</main>

@include('includes.public-footer')

@if($settings->tido)
<script src="//code.tidio.co/{{ $settings->tido }}" async></script>
@endif

<script>
function placeTranslateWidget() {
    var el = document.getElementById('google_translate_element');
    var desk = document.getElementById('google_translate_slot_desktop');
    var mob = document.getElementById('google_translate_slot_mobile');
    if (!el || !desk || !mob) return;
    el.classList.remove('sr-only');
    el.removeAttribute('aria-hidden');
    if (window.matchMedia('(min-width: 768px)').matches) {
        desk.appendChild(el);
    } else {
        mob.appendChild(el);
    }
}
function googleTranslateElementInit() {
    new google.translate.TranslateElement({ pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL }, 'google_translate_element');
    placeTranslateWidget();
}
window.addEventListener('resize', placeTranslateWidget);
document.addEventListener('DOMContentLoaded', placeTranslateWidget);
</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

@yield('scripts')
</body>
</html>
