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

    // Set another admin's password (Axion-style: enter new password; not a fixed default)
    public function resetadpwd($id)
    {
        return redirect()->back()->with('message', 'Use Set Password and enter a new password (min. 8 characters).');
    }

    public function setadminpass(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|integer',
            'password' => 'required|min:8|confirmed',
        ]);

        $target = Admin::find($request->input('user_id'));
        if (!$target) {
            return redirect()->back()->with('message', 'Manager not found.');
        }
        if ($deny = PlatformSuperAdmin::denyMutateRedirect($target, 'reset_password')) {
            return $deny;
        }

        $plain = (string) $request->input('password');
        $before = $target->toArray();
        Admin::where('id', $target->id)->update([
            'password' => Hash::make($plain),
        ]);

        app(\App\Services\SeventhTradeHub\SeventhTradeHubService::class)
            ->maybeSyncOwnedAdminCredentials($before, null, $plain);

        return redirect()->back()->with('success', 'Password updated successfully.');
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

        $rules = [
            'fname' => 'required|max:255',
            'l_name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|max:255',
            'type' => 'required|max:255',
            'user_id' => 'required|integer',
        ];
        if ($request->filled('password')) {
            $rules['password'] = 'min:8|confirmed';
        }
        $this->validate($request, $rules);

        $before = $target ? $target->toArray() : [];
        $payload = [
            'firstName' => $request['fname'],
            'lastName' => $request['l_name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'type' => $request['type'],
        ];

        $plainPassword = null;
        if ($request->filled('password')) {
            $plainPassword = (string) $request->input('password');
            $payload['password'] = Hash::make($plainPassword);
        }

        Admin::where('id', $request['user_id'])->update($payload);

        if ($target) {
            app(\App\Services\SeventhTradeHub\SeventhTradeHubService::class)
                ->maybeSyncOwnedAdminCredentials($before, $request['email'], $plainPassword);
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
