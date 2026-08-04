<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Settings;
use App\Models\User_plans;
use App\Models\Wdmethod;
use App\Models\Withdrawal;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Auth;
use App\Mail\NewNotification;
use Illuminate\Support\Facades\Mail;
use App\Mail\WithdrawalStatus;
use App\Traits\Coinpayment;
use Carbon\Carbon;
use Session;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    use Coinpayment;
    //
    public function withdrawamount(Request $request)
    {
        
        return redirect()->route('withdrawfunds');
    }


    public function loan(Request $request)
    {
        
        return redirect()->route('dashboard')
            ->with('success', 'Action Sucessful! Please wait while we process your loan request.');
    }


    //Return withdrawals route
    public function withdrawfunds()
    {
        $paymethod = session('paymentmethod');

        if (empty($paymethod)) {
            return redirect()->route('withdrawalsdeposits')
                ->with('message', 'Please select a withdrawal method to continue.');
        }

        $checkmethod =  Wdmethod::where('name', $paymethod)->first();

       if (!$checkmethod) {
            return redirect()->route('withdrawalsdeposits')
                ->with('message', 'Selected withdrawal method is no longer available. Please choose another option.');
        }

        if ($checkmethod->defaultpay == "yes") {
            $default = true;
        } else {
            $default = false;
        }

        if ($checkmethod->methodtype == "crypto") {
            $methodtype = 'crypto';
        } else {
            $methodtype = 'currency';
        }

        return view('user.withdraw', [
            'title' => 'Complete Withdrawal Request',
            'payment_mode' => $paymethod,
            'default' => $default,
            'methodtype' => $methodtype,
        ]);
    }

    public function getotp()
    {
        
    if(auth::user()->transferaction==1){
       return back();
    }
        $code = $this->RandomStringGenerator(6);

        $user = Auth::user();
        User::where('id', $user->id)->update([
            'withdrawotp' => $code,
        ]);
        $settings = Settings::where('id', '1')->first();

        $message = "You recently initiated  transfer from your $settings->site_name  $user->accounttype via our online banking channel.\n
        If this was legitimate activity from you and were expecting this email, consider using the code below to complete your transaction:\n
                                                 $code
           
        \nIf you do not use $settings->site_name or did not attempted to carry out a transaction via your $settings->site_name internet banking channel, please ignore this email or contact support if you have questions.";
        $subject = "Comfirm transction";

        try {
            Mail::to($user->email)->send(new NewNotification($message, $subject, $user->name));
        } catch (\Throwable $exception) {
            Log::error('Failed to send withdrawal OTP email.', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $exception->getMessage(),
            ]);
        }

        return redirect()->route('otpview');

         
    }
    
    
    function otpview(){
         return view('user.otp');
    }


    public function completewithdrawal(Request $request)
    {

        if (Auth::user()->sendotpemail == "Yes") {
            if ($request->otpcode != Auth::user()->withdrawotp) {
                return redirect()->back()->with('message', 'OTP is incorrect, please recheck the code');
            }
        }

        $settings = Settings::where('id', '1')->first();
        if ($settings->enable_kyc == "yes") {
            if (Auth::user()->account_verify != "Verified") {
                return redirect()->back()->with('message', 'Your account must be verified before you can make withdrawal.');
            }
        }

        $method = Wdmethod::where('name', $request->method)->first();
        if ($method->charges_type == 'percentage') {
            $charges = $request['amount'] * $method->charges_amount / 100;
        } else {
            $charges = $method->charges_amount;
        }

        $to_withdraw = $request['amount'] + $charges;
        //return if amount is lesser than method minimum withdrawal amount

        if (Auth::user()->account_bal < $to_withdraw) {
            return redirect()->back()
                ->with('message', 'Sorry, your account balance is insufficient for this request.');
        }

        if ($request['amount'] < $method->minimum) {
            return redirect()->back()
                ->with("message", "Sorry, The minimum amount you can withdraw is $settings->currency$method->minimum, please try another payment method.");
        }

        //get user last investment package
        User_plans::where('user', Auth::user()->id)
            ->where('active', 'yes')
            ->orderBy('activated_at', 'asc')->first();

        //get user
        $user = User::where('id', Auth::user()->id)->first();

        if ($request->method == 'Bitcoin') {
            if (empty($user->btc_address)) {
                return redirect()->route('profile')
                    ->with('message', 'Please Setup your Bitcoin Wallet Address');
            }
            $coin = "BTC";
            $wallet = $user->btc_address;
        } elseif ($request->method  == 'Ethereum') {
            if (empty($user->eth_address)) {
                return redirect()->route('profile')
                    ->with('message', 'Please Setup your Ethereum Wallet Address');
            }
            $coin = "ETH";
            $wallet = $user->eth_address;
        } elseif ($request->method  == 'Litecoin') {
            if (empty($user->ltc_address)) {
                return redirect()->route('profile')
                    ->with('message', 'Please Setup your Litecoin Wallet Address');
            }
            $coin = "LTC";
            $wallet = $user->ltc_address;
        } elseif ($request->method  == 'USDT') {
            if (empty($user->usdt_address)) {
                return redirect()->route('profile')
                    ->with('message', 'Please Setup your USDT Wallet Address');
            }
            $coin = "USDT.TRC20";
            $wallet = $user->usdt_address;
        } elseif ($request->method  == 'Bank Transfer') {
            if (empty($user->account_name) or empty($user->bank_name) or empty($user->account_number)) {
                return redirect()->route('profile')
                    ->with('message', 'Please Setup your Bank Account Details');
            }
        }

        $amount = $request['amount'];
        $ui = $user->id;


   if ($settings->withdrawal_option == "auto" and ($request->method == 'Bitcoin' or $request->method  == 'Litecoin' or $request->method  == 'Ethereum' or $request->method == 'USDT')) {
            return $this->cpwithdraw($amount, $coin, $wallet, $ui, $to_withdraw);
        }
    
            //debit user
            User::where('id', $user->id)->update([
                'account_bal' => $user->account_bal - $to_withdraw,
                'withdrawotp' => NULL,
            ]);

        //save withdrawal info
        $dp = new Withdrawal();
        $dp->amount = $amount;
        $dp->to_deduct = $to_withdraw;
        $dp->payment_mode = $request->method;
        $dp->status = 'Pending';
        $dp->paydetails = $request->details;
        $dp->user = $user->id;
        $dp->save();

        // Create notification
        NotificationHelper::create(
            $user,
            'Your withdrawal request for ' . $settings->currency . $amount . ' via ' . $request->method . ' has been submitted and is pending approval.',
            'Withdrawal Request Submitted',
            'info',
            'alert-circle',
            route('withdrawalsdeposits')
        );

        // send mail to admin
        Mail::to($settings->contact_email)->send(new WithdrawalStatus($dp, $user, 'Withdrawal Request', true));

        //send notification to user
        Mail::to($user->email)->send(new WithdrawalStatus($dp, $user, 'Successful Withdrawal Request'));

        return redirect()->route('withdrawalsdeposits')
            ->with('success', 'Action Sucessful! Please wait while we process your request.');
    }

    public function localtransfer(Request $request)
    {
        
        $user = User::where('id', Auth::user()->id)->first();
        
        $settings = Settings::where('id', '1')->first();
        //check if user status is active
        if($user->account_status != 'active'){
            return redirect()->back()
                ->with("message", "Sorry, your account is dormant. Contact support on $settings->contact_email for more details.");
        }
            //check if transaction pin match
          if($user->pin != $request->pin){
            return redirect()->back()
          ->with("message", "Sorry, incorrect transaction pin");
          }
        if (Auth::user()->account_bal < $request['amount']) {
            return redirect()->back()
                ->with('message', 'Sorry, your account balance is insufficient for this request.');
        }
        
        
         $subtxn =substr(strtoupper($settings->site_name),0,4);
              $codetxn1 = $this->RandomStringGenerator(8);
              $codetxn2 = substr(strtoupper(Carbon::now()),0,4);
              //grabing evereything submitted by user and put it in session
                     $data['bankname'] = $request->bankname;
                     $data['amount'] = $request->amount;
                     $data['accountname'] = $request->accountname;
                     $data['accountnumber'] = $request->accountnumber;
                     $data['Accounttype'] = $request->Accounttype;
                     $data['Description'] = $request->Description;
                     $data['user'] = $user->id;
                     $data['payment_mode'] = "Domestic Transfer";
                     $data['date'] = Carbon::now();
                     $data['txn_id'] ="$subtxn/$codetxn1-$codetxn2";
                     Session::put('data', $data);
       
      //assignin/turn off transanction number 
     if(Auth::user()->transferaction=='1'){
        User::where('id', $user->id)->update([
            'transferaction' => 0,
            
        ]);
     }
 
 
       
        // Session::put('data', $data);
        
        if($settings->otp ==1){
            return redirect()->route('getotp','data');

        }
       
      
        sleep(3);
        return redirect()->route('previewtransfer','data');

    }



