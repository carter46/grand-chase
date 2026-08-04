<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title') - {{ $settings->site_name }}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Secure online banking with {{ $settings->site_name }}.">
    <link rel="shortcut icon" href="{{ asset('storage/app/public/' . $settings->favicon) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @include('includes.fsm-tailwind-config')

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        [x-cloak] { display: none !important; }
    </style>

    @yield('styles')
</head>
<body class="bg-surface font-body-md text-on-surface min-h-screen">
    @yield('content')

    @if($settings->tido)
    <script src="//code.tidio.co/{{ $settings->tido }}" async></script>
    @endif

    @yield('scripts')
</body>
</html>
