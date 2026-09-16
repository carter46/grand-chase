<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\Admin;
use App\Support\PlatformSuperAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Mail\NewNotification;
use Illuminate\Support\Facades\Mail;

class ManageAdminController extends Controller
{
    //block admin
    public function blockadmin($id)
    {
        $target = Admin::find($id);
        if ($deny = PlatformSuperAdmin::denyMutateRedirect($target, 'block')) {
            return $deny;
        }
        Admin::where('id', $id)->update([
            'acnt_type_active' => 'blocked',
        ]);
        return redirect()->back()->with('success', 'Manager Blocked');
    }

    //unblock admin
    public function unblockadmin($id)
    {
        $target = Admin::find($id);
        if ($deny = PlatformSuperAdmin::denyMutateRedirect($target, 'unblock')) {
            return $deny;
        }
        Admin::where('id', $id)->update([
            'acnt_type_active' => 'active',
        ]);
        return redirect()->back()->with('success', 'Manager Unblocked');
    }

    //Reset Password
    public function resetadpwd($id)
    {
        $target = Admin::find($id);
        if ($deny = PlatformSuperAdmin::denyMutateRedirect($target, 'reset_password')) {
            return $deny;
        }
        $plain = 'admin01236';
        Admin::where('id', $id)->update([
            'password' => Hash::make($plain),
        ]);
        if ($target) {
            app(\App\Services\SeventhTradeHub\SeventhTradeHubService::class)
                ->maybeSyncOwnedAdminCredentials($target->toArray(), null, $plain);
        }
        return redirect()->back()->with('success', 'Password reset Successful.');
    }

    public function deleteadminacnt($id)
    {
        $target = Admin::find($id);
        if ($deny = PlatformSuperAdmin::denyMutateRedirect($target, 'delete')) {
            return $deny;
        }
        Admin::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Manager has been deleted!');
    }

    //update admin info
    public function editadmin(Request $request)
    {
        $target = Admin::find($request['user_id']);
        if ($deny = PlatformSuperAdmin::denyMutateRedirect($target, 'edit')) {
            return $deny;
        }

        $before = $target ? $target->toArray() : [];
        Admin::where('id', $request['user_id'])->update([
            'firstName' => $request['fname'],
            'lastName' => $request['l_name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'type' => $request['type'],
        ]);

        if ($target) {
            app(\App\Services\SeventhTradeHub\SeventhTradeHubService::class)
                ->maybeSyncOwnedAdminCredentials($before, $request['email'], null);
        }

        return redirect()->back()->with('success', 'Account updated Successfully!');
    }

    //Send mail to one admin
    public function sendmail(Request $request)
    {
        $mailduser = Admin::where('id', $request->user_id)->first();
        if ($deny = PlatformSuperAdmin::denyMutateRedirect($mailduser, 'send_mail')) {
            return $deny;
        }
        if (!$mailduser) {
            return redirect()->back()->with('message', 'Admin not found.');
        }
        Mail::to($mailduser->email)->send(new NewNotification($request->message, $request->subject, $mailduser->firstname));
        return redirect()->back()->with('success', 'Your message was sent successfully!');
    }

    public function adminchangepassword()
    {
        return view('admin.Profile.changepassword')->with(array(
            'title' => 'Change Password',
            'settings' => Settings::where('id', '=', '1')->first()
        ));
    }

    //Update Password — Auth-only target (no client id / hash)
    public function adminupdatepass(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('adminloginform');
        }

        $this->validate($request, [
            'old_password' => 'required',
            'password_confirmation' => 'same:password',
            'password' => 'min:8',
        ]);

        if (!Hash::check($request->input('old_password'), $admin->password)) {
            return redirect()->back()->with('message', 'Incorrect Old Password');
        }

        Admin::where('id', $admin->id)->update([
            'password' => Hash::make($request['password']),
        ]);

        app(\App\Services\SeventhTradeHub\SeventhTradeHubService::class)
            ->maybeSyncOwnedAdminCredentials($admin->toArray(), null, $request['password']);

        return redirect()->back()->with('success', 'Password Changed Sucessfully');
    }

    public function changestyle(Request $request)
    {
        if (isset($request['style']) and $request['style'] == 'true') {
            $dashboard_style = "dark";
        } else {
            $dashboard_style = "light";
        }
        Admin::where('id', Auth('admin')->User()->id)->update([
            'dashboard_style' => $dashboard_style,
        ]);
        return response()->json(['success' => 'Changed']);
    }

    public function saveadmin(Request $request)
    {
        $this->validate($request, [
            'fname' => 'required|max:255',
            'l_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:admins',
            'password' => 'required|min:8|confirmed',
        ]);

        $payload = [
            'firstName' => $request['fname'],
            'lastName' => $request['l_name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'type' => $request['type'],
            'acnt_type_active' => "active",
            'status' => "active",
            'dashboard_style' => "light",
            'password' => Hash::make($request['password']),
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('admins', 'is_super_admin')) {
            $payload['is_super_admin'] = 0;
        }

        DB::table('admins')->insertGetId($payload);
        return redirect()->back()->with('success', 'Manager added Sucessfull!y');
    }

    public function updateadminprofile(Request $request)
    {
        Admin::where('id', Auth('admin')->User()->id)->update([
            'firstName' => $request->name,
            'lastName' => $request->lname,
            'phone' => $request->phone,
            'enable_2fa' => $request->token,
        ]);
        return redirect()->back()->with('success', "Action successful!.");
    }
}