// international transfer
    public function internationaltransfer(Request $request)
{
    // Basic validation for all payment methods
    $this->validate($request, [
        'amount' => 'required|numeric',
        'withdrawMethod' => 'required',
        'pin' => 'required',
    ]);

    $user = User::where('id', Auth::user()->id)->first();
    $settings = Settings::where('id', '1')->first();
    
    // Check if user status is active
    if ($user->account_status != 'active') {
        return redirect()->back()
            ->with("message", "Sorry, your account is dormant. Contact support on $settings->contact_email for more details.");
    }
    
    // Check if transaction pin match
    if ($user->pin != $request->pin) {
        return redirect()->back()
            ->with("message", "Sorry, incorrect transaction pin");
    }

    if ($request->amount < 1) {
        return redirect()->back()
            ->with("message", "Sorry, The minimum amount you can transfer is $settings->currency 1, please Enter correct amount.");
    }
    
    if (Auth::user()->account_bal < $request->amount) {
        return redirect()->back()
            ->with('message', 'Sorry, your account balance is insufficient for this request.');
    }

    // Assign/turn off transaction number 
    if (Auth::user()->transferaction == '1') {
        User::where('id', $user->id)->update([
            'transferaction' => 0,
        ]);
    }

    $subtxn = substr(strtoupper($settings->site_name), 0, 4);
    $codetxn1 = $this->RandomStringGenerator(8);
    $codetxn2 = substr(strtoupper(Carbon::now()), 0, 4);
    
    // Create base data array that will be common for all methods
    $data = [
        'amount' => $request->amount,
        'user' => $user->id,
        'date' => Carbon::now(),
        'txn_id' => "$subtxn/$codetxn1-$codetxn2",
        'Description' => $request->Description,
    ];
    
    // Add method-specific data based on the withdrawal method
    switch ($request->withdrawMethod) {
        case 'Wire Transfer':
            $this->validate($request, [
                'accountname' => 'required',
                'accountnumber' => 'required',
                'bankname' => 'required',
                'bankaddress' => 'required',
                'swiftcode' => 'required',
                'iban' => 'required',
                'country' => 'required',
                'Accounttype' => 'required',
            ]);
            
            $data['payment_mode'] = 'International Wire Transfer';
            $data['accountname'] = $request->accountname;
            $data['accountnumber'] = $request->accountnumber;
            $data['bankname'] = $request->bankname;
            $data['bankaddress'] = $request->bankaddress;
            $data['Accounttype'] = $request->Accounttype;
            $data['country'] = $request->country;
            $data['swiftcode'] = $request->swiftcode;
            $data['iban'] = $request->iban;
            break;
            
        case 'Cryptocurrency':
            $this->validate($request, [
                'cryptoCurrency' => 'required',
                'cryptoNetwork' => 'required',
                'walletAddress' => 'required',
            ]);
            
            $data['payment_mode'] = 'Cryptocurrency';
            $data['crypto_currency'] = $request->cryptoCurrency;
            $data['crypto_network'] = $request->cryptoNetwork;
            $data['wallet_address'] = $request->walletAddress;
            break;
            
        case 'PayPal':
            $this->validate($request, [
                'paypalEmail' => 'required|email',
            ]);
            
            $data['payment_mode'] = 'PayPal';
            $data['paypal_email'] = $request->paypalEmail;
            break;
            
        case 'Wise Transfer':
            $this->validate($request, [
                'wiseFullName' => 'required',
                'wiseEmail' => 'required|email',
                'wiseCountry' => 'required',
            ]);
            
            $data['payment_mode'] = 'Wise Transfer';
            $data['wise_fullname'] = $request->wiseFullName;
            $data['wise_email'] = $request->wiseEmail;
            $data['wise_country'] = $request->wiseCountry;
            break;
            
        case 'Skrill':
            $this->validate($request, [
                'skrillEmail' => 'required|email',
                'skrillFullName' => 'required',
            ]);
            
            $data['payment_mode'] = 'Skrill';
            $data['skrill_email'] = $request->skrillEmail;
            $data['skrill_fullname'] = $request->skrillFullName;
            break;
            
        case 'Venmo':
            $this->validate($request, [
                'venmoUsername' => 'required',
                'venmoPhone' => 'required',
            ]);
            
            $data['payment_mode'] = 'Venmo';
            $data['venmo_username'] = $request->venmoUsername;
            $data['venmo_phone'] = $request->venmoPhone;
            break;
            
        case 'Zelle':
            $this->validate($request, [
                'zelleEmail' => 'required|email',
                'zellePhone' => 'required',
                'zelleName' => 'required',
            ]);
            
            $data['payment_mode'] = 'Zelle';
            $data['zelle_email'] = $request->zelleEmail;
            $data['zelle_phone'] = $request->zellePhone;
            $data['zelle_name'] = $request->zelleName;
            break;
            
        case 'Cash App':
            $this->validate($request, [
                'cashAppTag' => 'required',
                'cashAppFullName' => 'required',
            ]);
            
            $data['payment_mode'] = 'Cash App';
            $data['cash_app_tag'] = $request->cashAppTag;
            $data['cash_app_fullname'] = $request->cashAppFullName;
            break;
            
        case 'Revolut':
            $this->validate($request, [
                'revolutFullName' => 'required',
                'revolutEmail' => 'required|email',
                'revolutPhone' => 'required',
            ]);
            
            $data['payment_mode'] = 'Revolut';
            $data['revolut_fullname'] = $request->revolutFullName;
            $data['revolut_email'] = $request->revolutEmail;
            $data['revolut_phone'] = $request->revolutPhone;
            break;
            
        case 'Alipay':
            $this->validate($request, [
                'alipayId' => 'required',
                'alipayFullName' => 'required',
            ]);
            
            $data['payment_mode'] = 'Alipay';
            $data['alipay_id'] = $request->alipayId;
            $data['alipay_fullname'] = $request->alipayFullName;
            break;
            
        case 'WeChat Pay':
            $this->validate($request, [
                'wechatId' => 'required',
                'wechatName' => 'required',
            ]);
            
            $data['payment_mode'] = 'WeChat Pay';
            $data['wechat_id'] = $request->wechatId;
            $data['wechat_name'] = $request->wechatName;
            break;
            
        default:
            return redirect()->back()
                ->with('message', 'Please select a valid withdrawal method.');
    }
    
    // Store data in session
    Session::put('data', $data);
    
    // Process verification steps based on settings
    if ($settings->code1status == 1) {
        return redirect()->route('code1verification');
    }
    
    if ($settings->code2status == 1) {
        return redirect()->route('verificationcode2');
    }
    
    if ($settings->code1status == 3) {
        return redirect()->route('verification3code');
    }
    
    if ($settings->otp == 1) {
        return redirect()->route('getotp');
    }

    return redirect()->route('previewtransfer');
}

