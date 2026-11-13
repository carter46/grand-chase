<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo" style="max-width: 200px; max-height: 72px; width: auto; height: auto; display: block; margin: 0 auto; object-fit: contain; border: 0;">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
