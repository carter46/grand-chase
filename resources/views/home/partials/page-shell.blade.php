{{-- Shared page hero for legacy public pages --}}
<section class="relative bg-inverse-surface min-h-[calc(100vh-104px)] md:min-h-[calc(100vh-131px)] flex items-center border-b border-outline-variant/10">
    <div class="max-w-[1200px] mx-auto px-4 md:px-gutter w-full py-12 md:py-16">
        <nav class="font-caption text-on-primary/50 mb-4">
            <a href="{{ url('/') }}" class="hover:text-primary">Home</a>
            <span class="mx-2">/</span>
            <span class="text-on-primary">{{ $title ?? 'Page' }}</span>
        </nav>
        <h1 class="text-hero-small text-on-primary uppercase tracking-tight">{{ $title ?? 'Page' }}</h1>
    </div>
</section>
<section class="bg-surface py-12 md:py-16">
    <div class="max-w-[1200px] mx-auto px-4 md:px-gutter">
        {{ $slot }}
    </div>
</section>