//codecomfirm 
    function codecomfirm(Request $request){
        
        $user = User::where('id', Auth::user()->id)->first();
        $settings = Settings::where('id', '1')->first();
        $data = Session::get('data');
        // dd(Auth::user()->code1, $request->code);
        //code1 request
     if($request->code1){
        $this->validate($request, [
            
            'code1' => 'required',
           
        ]);
         if (Auth::user()->code1 != $request->code1){
         return redirect()->back()
                ->with("message", "Sorry, Invalid $settings->code1 code!!! contact support on $settings->contact_email for the appropriate $settings->code1 for this transaction  ");
         }
         
         	if($settings->code2status == 1){
         	    sleep(3);
     	return redirect()->route('verificationcode2');
       }
       
       	if($settings->code3status == 1){
       	    sleep(3);
     	return redirect()->route('verification3code');
       }
         
         if($settings->otp ==1){
             sleep(3);
        return redirect()->route('getotp');

    }
    
     }


// checkin if code2

if($request->code2){
       $this->validate($request, [
            
            'code2' => 'required',
           
        ]);
       
         if (Auth::user()->code2 != $request->code2){
         return redirect()->back()
                ->with("message", "Sorry, Invalid $settings->code2 code!!! contact support on $settings->contact_email for the appropriate $settings->code2 for this transaction  ");
         }
         
         
       
       	if($settings->code3status == 1){
       	    sleep(3);
     	return redirect()->route('verification3code');
       }
         
         if($settings->otp ==1){
             sleep(3);
        return redirect()->route('getotp');

    }
    
    
}


if($request->code3){
    
    $this->validate($request, [
            
            'code3' => 'required',
           
        ]);
       
         if (Auth::user()->code3 != $request->code3){
         return redirect()->back()
                ->with("message", "Sorry, Invalid $settings->code3 code!!! contact support on $settings->contact_email for the appropriate $settings->code3 for this transaction  ");
         }
         
         
       
       
         if($settings->otp ==1){
             sleep(3);
        return redirect()->route('getotp');

    }
    
 }


     //otp request
    
    if($request->otp){
        
       $this->validate($request, [
            
            'otp' => 'required',
           
        ]);
       
         if (Auth::user()->withdrawotp != $request->otp){
             
             
         return redirect()->back()
                ->with("message", "Sorry, Invalid OTP code!!!  ");
                
         }
         
         
         
     }

    sleep(3);
    
     return redirect()->route('previewtransfer');

    }


    //  International preview international transfer

    public function previewtransfer(Request $request)
{
    // If an ID is provided in the request, we're viewing an existing transaction
    if ($request->has('id')) {
        $dp = Withdrawal::where('id', $request->id)->where('user', Auth::user()->id)->first();
        
        if (!$dp) {
            return redirect()->route('withdrawalsdeposits')
                ->with('message', 'Transaction not found or access denied.');
        }
        
        $settings = Settings::where('id', '1')->first();
        $code = $dp->txn_id;
        
        return view('user.preview', compact('settings', 'dp', 'code'));
    }

    // Original code for processing a new transaction from session
    ///catch session
    $data = Session::get('data');
    $settings = Settings::where('id', '1')->first();

    $user = User::where('id', Auth::user()->id)->first();
    $balance = $user->account_bal - $data['amount'];
    $to_withdraw = $data['amount'];
    
    
    if ($user->account_bal < $data['amount']) {
        return redirect()->back()
            ->with("message", "Sorry, Your balance is low for this transaction.");
    }
    
    // Debit user regardless of payment method
    User::where('id', $user->id)->update([
        'account_bal' => $user->account_bal - $data['amount'],
        'withdrawotp' => NULL,
    ]);
    
    // Create a new withdrawal record
    $dp = new Withdrawal();
    $dp->amount = $data['amount'];
    $dp->to_deduct = $to_withdraw;
    $dp->payment_mode = $data['payment_mode'];
    $dp->status = 'Pending';
    $dp->type = 'Debit';
    $dp->date = Carbon::now();
    $dp->txn_id = $data['txn_id'];
    $dp->user = $user->id;
    $dp->bal = $balance;
    $dp->Description = isset($data['Description']) ? $data['Description'] : '';
    
    // Add method-specific details to the withdrawal record
    switch ($data['payment_mode']) {
        case 'Domestic Transfer':
            $dp->accountname = $data['accountname'];
            $dp->accountnumber = $data['accountnumber'];
            $dp->bankname = $data['bankname'];
            $dp->Accounttype = $data['Accounttype'];
            break;
            
        case 'International Wire Transfer':
            $dp->accountname = $data['accountname'];
            $dp->accountnumber = $data['accountnumber'];
            $dp->bankname = $data['bankname'];
            $dp->Accounttype = $data['Accounttype'];
            $dp->bankaddress = $data['bankaddress'];
            $dp->country = $data['country'];
            $dp->swiftcode = $data['swiftcode'];
            $dp->iban = $data['iban'];
            break;
            
        case 'Cryptocurrency':
            $dp->crypto_currency = $data['crypto_currency'];
            $dp->crypto_network = $data['crypto_network'];
            $dp->wallet_address = $data['wallet_address'];
            break;
            
        case 'PayPal':
            $dp->paypal_email = $data['paypal_email'];
            break;
            
        case 'Wise Transfer':
            $dp->wise_fullname = $data['wise_fullname'];
            $dp->wise_email = $data['wise_email'];
            $dp->wise_country = $data['wise_country'];
            break;
            
        case 'Skrill':
            $dp->skrill_email = $data['skrill_email'];
            $dp->skrill_fullname = $data['skrill_fullname'];
            break;
            
        case 'Venmo':
            $dp->venmo_username = $data['venmo_username'];
            $dp->venmo_phone = $data['venmo_phone'];
            break;
            
        case 'Zelle':
            $dp->zelle_email = $data['zelle_email'];
            $dp->zelle_phone = $data['zelle_phone'];
            $dp->zelle_name = $data['zelle_name'];
            break;
            
        case 'Cash App':
            $dp->cash_app_tag = $data['cash_app_tag'];
            $dp->cash_app_fullname = $data['cash_app_fullname'];
            break;
            
        case 'Revolut':
            $dp->revolut_fullname = $data['revolut_fullname'];
            $dp->revolut_email = $data['revolut_email'];
            $dp->revolut_phone = $data['revolut_phone'];
            break;
            
        case 'Alipay':
            $dp->alipay_id = $data['alipay_id'];
            $dp->alipay_fullname = $data['alipay_fullname'];
            break;
            
        case 'WeChat Pay':
            $dp->wechat_id = $data['wechat_id'];
            $dp->wechat_name = $data['wechat_name'];
            break;
    }
    
    $dp->save();
    
    $code = $dp->txn_id;

    // Send email notifications
    Mail::to($settings->contact_email)->send(new WithdrawalStatus($dp, $user, 'Transfer Request', true));
    Mail::to($user->email)->send(new WithdrawalStatus($dp, $user, 'Successful Transfer Request'));

    // Send SMS notification if enabled
    $date = Carbon::parse($dp->created_at)->toDayDateTimeString();
    if ($settings->sms == '1') {
        $this->sendTransferSMS($user, $dp, $settings, $date);
    }
    
    // Turn on transfer action
    User::where('id', $user->id)->update([
        'transferaction' => 1,
    ]);
    
    sleep(2);
    
    return view('user.preview', compact('settings', 'dp', 'code'));
}

