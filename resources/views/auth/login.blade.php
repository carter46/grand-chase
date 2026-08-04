@extends('layouts.guest2')
@section('title', 'Login')

@section('content')
@include('includes.public-auth-chrome')

<main class="w-full min-h-[calc(100vh-3.5rem)] bg-surface">
<div class="flex flex-col lg:flex-row min-h-[calc(100vh-3.5rem)] w-full">
    <div class="relative flex-1 bg-inverse-surface flex items-center justify-center overflow-hidden p-6 md:p-margin-desktop min-h-[280px] lg:min-h-0">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 32px 32px;"></div>
        <div class="relative z-10 max-w-lg flex flex-col gap-stack-lg">
            <div class="flex flex-col gap-stack-sm">
                <span class="font-label-bold text-primary tracking-[0.2em] uppercase">Institutional Excellence</span>
                <h1 class="font-headline-xl text-on-primary text-3xl md:text-[48px] leading-tight">Secure Financial Hub</h1>
                <p class="font-body-lg text-on-primary/70">Experience modern banking with {{ $settings->site_name }}. Seamless transactions and comprehensive financial management.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-gutter">
                @foreach ([['icon' => 'shield_person', 'title' => 'Secure Transfers'], ['icon' => 'bolt', 'title' => 'Lightning Fast'], ['icon' => 'public', 'title' => 'Global Access'], ['icon' => 'smartphone', 'title' => 'Mobile Ready']] as $f)
                <div class="flex items-start gap-4 p-4 rounded bg-white/5">
                    <span class="material-symbols-outlined text-primary text-[32px]">{{ $f['icon'] }}</span>
                    <span class="font-label-bold text-on-primary uppercase text-[12px] tracking-wider">{{ $f['title'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="flex-1 flex items-center justify-center bg-surface p-6 md:p-margin-desktop">
        <div class="w-full max-w-[440px] flex flex-col gap-stack-lg">
            <div class="flex flex-col items-center lg:items-start text-center lg:text-left gap-stack-sm">
                <h2 class="text-headline-lg text-on-surface">Access Your Account</h2>
                <p class="font-body-md text-on-surface-variant">Sign in to your secure banking portal.</p>
            </div>

            @if (Session::has('status'))
            <div class="p-4 bg-error-container border border-error text-on-error-container font-body-sm">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
            <div class="p-4 bg-error-container border border-error text-on-error-container font-body-sm">
                <ul class="list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
            @endif

            <div class="bg-white border border-surface-container-highest p-stack-lg shadow-xl">
                <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-6">
                    @csrf
                    <input type="text" name="website_url" class="hidden" tabindex="-1" autocomplete="off">

                    <div class="flex flex-col gap-2">
                        <label class="font-label-bold text-on-surface text-[12px] uppercase tracking-wider" for="email">Email Address</label>
                        <div class="relative group">
                            <input id="email" type="email" name="email" required autocomplete="email" placeholder="Enter your registered email"
                                class="w-full px-4 py-3 bg-surface border border-surface-container-highest focus:border-primary focus:ring-0 outline-none font-body-md">
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant/40 group-focus-within:text-primary">mail</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between items-end">
                            <label class="font-label-bold text-on-surface text-[12px] uppercase tracking-wider" for="password">Password</label>
                            <a class="font-label-md text-primary hover:underline text-[12px]" href="{{ route('password.request') }}">Forgot Password?</a>
                        </div>
                        <div class="relative group">
                            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                                class="w-full px-4 py-3 bg-surface border border-surface-container-highest focus:border-primary focus:ring-0 outline-none font-body-md">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant/40 hover:text-on-surface">
                                <span class="material-symbols-outlined" id="passIcon">visibility</span>
                            </button>
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="remember_me" checked class="border-surface-container-highest text-primary focus:ring-primary">
                        <span class="font-body-sm text-on-surface-variant">Stay signed in for 30 days</span>
                    </label>

                    <button type="submit" class="w-full bg-primary hover:bg-primary-container text-on-primary py-4 font-label-bold uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">lock</span> Sign In
                    </button>

                    <a href="{{ route('register') }}" class="w-full border border-on-surface text-on-surface py-4 font-label-bold uppercase tracking-widest text-center hover:bg-surface-container-low transition-all">
                        Create Account
                    </a>
                </form>
            </div>

            <p class="font-caption text-on-surface-variant text-center">
                By signing in, you agree to our <a href="{{ url('terms') }}" class="text-primary hover:underline">Terms</a> and <a href="{{ url('privacy') }}" class="text-primary hover:underline">Privacy Policy</a>.
            </p>
        </div>
    </div>
</div>
</main>
@endsection

@section('scripts')
<script>
function togglePasswordVisibility() {
    const input = document.getElementById('password');
    const icon = document.getElementById('passIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}
</script>
@endsection
