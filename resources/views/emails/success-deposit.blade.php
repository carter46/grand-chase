{{-- blade-formatter-disable --}}
@component('mail::message')
# @if ($foramin) Transaction Alert @else Deposit Confirmation @endif

Hello {{ $foramin ? 'Administrator' : $user->name }},

@if ($foramin)
A deposit transaction has been recorded in the system.

## Transaction Details

**Customer:** {{ $user->name }}  
**Amount:** {{ $settings->currency }}{{ number_format($deposit->amount, 2) }}  
**Status:** {{ $deposit->status }}  
**Date:** {{ \Carbon\Carbon::parse($deposit->created_at)->format('F j, Y \a\t g:i A') }}

@if ($deposit->status != 'Processed')
@component('mail::button', ['url' => $settings->site_address . '/admin/dashboard'])
Process Transaction
@endcomponent
@endif

@else
@if ($deposit->status == 'Processed')
Your deposit has been successfully processed and confirmed.

## Transaction Summary

**Amount Deposited:** {{ $settings->currency }}{{ number_format($deposit->amount, 2) }}  
**Transaction Date:** {{ \Carbon\Carbon::parse($deposit->created_at)->format('F j, Y \a\t g:i A') }}  
**Status:** Confirmed  

Your account balance has been updated to reflect this deposit.

@component('mail::button', ['url' => $settings->site_address . '/dashboard'])
View Account Balance
@endcomponent

@else
Your deposit has been received and is being processed.

## Transaction Details

**Amount:** {{ $settings->currency }}{{ number_format($deposit->amount, 2) }}  
**Submitted:** {{ \Carbon\Carbon::parse($deposit->created_at)->format('F j, Y \a\t g:i A') }}  
**Status:** Pending Confirmation

For cryptocurrency deposits, blockchain confirmation typically takes 10-15 minutes. You will receive an automatic notification once your transaction is confirmed.

@endif
@endif

@if (!$foramin)
If you have any questions about this transaction, please contact our support team at {{ $settings->contact_email }}.
@endif

Best regards,  
**{{ $settings->site_name }} Team**

---

*This is an automated notification. For support inquiries, contact {{ $settings->contact_email }}*
@endcomponent
{{-- blade-formatter-disable --}}
