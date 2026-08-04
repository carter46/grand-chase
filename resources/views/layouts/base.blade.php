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
function googleTranslateElementInit() {
    new google.translate.TranslateElement({ pageLanguage: 'en' }, 'google_translate_element');
}
</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

@yield('scripts')
</body>
</html>
