<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewNotification;
use App\Models\Mt4Details;
use App\Models\Settings;
use App\Models\User;
use App\Traits\PingServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubscriptionController extends Controller
{
    use PingServer;

    public function myTradingSettings()
    {
        $settings = Settings::find(1);
        $account = $this->fetctApi('/account-profile');

        $response = $this->fetctApi('/master-account');

        $acout = $this->fetctApi('/trading-accounts');

        $settings = $this->fetctApi('/settings');
        $amountPerSlot = $settings['data']['amount_per_slot'];

        return view('admin.subscription.trading-settings', [
            'title' => 'Trading Settings',
            'accounts' => $response['data'],
            'myaccount' => $account['data'],
            'data' => $acout['data'],
            'amountPerSlot' => $amountPerSlot
        ]);
    }

    public function createCopyMasterAccount(Request $request)
    {
        $response = $this->fetctApi('/create-copytrade-account', [
            'login' => $request->login,
            'password' => $request->password,
            'serverName' => $request->serverName,
            'name' => $request->name,
            'leverage' => $request->leverage,
            'account_type' => $request->acntype,
            'baseCurrency' => $request->currency ? $request->currency : 'USD',
        ], 'POST');

        if ($response->failed()) {
            return redirect()->back()->with('message', $response['message']);
        }
        return redirect()->back()->with('success', $response['message']);
    }


    public function updateStrategy(Request $request)
    {
        if ($request->has('fixedRisk')) {
            $modeCompliment = $request->fixedRisk;
        } elseif ($request->has('fixedVolume')) {
            $modeCompliment = $request->fixedVolume;
        } elseif ($request->has('expression')) {
            $modeCompliment = $request->expression;
        } else {
            $modeCompliment = '';
        }

        $response = $this->fetctApi('/update-strategy', [
            'mode' => $request->trademode,
            'strategy_name' => $request->name,
            'description' => $request->desc,
            'modecompliment' => $modeCompliment,
        ], 'POST');

        if ($response->failed()) {
            return redirect()->back()->with('message', $response['message']);
        }
        return redirect()->back()->with('success', $response['message']);
    }


    public function deleteMasterAccount($id)
    {
        $response = $this->fetctApi('/delete-master-account' . '/' . $id);
        if ($response->failed()) {
            return redirect()->back()->with('message', $response['message']);
        }
        return redirect()->back()->with('success', $response['message']);
    }


    public function renewAccount(Request $request)
    {
        $response = $this->fetctApi('/renew-master-account', [
            'account' => $request->account_id,
        ], 'POST');
        if ($response->failed()) {
            return redirect()->back()->with('message', $response['message']);
        }
        return redirect()->back()->with('success', $response['message']);
    }


    public function delsub($id)
    {
        Mt4Details::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Subscription Sucessfully Deleted');
    }

    /**
     * Confirm / activate a local MT4 subscription (mt4_details).
     */
    public function confirmsub($id)
    {
        $sub = Mt4Details::findOrFail($id);
        $user = User::where('id', $sub->client_id)->first();

        if ($sub->duration == 'Monthly') {
            $end_at = now()->addMonths(1);
        } elseif ($sub->duration == 'Quaterly') {
            $end_at = now()->addMonths(4);
        } elseif ($sub->duration == 'Yearly') {
            $end_at = now()->addYears(1);
        } else {
            $end_at = now()->addMonths(1);
        }
        $remindAt = (clone $end_at)->subDays(10);

        $sub->start_date = now();
        $sub->end_date = $end_at;
        $sub->reminded_at = $remindAt;
        $sub->status = 'Active';
        $sub->save();

        if ($user) {
            $settings = Settings::where('id', '=', '1')->first();
            $site = $settings->site_name ?? config('app.name');
            $message = "$user->name, This is to inform you that your trading account management request has been reviewed and processed. Thank you for trusting $site";
            Mail::to($user->email)->send(new NewNotification($message, 'Subscription Account Started!', $user->name));
        }

        return redirect()->back()->with('success', 'Subscription confirmed and activated.');
    }
}
