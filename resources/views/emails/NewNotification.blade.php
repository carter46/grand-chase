{{-- blade-formatter-disable --}}
@component('mail::message')
# {{ $salutaion ? $salutaion : "Hello" }} {{ $recipient}},

@if ($attachment != null)
    @php $embedPath = public_storage_path($attachment); @endphp
    @if ($embedPath)
        <img src="{{ $message->embed($embedPath) }}">
    @elseif (preg_match('#^https?://#i', (string) $attachment))
        <img src="{{ $attachment }}">
    @endif
@endif
{!! $body !!}

Thanks,
{{ config('app.name') }}

@endcomponent
{{-- blade-formatter-disable --}}
