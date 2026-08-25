@props([
    'title',
    'subtitle' => null,
    'badge' => null,
    'badgeClass' => 'badge-secondary',
    'meta' => [],
    'avatar' => null,
])

<div class="admin-mobile-card" x-data="{ open: false }">
    <div class="admin-mobile-card-summary" @click="open = !open">
        @isset($leading)
            <div class="admin-mobile-card-leading" @click.stop>
                {{ $leading }}
            </div>
        @endisset

        @if ($avatar)
            <img src="{{ $avatar }}" alt="" class="admin-mobile-card-avatar">
        @endif

        <div class="admin-mobile-card-info">
            <p class="admin-mobile-card-title">{{ $title }}</p>
            @if ($subtitle)
                <p class="admin-mobile-card-subtitle">{{ $subtitle }}</p>
            @endif
        </div>

        @if ($badge)
            <span class="admin-mobile-card-badge badge {{ $badgeClass }}">{{ $badge }}</span>
        @endif

        <button type="button" class="admin-mobile-card-chevron" :class="{ 'is-open': open }"
            aria-label="Toggle details">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>

    <div class="admin-mobile-card-details" x-show="open" x-cloak>
        @foreach ($meta as $row)
            <div class="admin-mobile-card-row">
                <span class="admin-mobile-card-label">{{ $row['label'] ?? '' }}</span>
                <span class="admin-mobile-card-value">{!! $row['html'] ?? e($row['value'] ?? '') !!}</span>
            </div>
        @endforeach

        @if (!$slot->isEmpty())
            <div class="admin-mobile-card-actions">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
