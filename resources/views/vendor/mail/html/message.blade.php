@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
@php
    $mailLogo = public_storage_url($settings->logo ?? null);
@endphp
@if ($mailLogo)
<img src="{{ $mailLogo }}" class="logo" alt="{{ config('app.name') }}" style="max-width: 200px; max-height: 72px; width: auto; height: auto; display: block; margin: 0 auto; object-fit: contain; border: 0;">
@else
{{ config('app.name') }}
@endif
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
