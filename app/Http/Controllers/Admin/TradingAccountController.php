<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\PingServer;
use Illuminate\Http\Request;

class TradingAccountController extends Controller
{
    use PingServer;

    public function tradingAccounts()
    {
        $response = $this->fetctApi('/trading-accounts');
        $apisettings = $this->fetctApi('/settings');

        $amountPerSlot = $apisettings['data']['amount_per_slot'];
        $accounts = $this->fetctApi('/master-account');

        return view('admin.subscription.tradingAccounts', [
            'title' => 'Provisioned Trading accounts',
            'data' => $response['data'],
            'amountPerSlot' => $amountPerSlot,
            'masters' => $accounts['data'],
        ]);
    }


    public function renewAccount(Request $request)
    {
        $response = $this->fetctApi('/renew-account', [
            'account' => $request->account_id,
        ], 'POST');

        if ($response->failed()) {
            return redirect()->back()->with('message', $response['message']);
        }

        return redirect()->back()->with('success', $response['message']);
    }

    public function createSubscriberAccount(Request $request)
    {
        $response = $this->fetctApi('/create-sub-account', [
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

        if ($request->has('mt4id')) {
            app(SubscriptionController::class)->confirmsub($request->mt4id);
        }
        return redirect()->back()->with('success', $response['message']);
    }


    public function deleteSubAccount($id)
    {
        $response = $this->fetctApi('/delete-sub-account' . '/' . $id);
        if ($response->failed()) {
            return redirect()->back()->with('message', $response['message']);
        }
        return redirect()->back()->with('success', $response['message']);
    }

    public function copyTrade(Request $request)
    {
        $response = $this->fetctApi('/copytrade', [
            'account' => $request->subscriberid,
            'master_account_id' => $request->master,
        ], 'POST');

        if ($response->failed()) {
            return redirect()->back()->with('message', $response['message']);
        }
        return redirect()->back()->with('success', $response['message']);
    }


    public function deployment($id, $deployment)
    {
        $response = $this->fetctApi('/deployment', [
            'account' => $id,
            'deploy_type' => $deployment,
        ], 'POST');
        if ($response->failed()) {
            return redirect()->back()->with('message', $response['message']);
        }
        return redirect()->back()->with('success', $response['message']);
    }
}
