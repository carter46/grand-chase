{{-- Logo XOR site_name; onerror falls back to site name if image URL is broken --}}
@php
    $brandHref = $brandHref ?? url('/');
    $brandClass = $brandClass ?? 'flex items-center gap-stack-sm';
    $brandImgClass = $brandImgClass ?? 'h-10 md:h-12 w-auto object-contain';
    $brandTextClass = $brandTextClass ?? 'font-label-bold text-on-surface uppercase tracking-wider text-base md:text-lg';
    $logoUrl = !empty($settings->logo) ? public_storage_url($settings->logo) : '';
    $hasLogo = $logoUrl !== '';
@endphp
<a href="{{ $brandHref }}" class="{{ $brandClass }}">
    @if ($hasLogo)
        <img
            alt="{{ $settings->site_name }} Logo"
            class="{{ $brandImgClass }}"
            src="{{ $logoUrl }}"
            onerror="this.classList.add('hidden'); var s=this.nextElementSibling; if(s){ s.classList.remove('hidden'); }"
        >
        <span class="{{ $brandTextClass }} hidden">{{ $settings->site_name }}</span>
    @else
        <span class="{{ $brandTextClass }}">{{ $settings->site_name }}</span>
    @endif
</a>