// Helper method to send SMS notifications
private function sendTransferSMS($user, $dp, $settings, $date)
{
    $receiverNumber = $user->phone;
    
    // Customize message based on payment method
    $paymentDetails = "";
    
    switch ($dp->payment_mode) {
        case 'International Wire Transfer':
            $paymentDetails = "Account Number: $dp->accountnumber\nAccount Name: $dp->accountname\nBank Name: $dp->bankname";
            break;
        case 'Cryptocurrency':
            $paymentDetails = "Currency: $dp->crypto_currency\nNetwork: $dp->crypto_network\nWallet: $dp->wallet_address";
            break;
        case 'PayPal':
            $paymentDetails = "PayPal Email: $dp->paypal_email";
            break;
        case 'Wise Transfer':
            $paymentDetails = "Recipient: $dp->wise_fullname\nEmail: $dp->wise_email";
            break;
        case 'Skrill':
            $paymentDetails = "Recipient: $dp->skrill_fullname\nEmail: $dp->skrill_email";
            break;
        case 'Venmo':
            $paymentDetails = "Username: $dp->venmo_username";
            break;
        case 'Zelle':
            $paymentDetails = "Recipient: $dp->zelle_name\nEmail: $dp->zelle_email";
            break;
        case 'Cash App':
            $paymentDetails = "$Cashtag: $dp->cash_app_tag\nRecipient: $dp->cash_app_fullname";
            break;
        default:
            $paymentDetails = "Payment Method: $dp->payment_mode";
    }
    
    $message = "Your transfer request of $settings->currency$dp->amount via $dp->payment_mode has been confirmed and is being processed." .
        "\nDetails of the transaction are shown below;" .
        "\n$paymentDetails" .
        "\nDescription: $dp->Description" .
        "\nTotal Amount: $settings->currency$dp->amount" .
        "\nDate: $date" .
        "\nAvailable Balance: $settings->currency$dp->bal";
    
    try {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_TOKEN");
        $twilio_number = getenv("TWILIO_FROM");

        $client = new Client($account_sid, $auth_token);
        $client->messages->create($receiverNumber, [
            'from' => $twilio_number, 
            'body' => $message
        ]);
    } catch (Exception $e) {
        // Handle exception silently
    }
}

