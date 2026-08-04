@extends('layouts.guest2')
@section('title', 'Administration Access')

@section('content')
<main class="w-full min-h-screen bg-surface">
<div class="flex flex-col lg:flex-row min-h-screen w-full">
    {{-- Desktop panel --}}
    <div class="hidden lg:flex relative flex-1 bg-inverse-surface flex-col overflow-hidden">
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 32px 32px;"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-primary/20 via-transparent to-transparent pointer-events-none"></div>
        <div class="relative z-10 p-8 md:p-10">
            @include('includes.public-brand', [
                'brandTextClass' => 'font-label-bold text-on-primary uppercase tracking-wider',
                'brandImgClass' => 'h-9 w-auto object-contain',
            ])
        </div>
        <div class="relative z-10 flex-1 flex items-center justify-center p-8 md:p-12">
            <div class="max-w-md flex flex-col gap-8">
                <div class="flex flex-col gap-3">
                    <span class="font-label-bold text-primary tracking-[0.2em] uppercase text-xs">Restricted access</span>
                    <h1 class="font-headline-xl text-on-primary text-3xl md:text-[42px] leading-tight">Admin Control Center</h1>
                    <p class="font-body-md text-on-primary/70 leading-relaxed">
                        Authorized personnel only. All sign-ins are monitored for the security of {{ $settings->site_name }}.
                    </p>
                </div>
                <div class="flex flex-col gap-4">
                    @foreach ([
                        ['icon' => 'verified_user', 'label' => 'Role-based access control'],
                        ['icon' => 'monitoring', 'label' => 'Full activity auditing'],
                        ['icon' => 'lock', 'label' => 'Encrypted admin sessions'],
                    ] as $item)
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-[22px]">{{ $item['icon'] }}</span>
                        <span class="font-label-bold text-on-primary/90 text-xs uppercase tracking-wider">{{ $item['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="w-full lg:flex-1 flex items-center justify-center bg-surface p-4 sm:p-6 md:p-12">
        <div class="w-full max-w-[420px] flex flex-col gap-8">
            <div class="lg:hidden flex justify-center">
                @include('includes.public-brand', [
                    'brandImgClass' => 'h-9 w-auto object-contain',
                    'brandTextClass' => 'font-label-bold text-on-surface uppercase tracking-wider text-lg',
                ])
            </div>

            <div class="flex flex-col gap-2 text-center lg:text-left items-center lg:items-start">
                <span class="font-label-bold text-primary tracking-[0.15em] uppercase text-[11px]">Administration</span>
                <h2 class="text-headline-lg text-on-surface">Sign in to Admin</h2>
                <p class="font-body-sm text-on-surface-variant">Enter your staff credentials to continue.</p>
            </div>

            @if (Session::has('status'))
            <div class="p-4 bg-error-container border border-error text-on-error-container font-body-sm">
                {{ session('status') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="p-4 bg-error-container border border-error text-on-error-container font-body-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white border border-surface-container-highest p-6 sm:p-8 shadow-xl">
                <form method="POST" action="{{ route('adminlogin') }}" class="flex flex-col gap-5">
                    @csrf

                    <div class="flex flex-col gap-2">
                        <label class="font-label-bold text-on-surface text-[11px] uppercase tracking-wider" for="email">Email Address</label>
                        <div class="relative group">
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="username"
                                placeholder="admin@example.com"
                                class="w-full px-4 py-3.5 pr-11 bg-surface border border-surface-container-highest focus:border-primary focus:ring-0 outline-none font-body-md text-on-surface"
                            >
                            <span class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant/40 group-focus-within:text-primary text-[20px]">mail</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between items-end gap-3">
                            <label class="font-label-bold text-on-surface text-[11px] uppercase tracking-wider" for="password">Password</label>
                            <a href="{{ route('admin.forgetpassword') }}" class="font-label-md text-primary hover:underline text-[11px] shrink-0">Forgot password?</a>
                        </div>
                        <div class="relative group" x-data="{ show: false }">
                            <input
                                id="password"
                                :type="show ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full px-4 py-3.5 pr-11 bg-surface border border-surface-container-highest focus:border-primary focus:ring-0 outline-none font-body-md text-on-surface"
                            >
                            <button type="button" @click="show = !show" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant/40 hover:text-on-surface" aria-label="Toggle password">
                                <span class="material-symbols-outlined text-[20px]" x-text="show ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-1 bg-primary hover:bg-primary-container text-on-primary py-4 font-label-bold uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">admin_panel_settings</span>
                        Sign In
                    </button>
                </form>
            </div>

            <div class="flex flex-col items-center gap-3">
                <p class="font-caption text-on-surface-variant text-center flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[14px] text-primary">shield</span>
                    Encrypted staff session
                </p>
                <a href="{{ url('/') }}" class="font-label-bold text-[11px] uppercase tracking-wider text-on-surface-variant hover:text-primary transition-colors">
                    Back to website
                </a>
            </div>
        </div>
    </div>
</div>
</main>
@endsection
