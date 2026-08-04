{{-- Logo XOR site_name, with onerror fallback to site name --}}
@php
    $brandHref = $brandHref ?? url('/');
    $brandClass = $brandClass ?? 'flex items-center gap-stack-sm';
    $brandImgClass = $brandImgClass ?? 'h-8 w-auto object-contain';
    $brandTextClass = $brandTextClass ?? 'font-label-bold text-on-surface uppercase tracking-wider';
    $hasLogo = !empty($settings->logo);
@endphp
<a href="{{ $brandHref }}" class="{{ $brandClass }}">
    @if ($hasLogo)
        <img
            alt="{{ $settings->site_name }} Logo"
            class="{{ $brandImgClass }}"
            src="{{ asset('storage/app/public/'.$settings->logo) }}"
            onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
        >
        <span class="{{ $brandTextClass }} hidden">{{ $settings->site_name }}</span>
    @else
        <span class="{{ $brandTextClass }}">{{ $settings->site_name }}</span>
    @endif
</a>
