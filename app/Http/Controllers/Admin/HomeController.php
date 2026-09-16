<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Settings;
use App\Models\Plans;
use App\Models\SettingsCont;
use App\Models\Agent;
use App\Models\User_plans;
use App\Models\Mt4Details;
use App\Models\Admin;
use App\Models\Faq;
use App\Models\Images;
use App\Models\Testimony;
use App\Models\Content;
use App\Models\Deposit;
use App\Models\Wdmethod;
use App\Models\Withdrawal;
use App\Models\Cp_transaction;
use App\Models\Tp_Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Kyc;
use App\Models\Task;

class HomeController extends Controller
{
    /**
     * Show Admin Dashboard.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $demoIds = [];
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_demo_user')) {
            $demoIds = User::where('is_demo_user', 1)->pluck('id')->all();
        }

        $sumDeposits = function ($status) use ($demoIds) {
            $q = DB::table('deposits')->select(DB::raw('SUM(amount) as count'))->where('status', $status);
            if (!empty($demoIds)) {
                $q->whereNotIn('user', $demoIds);
            }

            return $q->get();
        };
        $sumWithdrawals = function ($status) use ($demoIds) {
            $q = DB::table('withdrawals')->select(DB::raw('SUM(amount) as count'))->where('status', $status);
            if (!empty($demoIds)) {
                $q->whereNotIn('user', $demoIds);
            }

            return $q->get();
        };

        $total_deposited = $sumDeposits('Processed');
        $pending_deposited = $sumDeposits('Pending');
        $total_withdrawn = $sumWithdrawals('Processed');
        $pending_withdrawn = $sumWithdrawals('Pending');

        $userlist = \App\Support\DemoUserVisibility::count();
        $activeusers = \App\Support\DemoUserVisibility::excludeFromQuery(User::where('status', 'active'))->count();
        $blockeusers = \App\Support\DemoUserVisibility::excludeFromQuery(User::where('status', 'blocked'))->count();
        $plans = Plans::count();
        $unverifiedusers = \App\Support\DemoUserVisibility::excludeFromQuery(User::where('account_verify', '!=', 'yes'))->count();

        $chartSum = function ($table, $status) use ($demoIds) {
            $q = DB::table($table)->where('status', $status);
            if (!empty($demoIds)) {
                $q->whereNotIn('user', $demoIds);
            }

            return $q->sum('amount');
        };

        $chart_pdepsoit = $chartSum('deposits', 'Processed');
        $chart_pendepsoit = $chartSum('deposits', 'Pending');
        $chart_pwithdraw = $chartSum('withdrawals', 'Processed');
        $chart_pendwithdraw = $chartSum('withdrawals', 'Pending');
        $chart_trans = Tp_Transaction::sum('amount');

        return view('admin.dashboard', [
            'title' => 'Admin Dashboard',
            'total_deposited' => $total_deposited,
            'pending_deposited' => $pending_deposited,
            'total_withdrawn' => $total_withdrawn,
            'pending_withdrawn' => $pending_withdrawn,
            'user_count' => $userlist,
            'plans' => $plans,
            'chart_pdepsoit' => $chart_pdepsoit,
            'chart_pendepsoit' => $chart_pendepsoit,
            'chart_pwithdraw' => $chart_pwithdraw,
            'chart_pendwithdraw' => $chart_pendwithdraw,
            'chart_trans' => $chart_trans,
            'activeusers' => $activeusers,
            'blockeusers' => $blockeusers,
            'unverifiedusers' => $unverifiedusers,
        ]);
    }
    //Plans route
    public function plans()
    {
        return view('admin.Plans.plans')
            ->with(array(
                'title' => 'System Plans',
                'plans' => Plans::where('type', 'Main')->orderby('created_at', 'ASC')->get(),
                'pplans' => Plans::where('type', 'Promo')->get(),

            ));
    }

    public function newplan()
    {
        return view('admin.Plans.newplan')
            ->with(array(
                'title' => 'Add Investment Plan',

            ));
    }

    public function editplan($id)
    {
        return view('admin.Plans.editplan')
            ->with(array(
                'title' => 'Edit Investment Plan',
                'plan' => Plans::where('id', $id)->first(),

            ));
    }

    //Return manage users route
    public function manageusers()
    {
        return view('admin.Users.users')
            ->with(array(
                'title' => 'All users',

            ));
    }


    //Return create users route
    public function createnewuser()
    {
       
        $usernumber = $this->RandomStringGenerator(11);
            $code1  = $this->RandomStringGenerator(7);
            $code2 =  $this->RandomStringGenerator(7);
             $code3 = $this->RandomStringGenerator(7);
             $pin =   $this->RandomStringGenerator(4);
        return view('admin.createnewuser')
            ->with(array(
                'title' => 'Create a user',
                'code1'=>$code1,
                'code2'=>$code2,
                'code3'=>$code3,
                'usernumber'=>$usernumber,
                'pin' => $pin,

            ));
    }

    public function activeInvestments()
    {
    return view('admin.Plans.activeinv', [
        'title' => 'Active investment plans',
        'plans' => \App\Support\DemoUserVisibility::whereHasNonDemo(
            User_plans::whereIn('active', ['Pending', 'Processed'])->orderByDesc('id')->with(['dplan', 'duser']),
            'duser'
        )->get(),
    ]);
    }


    // Orphaned SQLi-prone FULLTEXT search removed — no route, no FULLTEXT index on mt4_details.
    // Prefer bound LIKE / Scout if search is reintroduced.

    //Return search route for Withdrawals
    public function searchWt(Request $request)
    {
        $dp = \App\Support\DemoUserVisibility::whereHasNonDemo(
            Withdrawal::with('duser'),
            'duser'
        )->get();
        $searchItem = $request['wtquery'];

        $result = \App\Support\DemoUserVisibility::whereHasNonDemo(
            Withdrawal::query()
                ->where(function ($q) use ($searchItem) {
                    $q->where('user', $searchItem)
                        ->orWhere('amount', $searchItem)
                        ->orWhere('payment_mode', $searchItem)
                        ->orWhere('status', $searchItem);
                }),
            'duser'
        )->paginate(10);

        return view('admin.mwithdrawals')
            ->with(array(
                'dp' => $dp,
                'title' => 'Withdrawals search result',
                'withdrawals' => $result,

            ));
    }


    //Return manage withdrawals route
    public function mwithdrawals()
    {
        return view('admin.Withdrawals.mwithdrawals')
            ->with(array(
                'title' => 'Manage users withdrawals',
                'withdrawals' => \App\Support\DemoUserVisibility::whereHasNonDemo(
                    Withdrawal::with('duser')->orderBy('id', 'desc'),
                    'duser'
                )->get(),

            ));
    }

    //Return manage deposits route
    public function mdeposits()
    {
        return view('admin.Deposits.mdeposits')
            ->with(array(
                'title' => 'Manage users deposits',
                'deposits' => \App\Support\DemoUserVisibility::whereHasNonDemo(
                    Deposit::with('duser')->orderBy('id', 'desc'),
                    'duser'
                )->get(),

            ));
    }

    //Return agents route
    public function agents()
    {
        return view('admin.agents')
            ->with(array(
                'title' => 'Manage agents',
                'users' => \App\Support\DemoUserVisibility::excludeFromQuery(User::orderBy('id', 'desc'))->get(),
                'agents' => Agent::all(),
            ));
    }

    public function aboutonlinetrade()
    {
        return view('admin.about')
            ->with(array(
                'title' => 'About Onlinetrader',

            ));
    }

    public function emailServices()
    {
        return view('admin.email.index', [
            'title' =>  "Email services"
        ]);
    }

    //Return view agent route
    public function viewagent($agent)
    {
        $agentUser = User::where('id', $agent)->first();
        if ($deny = \App\Support\DemoUserVisibility::denyPeerAccessRedirect($agentUser)) {
            return $deny;
        }
        return view('admin.viewagent')
            ->with(array(
                'title' => 'Agent record',
                'agent' => $agentUser,
                'ag_r' => \App\Support\DemoUserVisibility::excludeFromQuery(User::where('ref_by', $agent))->get(),

            ));
    }

    // Legacy settings URL — redirect to working App Settings (assets table removed from production).
    public function settings(Request $request)
    {
        return redirect()->route('appsettingshow');
    }

    public function msubtrade()
    {
        $query = Mt4Details::with('tuser')->orderBy('id', 'desc');
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_demo_user')) {
            $query = \App\Support\DemoUserVisibility::whereHasNonDemo($query, 'tuser');
        }
        return view('admin.subscription.msubtrade')
            ->with(array(
                'subscriptions' => $query->paginate(10),
                'title' => 'Manage Subscription',

            ));
    }

    public function userplans($id)
    {
        $user = User::where('id', $id)->first();
        if ($deny = \App\Support\DemoUserVisibility::denyPeerAccessRedirect($user)) {
            return $deny;
        }
        return view('admin.Users.user_plans')
            ->with(array(
                'plans' => User_plans::where('user', $id)->orderBy('id', 'desc')->get(),
                'user' => $user,
                'title' => 'User Loans',

            ));
    }



    //return front end management page
    public function frontpage()
    {
        return view('admin.Settings.FrontendSettings.frontpage', [
            'title' => 'Front page management',
            'faqs' => Faq::orderByDesc('id')->get(),
            'images' => Images::orderBy('id', 'desc')->get(),
            'testimonies' => Testimony::orderBy('id', 'desc')->get(),
            'contents' => Content::orderBy('id', 'desc')->get(),
        ]);
    }


    public function adduser()
    {
        return view('admin.referuser')->with(array(
            'title' => 'Add new Users',
            'settings' => Settings::where('id', '=', '1')->first()
        ));
    }

    public function addmanager()
    {
        return view('admin.addadmin')->with(array(
            'title' => 'Add new manager',
            'settings' => Settings::where('id', '=', '1')->first()
        ));
    }
    public function madmin()
    {
        $query = Admin::orderby('id', 'desc');
        $query = \App\Support\PlatformSuperAdmin::filterAdminsForViewer($query);

        return view('admin.madmin')->with(array(
            'admins' => $query->get(),
            'title' => 'Add new manager',
        ));
    }

    //Return KYC route
    public function kyc()
    {
        return view('admin.kyc', [
            'title' => 'KYC Applications',
            'kycs' => Kyc::orderByDesc('id')->with(['user'])
                ->whereHas('user', function ($q) {
                    \App\Support\DemoUserVisibility::excludeFromQuery($q);
                })->get(),
        ]);
    }

    public function viewKycApplication($id)
    {
        $kyc = Kyc::where('id', $id)->with(['user'])->first();
        if ($kyc && ($deny = \App\Support\DemoUserVisibility::denyPeerAccessRedirect($kyc->user))) {
            return $deny;
        }

        return view('admin.kyc-applications', [
            'title' => 'View KYC Application',
            'kyc' => $kyc,
        ]);
    }

    public function adminprofile()
    {
        return view('admin.Profile.profile')
            ->with(array(
                'title' => 'Admin Profile',


            ));
    }

    public function managecryptoasset()
    {

        return view('admin.Settings.Crypto.pageview', [
            'title' => 'Manage Crypto Asset',
            'moresettings' => SettingsCont::find(1),
        ]);
    }


    public function showtaskpage()
    {
        $admins = \App\Support\PlatformSuperAdmin::filterAdminsForViewer(Admin::orderby('id', 'desc'));
        return view('admin.task')
            ->with(array(
                'admin' => $admins->get(),
                'title' => 'Create a New Task',

            ));
    }

    public function mtask()
    {
        $admins = \App\Support\PlatformSuperAdmin::filterAdminsForViewer(Admin::orderby('id', 'desc'));
        return view('admin.mtask')
            ->with(array(
                'admin' => $admins->get(),
                'tasks' => Task::orderby('id', 'desc')->get(),
                'title' => 'Manage Task',

            ));
    }
    public function viewtask()
    {
        return view('admin.vtask')
            ->with(array(
                'tasks' => Task::orderby('id', 'desc')->where('designation', Auth('admin')->User()->id)->get(),
                'title' => 'View my Task',

            ));
    }

    public function leads()
    {
        $admins = \App\Support\PlatformSuperAdmin::filterAdminsForViewer(Admin::orderBy('id', 'desc'));
        return view('admin.leads')
            ->with(array(
                'admin' => $admins->get(),
                'users' => \App\Support\DemoUserVisibility::excludeFromQuery(
                    User::orderby('id', 'desc')->where('cstatus', NULL)
                )->get(),
                'title' => 'Manage New Registered Clients',
            ));
    }
    public function leadsassign()
    {
        return view('admin.lead_asgn')
            ->with(array(
                'usersAssigned' => \App\Support\DemoUserVisibility::excludeFromQuery(User::orderby('id', 'desc')->where([
                    ['assign_to', Auth('admin')->User()->id],
                    ['cstatus', NULL]
                ]))->get(),

                'title' => 'Manage New Registered Clients',

            ));
    }


    public function customer()
    {
        return view('admin.customer')
            ->with(array(
                'users' => \App\Support\DemoUserVisibility::excludeFromQuery(
                    User::orderby('id', 'desc')->where('cstatus', 'Customer')
                )->get(),
                'title' => 'Manage New Registered Clients',

            ));
    }

    function RandomStringGenerator($n)
{
    $generated_string = "";
    $domain = "12345678900123456789023456789034567890456789056789067890890";
    $len = strlen($domain);
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, $len - 1);
        $generated_string = $generated_string . $domain[$index];
    }
    // Return the random generated string 
    return $generated_string;
}
}