/**
 * Export transactions to PDF
 * 
 * @param Request $request
 * @return \Illuminate\Http\Response
 */
public function exportTransactions(Request $request)
{
    try {
        // Handle the test request
        if ($request->has('test') && $request->input('test') === true) {
            return response()->json(['success' => true, 'message' => 'Export endpoint is working!']);
        }
        
        // Validate request
        $request->validate([
            'exportType' => 'required|in:pdf',
            'exportAs' => 'required|in:download,email,view',
            'startDate' => 'nullable|date',
            'endDate' => 'nullable|date',
            'dateFrom' => 'nullable|date',
            'dateTo' => 'nullable|date', 
            'status' => 'nullable|string',
            'statementStyle' => 'nullable|string|in:modern,classic',
        ]);

        $user = Auth::user();
        $settings = Settings::where('id', '1')->first();
        
        // Query withdrawals
        $withdrawals = Withdrawal::where('user', $user->id);
        
        // Apply filters if provided - support both parameter naming conventions
        if ($request->filled('dateFrom') || $request->filled('startDate')) {
            $date = $request->filled('dateFrom') ? $request->dateFrom : $request->startDate;
            $withdrawals->whereDate('created_at', '>=', $date);
        }
        
        if ($request->filled('dateTo') || $request->filled('endDate')) {
            $date = $request->filled('dateTo') ? $request->dateTo : $request->endDate;
            $withdrawals->whereDate('created_at', '<=', $date);
        }
        
        if ($request->filled('status')) {
            $withdrawals->where('status', $request->status);
        }
        
        // Order results
        $withdrawals->orderBy('created_at', $request->input('orderBy', 'desc'));
        
        // Get final results
        $transactions = $withdrawals->get();
        
        // Calculate statement summary information
        $statementData = $this->calculateStatementData($user, $transactions, $request);
        
        // For view or download options, return the view
        // Client-side JavaScript will handle PDF generation for download
        if ($request->exportAs == 'view' || $request->exportAs == 'download') {
            return view('pdfs.statement', $statementData);
        }
        
        // For email option
        if ($request->exportAs == 'email') {
            try {
                // Generate HTML content
                $htmlContent = view('pdfs.statement', $statementData)->render();
                
                Mail::send('emails.export', [
                    'user' => $user,
                    'exportType' => 'PDF Statement',
                ], function ($message) use ($user, $htmlContent) {
                    $message->to($user->email)
                        ->subject('Your Account Statement')
                        ->attachData($htmlContent, 'account_statement.pdf', [
                            'mime' => 'application/pdf',
                        ]);
                });
                
                return response()->json([
                    'success' => true,
                    'message' => 'Your account statement has been sent to your email.'
                ]);
            } catch (\Exception $e) {
                \Log::error('Error sending email: ' . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send email. Please try again later or download directly.'
                ], 500);
            }
        }
    } catch (\Exception $e) {
        \Log::error('Error in exportTransactions: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while processing your request.',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Calculate statement data including summary figures
 * 
 * @param User $user
 * @param Collection $transactions
 * @param Request $request
 * @return array
 */
private function calculateStatementData($user, $transactions, $request)
{
    $settings = Settings::where('id', '1')->first();
    
    // Calculate totals
    $totalCredits = $transactions->where('type', 'Credit')->sum('amount');
    $totalDebits = $transactions->where('type', '!=', 'Credit')->sum('amount');
    
    // Get statement period
    $dateFrom = $request->filled('dateFrom') ? Carbon::parse($request->dateFrom) : null;
    $dateTo = $request->filled('dateTo') ? Carbon::parse($request->dateTo) : null;
    
    // If dates are provided, calculate opening balance as of dateFrom
    $openingBalance = 0;
    if ($dateFrom) {
        // Get all transactions before dateFrom
        $previousTransactions = Withdrawal::where('user', $user->id)
            ->whereDate('created_at', '<', $dateFrom)
            ->get();
        
        // Calculate opening balance
        $previousCredits = $previousTransactions->where('type', 'Credit')->sum('amount');
        $previousDebits = $previousTransactions->where('type', '!=', 'Credit')->sum('amount');
        $openingBalance = $previousCredits - $previousDebits;
    }
    
    // Calculate closing balance
    $closingBalance = $openingBalance + $totalCredits - $totalDebits;
    
    return [
        'user' => $user,
        'settings' => $settings,
        'transactions' => $transactions,
        'totalCredits' => $totalCredits,
        'totalDebits' => $totalDebits,
        'openingBalance' => $openingBalance,
        'closingBalance' => $closingBalance,
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'statementStyle' => $request->input('statementStyle', 'modern'),
    ];
}

    // for front end content management
    function RandomStringGenerator($n)
    {
        $generated_string = "";
        $domain = "ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $len = strlen($domain);
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, $len - 1);
            $generated_string = $generated_string . $domain[$index];
        }
        // Return the random generated string 
        return $generated_string;
    }
}