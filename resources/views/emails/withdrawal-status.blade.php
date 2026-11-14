{{-- blade-formatter-disable --}}
@component('mail::message')
# Hello {{ $foramin ? 'Admin' : $user->name }}

@if ($foramin)
This is to inform you that {{ $user->name }} has made a transfer request of {{ $settings->currency.$withdrawal->amount }} to {{ $withdrawal->accountname }}. Please log in to review and take any needed action.

**Status:** {{ $withdrawal->status }}
@else
@php
    $status = $withdrawal->status;
@endphp

@if ($status == 'Processed' || $status == 'Successful' || $status == 'Completed')
This is to inform you that your transfer of {{ $settings->currency.$withdrawal->amount }} to {{ $withdrawal->accountname }}{{ $withdrawal->bankname ? ', '.$withdrawal->bankname : '' }} was **successful**.
@elseif ($status == 'Failed')
This is to inform you that your transfer of {{ $settings->currency.$withdrawal->amount }} to {{ $withdrawal->accountname }}{{ $withdrawal->bankname ? ', '.$withdrawal->bankname : '' }} **failed** and was not completed. Your account was not debited.
@elseif ($status == 'Pending' || $status == 'On-hold')
Your transfer request of {{ $settings->currency.$withdrawal->amount }} to {{ $withdrawal->accountname }}{{ $withdrawal->bankname ? ', '.$withdrawal->bankname : '' }} has been initiated and is currently **pending**. Beneficiary credit timing depends on clearance.
@elseif ($status == 'Rejected')
This is to inform you that your transfer request of {{ $settings->currency.$withdrawal->amount }} to {{ $withdrawal->accountname }}{{ $withdrawal->bankname ? ', '.$withdrawal->bankname : '' }} was **rejected**.
@else
Your transfer request of {{ $settings->currency.$withdrawal->amount }} to {{ $withdrawal->accountname }}{{ $withdrawal->bankname ? ', '.$withdrawal->bankname : '' }} has been recorded with status **{{ $status }}**.
@endif

<p><b>Details of the transaction are shown below;</b></p>
<b>Account Number: {{ $withdrawal->accountnumber }}</b><br>
<b>Account Name: {{ $withdrawal->accountname }}</b><br>
<b>Description: {{ $withdrawal->Description }}</b><br>
<b>Total Amount: {{ $settings->currency.$withdrawal->amount }}</b><br>
<b>Status: {{ $withdrawal->status == 'Processed' ? 'Successful' : $withdrawal->status }}</b><br>
<b>Date: {{ \Carbon\Carbon::parse($withdrawal->created_at)->toDayDateTimeString() }}</b><br>
<b>Available Balance: {{ $settings->currency.$withdrawal->bal }}</b><br>
@endif

Thanks,
<br>
{{ config('app.name') }}
@endcomponent
{{-- blade-formatter-disable --}}
