<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $settings->site_name }} - @yield('title')</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="index, follow">
    <meta name="apple-mobile-web-app-title" content="{{$settings->site_name}}">
    <meta name="application-name" content="{{$settings->site_name}}">
    <meta name="description" content="Swift and Secure Money Transfer to any UK bank account will become a breeze with {{$settings->site_name}}.">
    <link rel="shortcut icon" href="{{ asset('storage/app/public/' . $settings->favicon) }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '{{ isset($appearanceSettings) ? $appearanceSettings->primary_color_light : "#f0f9ff" }}',
                            100: '{{ isset($appearanceSettings) ? $appearanceSettings->primary_color_light : "#e0f2fe" }}',
                            200: '{{ isset($appearanceSettings) ? $appearanceSettings->primary_color_light : "#bae6fd" }}',
                            300: '{{ isset($appearanceSettings) ? $appearanceSettings->primary_color_light : "#7dd3fc" }}',
                            400: '{{ isset($appearanceSettings) ? $appearanceSettings->primary_color_light : "#38bdf8" }}',
                            500: '{{ isset($appearanceSettings) ? $appearanceSettings->primary_color : "#0ea5e9" }}',
                            600: '{{ isset($appearanceSettings) ? $appearanceSettings->primary_color : "#0284c7" }}',
                            700: '{{ isset($appearanceSettings) ? $appearanceSettings->primary_color_dark : "#0369a1" }}',
                            800: '{{ isset($appearanceSettings) ? $appearanceSettings->primary_color_dark : "#075985" }}',
                            900: '{{ isset($appearanceSettings) ? $appearanceSettings->primary_color_dark : "#0c4a6e" }}',
                        },
                        secondary: {
                            50: '{{ isset($appearanceSettings) ? $appearanceSettings->secondary_color_light : "#f0fdfa" }}',
                            100: '{{ isset($appearanceSettings) ? $appearanceSettings->secondary_color_light : "#ccfbf1" }}',
                            200: '{{ isset($appearanceSettings) ? $appearanceSettings->secondary_color_light : "#99f6e4" }}',
                            300: '{{ isset($appearanceSettings) ? $appearanceSettings->secondary_color_light : "#5eead4" }}',
                            400: '{{ isset($appearanceSettings) ? $appearanceSettings->secondary_color_light : "#2dd4bf" }}',
                            500: '{{ isset($appearanceSettings) ? $appearanceSettings->secondary_color : "#14b8a6" }}',
                            600: '{{ isset($appearanceSettings) ? $appearanceSettings->secondary_color : "#0d9488" }}',
                            700: '{{ isset($appearanceSettings) ? $appearanceSettings->secondary_color_dark : "#0f766e" }}',
                            800: '{{ isset($appearanceSettings) ? $appearanceSettings->secondary_color_dark : "#115e59" }}',
                            900: '{{ isset($appearanceSettings) ? $appearanceSettings->secondary_color_dark : "#134e4a" }}',
                        }
                    },
                    fontFamily: {
                        'sans': ['Lato', 'sans-serif'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                          '0%, 100%': { transform: 'translateY(0)' },
                          '50%': { transform: 'translateY(-10px)' },
                        }
                    },
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    
    <!-- CSS Variables -->
    <script>
        // Set CSS theme variables
        document.documentElement.style.setProperty('--primary-color', '{{ isset($appearanceSettings) ? $appearanceSettings->primary_color : "#0ea5e9" }}');
        document.documentElement.style.setProperty('--primary-color-dark', '{{ isset($appearanceSettings) ? $appearanceSettings->primary_color_dark : "#0369a1" }}');
        document.documentElement.style.setProperty('--primary-color-light', '{{ isset($appearanceSettings) ? $appearanceSettings->primary_color_light : "#38bdf8" }}');
        document.documentElement.style.setProperty('--secondary-color', '{{ isset($appearanceSettings) ? $appearanceSettings->secondary_color : "#14b8a6" }}');
        document.documentElement.style.setProperty('--secondary-color-dark', '{{ isset($appearanceSettings) ? $appearanceSettings->secondary_color_dark : "#0f766e" }}');
        document.documentElement.style.setProperty('--secondary-color-light', '{{ isset($appearanceSettings) ? $appearanceSettings->secondary_color_light : "#5eead4" }}');
        document.documentElement.style.setProperty('--text-color', '{{ isset($appearanceSettings) ? $appearanceSettings->text_color : "#111827" }}');
        document.documentElement.style.setProperty('--bg-color', '{{ isset($appearanceSettings) ? $appearanceSettings->bg_color : "#f9fafb" }}');
        document.documentElement.style.setProperty('--card-bg-color', '{{ isset($appearanceSettings) ? $appearanceSettings->card_bg_color : "#ffffff" }}');
    </script>
    
    @if(isset($appearanceSettings) && $appearanceSettings->custom_css)
    <style>
        {!! $appearanceSettings->custom_css !!}
    </style>
    @endif
    
    <!-- Animated Loading Screen -->
    <style>
        .page-loading {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transition: all .4s .2s ease-in-out;
            background-color: #ffffff;
            visibility: hidden;
            z-index: 9999;
        }
        .page-loading.active {
            opacity: 1;
            visibility: visible;
        }
        .page-loading-inner {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            text-align: center;
            transform: translateY(-50%);
            transition: opacity .2s ease-in-out;
            opacity: 0;
        }
        .page-loading.active>.page-loading-inner {
            opacity: 1;
        }
        
        .loader {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
        }
        
        .loader-circle {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 4px solid transparent;
            border-top-color: var(--primary-color);
            animation: spin 2s linear infinite;
        }
        
        .loader-circle:nth-child(2) {
            border-top-color: transparent;
            border-right-color: var(--primary-color);
            animation: spin 3s linear infinite;
        }
        
        .loader-circle:nth-child(3) {
            width: 80%;
            height: 80%;
            top: 10%;
            left: 10%;
            border-top-color: transparent;
            border-left-color: var(--primary-color-light);
            animation: spin 1.5s linear infinite reverse;
        }
        
        .loader-logo {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40%;
            height: 40%;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .loader-progress {
            position: relative;
            width: 200px;
            height: 4px;
            background-color: rgba(14, 165, 233, 0.2);
            border-radius: 2px;
            margin: 10px auto;
            overflow: hidden;
        }
        
        .loader-progress-bar {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 0%;
            background-color: var(--primary-color);
            border-radius: 2px;
            animation: progress 2s ease-in-out infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes progress {
            0% { width: 0%; }
            50% { width: 100%; }
            100% { width: 0%; left: 100%; }
        }
        
        /* Numeric keypad styles */
        .keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }
        
        .key {
            aspect-ratio: 1/1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            user-select: none;
        }
        
        .key:active {
            transform: scale(0.95);
        }
        
        .pin-display {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .pin-digit {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background-color: #e5e7eb;
            transition: all 0.2s;
        }
        
        .pin-digit.active {
            background-color: var(--primary-color);
        }
        
        /* 3D button effect */
        .btn-3d {
            position: relative;
            transition: all 0.2s;
            transform-style: preserve-3d;
            transform: perspective(800px) translateZ(0);
        }
        
        .btn-3d:hover {
            transform: perspective(800px) translateZ(10px);
        }
        
        .btn-3d:active {
            transform: perspective(800px) translateZ(2px);
        }
        
        .btn-3d::before {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            right: 0;
            height: 4px;
            background-color: rgba(0, 0, 0, 0.1);
            border-bottom-left-radius: inherit;
            border-bottom-right-radius: inherit;
            transition: all 0.2s;
        }
        
        .btn-3d:active::before {
            height: 2px;
            bottom: -2px;
        }
    </style>
    @laravelPWA
</head>

<body class="font-sans bg-gray-50 text-gray-900">
    <!-- Page Loader -->
    <div class="page-loading active">
        <div class="page-loading-inner">
            <div class="loader">
                <div class="loader-circle"></div>
                <div class="loader-circle"></div>
                <div class="loader-circle"></div>
                <div class="loader-logo">
                    <img src="{{ asset('storage/app/public/'.$settings->logo) }}" alt="Logo" class="w-3/4 h-auto">
                </div>
            </div>
            <div class="loader-progress">
                <div class="loader-progress-bar"></div>
            </div>
            <p class="mt-3 text-sm text-gray-500">
                Loading secure environment...
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="min-h-screen flex flex-col">
        <main class="flex-grow flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-md">
                @yield('content')
            </div>
        </main>
        
        <footer class="py-4 bg-white border-t border-gray-200">
            <div class="container mx-auto px-4 text-center">
                <p class="text-sm text-gray-600">&copy; {{ date('Y') }} {{ $settings->site_name }}. All rights reserved.</p>
                <div class="mt-2 flex justify-center space-x-4">
                    <a href="#" class="text-xs text-gray-500 hover:text-primary-600">Privacy Policy</a>
                    <a href="#" class="text-xs text-gray-500 hover:text-primary-600">Terms of Service</a>
                    <a href="#" class="text-xs text-gray-500 hover:text-primary-600">Contact Support</a>
                </div>
            </div>
        </footer>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
    
    <!-- Enhanced Page Loading Animation -->
    <script>
        window.onload = function() {
            const preloader = document.querySelector('.page-loading');
            
            // Add a slight delay to make loading animation more noticeable
            setTimeout(function() {
                preloader.classList.remove('active');
                setTimeout(function() {
                    preloader.remove();
                }, 500);
            }, 800);
        };
    </script>
    
    
    @yield('scripts')
</body>
</html>