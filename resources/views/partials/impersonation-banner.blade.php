{{-- Shown on customer pages only while an admin is viewing the account. $variant: 'sidebar' | 'bar' --}}
@if (\App\Support\Impersonation::active())
    @php $impVariant = $variant ?? 'bar'; @endphp
    @if ($impVariant === 'sidebar')
        <form method="POST" action="{{ route('impersonate.leave') }}" class="{{ $class ?? '' }}" style="margin:0 16px 16px;">
            @csrf
            <p style="margin:0 0 6px;font-size:11px;color:#92400e;line-height:1.3;">
                Admin view: {{ Auth::user()->name }} {{ Auth::user()->lastname }}
            </p>
            <button type="submit"
                style="display:flex;align-items:center;justify-content:center;gap:6px;width:100%;padding:9px 12px;border:0;border-radius:8px;background:#f59e0b;color:#111827;font-size:13px;font-weight:600;cursor:pointer;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                Back to admin
            </button>
        </form>
    @else
        <div class="{{ $class ?? '' }}">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;padding:8px 14px;background:#f59e0b;color:#111827;font-size:13px;position:relative;z-index:50;">
            <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                Admin view: <strong>{{ Auth::user()->name }} {{ Auth::user()->lastname }}</strong>
            </span>
            <form method="POST" action="{{ route('impersonate.leave') }}" style="margin:0;flex-shrink:0;">
                @csrf
                <button type="submit"
                    style="padding:5px 12px;border:0;border-radius:6px;background:#111827;color:#fff;font-size:12px;font-weight:600;cursor:pointer;">
                    Back to admin
                </button>
            </form>
        </div>
        </div>
    @endif
@endif
