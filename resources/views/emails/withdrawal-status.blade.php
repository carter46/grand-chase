{{-- blade-formatter-disable --}}
@component('mail::message')
# Hello {{$foramin  ? 'Admin' : $user->name}}
@if ($foramin)
This is to inform you that {{$user->name}} have made a transfer request of {{$settings->currency.$withdrawal->amount}} to {{$withdrawal->accountname}}, kindly login to your bank website  to review and take neccesary action.
@else
@if ($withdrawal->status == 'Processed')
This is to inform you that your transfer request of {{$settings->currency.$withdrawal->amount}} to {{$withdrawal->accountname}}, {{$withdrawal->bankname}} has been approved.
@else
 Your transfer request of {{$settings->currency.$withdrawal->amount}} to {{$withdrawal->accountname}}, {{$withdrawal->bankname}} has been confirmed and beneficiary is expected to be credited within @if($withdrawal->payment_mode =='International Wire Transfer')
      5 days.
      @else
      an hour
     @endif
      

<p><b>Details of the transaction are shown below;</b></p>
             <b>Account Number: {{$withdrawal->accountnumber}}</b><br>
            <b>Account Name:{{$withdrawal->accountname}} </b><br>
             <b>Description: {{$withdrawal->Description}}</b><br>
             <b>Total Amount: {{$settings->currency.$withdrawal->amount}}</b><br>
             <b>Date: {{ \Carbon\Carbon::parse($withdrawal->created_at)->toDayDateTimeString() }}</b><br>
             <b>Available Balance: {{$settings->currency.$withdrawal->bal}}</b><br>
        
@endif    
@endif
Thanks,
<br>
{{ config('app.name') }}
@endcomponent
{{-- blade-formatter-disable --}}
