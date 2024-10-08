<?php

namespace App\Http\Controllers;

use App\Models\Refill;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agencies;
use App\Models\AdAccount;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemNotification;


class AdAccountController extends Controller
{
    public function index()
    {
        if (auth()->user()->role == 'customer') {
            $loadMore = route('ad-accounts.load-more');
            $loadAll = route('ad-accounts.load-all');
            $adAccounts = AdAccount::where('client_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate(50);
            return view('template.home.ad_account.index', compact('adAccounts', 'loadMore', 'loadAll'));
        } else
            $loadMore = route('ad-accounts.load-more');
        $loadAll = route('ad-accounts.load-all');
        $adAccounts = AdAccount::orderBy('created_at', 'desc')->paginate(50);
        return view('template.home.ad_account.index', compact('adAccounts', 'loadMore', 'loadAll'));
    }

    public function loadMore(Request $request)
    {
        if ($request->ajax()) {
            $page = $request->page;
            $adAccounts = AdAccount::orderBy('created_at', 'desc')->paginate(50, ['*'], 'page', $page);
            return view('template.home.ad_account.load_more', compact('adAccounts'))->render();
        }
        return response()->json(['message' => 'Bad Request'], 400);
    }

    public function loadAll(Request $request)
    {
        if ($request->ajax()) {
            if (auth()->user()->role == 'customer') {
                $adAccounts = AdAccount::where('client_id', auth()->user()->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $adAccounts = AdAccount::orderBy('created_at', 'desc')->get();
            }
            return view('template.home.ad_account.load_more', compact('adAccounts'))->render();
        }
        return response()->json(['message' => 'Bad Request'], 400);
    }




    public function showPendingAdAccounts()
    {
        if (auth()->user()->role == 'customer') {
            $loadMore = route('ad-accounts.load-more-pending');
            $loadAll = route('ad-accounts.load-all-pending');
            $adAccounts = AdAccount::where('client_id', auth()->user()->id)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->paginate(50);
            return view('template.home.ad_account.index', compact('adAccounts', 'loadMore', 'loadAll'));
        } else
            $loadMore = route('ad-accounts.load-more-pending');
        $loadAll = route('ad-accounts.load-all-pending');
        $adAccounts = AdAccount::where('status', 'pending')->orderBy('created_at', 'desc')->paginate(50);
        return view('template.home.ad_account.index', compact('adAccounts', 'loadMore', 'loadAll'));
    }

    public function loadMorePending(Request $request)
    {
        if ($request->ajax()) {
            $page = $request->page;
            $adAccounts = AdAccount::orderBy('created_at', 'desc')->where('status', 'pending')->paginate(50, ['*'], 'page', $page);
            return view('template.home.ad_account.load_more', compact('adAccounts'))->render();
        }
        return response()->json(['message' => 'Bad Request'], 400);
    }

    public function loadAllPending(Request $request)
    {
        if ($request->ajax()) {
            if (auth()->user()->role == 'customer') {
                $adAccounts = AdAccount::where('client_id', auth()->user()->id)
                    ->where('status', 'pending')
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $adAccounts = AdAccount::orderBy('created_at', 'desc')
                    ->where('status', 'pending')
                    ->get();
            }
            return view('template.home.ad_account.load_more', compact('adAccounts'))->render();
        }
        return response()->json(['message' => 'Bad Request'], 400);
    }



    public function showApprovedAdAccounts()
    {
        if (auth()->user()->role == 'customer') {
            $loadMore = route('ad-accounts.load-more-approved');
            $loadAll = route('ad-accounts.load-all-approved');
            $adAccounts = AdAccount::where('client_id', auth()->user()->id)
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->paginate(50);
            return view('template.home.ad_account.index', compact('adAccounts', 'loadMore', 'loadAll'));
        } else
            $loadMore = route('ad-accounts.load-more-approved');
        $loadAll = route('ad-accounts.load-all-approved');
        $adAccounts = AdAccount::where('status', 'approved')->orderBy('created_at', 'desc')->paginate(50);
        return view('template.home.ad_account.index', compact('adAccounts', 'loadMore', 'loadAll'));
    }

    public function loadMoreApproved(Request $request)
    {
        if ($request->ajax()) {
            $page = $request->page;
            $adAccounts = AdAccount::orderBy('created_at', 'desc')->where('status', 'approved')->paginate(50, ['*'], 'page', $page);
            return view('template.home.ad_account.load_more', compact('adAccounts'))->render();
        }
        return response()->json(['message' => 'Bad Request'], 400);
    }

    public function loadAllApproved(Request $request)
    {
        if ($request->ajax()) {
            if (auth()->user()->role == 'customer') {
                $adAccounts = AdAccount::where('client_id', auth()->user()->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $adAccounts = AdAccount::orderBy('created_at', 'desc')
                    ->where('status', 'approved')
                    ->get();
            }
            return view('template.home.ad_account.load_more', compact('adAccounts'))->render();
        }
        return response()->json(['message' => 'Bad Request'], 400);
    }




    public function account()
    {
        if (auth()->user()->role == 'customer') {
            $userId = Auth::id(); // Get the ID of the current authenticated user
            $adAccounts = AdAccount::where('status', 'approved')
                ->where('client_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();
            return view('template.home.ad_account.myaccount', compact('adAccounts'));
        } elseif (auth()->user()->role == 'admin' || auth()->user()->role == 'manager' || auth()->user()->role == 'employee') {
            $adAccounts = AdAccount::where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->get();
            return view('template.home.ad_account.myaccount', compact('adAccounts'));
        }
    }


    public function create()
    {
        $agencies = Agencies::all(); // Fetch all ad account agencies
        $customers = User::where('role', 'customer')->get(); // Fetch all users with role 'customer'
        $dollarRates = Settings::where('setting_name', 'Default Dollar Rate')->get();
        return view('template.home.ad_account.ad_account_application', compact('agencies', 'customers', 'dollarRates')); // Pass the data to the view
    }

    public function ad_account_id(User $user)
    {
        $agencies = Agencies::all(); // Fetch all ad account agencies
        $customers = User::where('role', 'customer')->get(); // Fetch all users with role 'customer'
        $dollarRates = Settings::where('setting_name', 'Default Dollar Rate')->get();
        return view('template.home.ad_account.ad_account_application_id', compact('user', 'agencies', 'customers', 'dollarRates')); // Pass the data to the view
    }



    public function store(Request $request)
    {
        if ($request->ad_acc_type) {
            AdAccount::create([
                'client_id' => $request->client_name,
                'ad_acc_name' => $request->ad_acc_name,
                'bm_id' => $request->bm_id,
                'ad_acc_id' => $request->ad_acc_id,
                'fb_link1' => $request->fb_link1,
                'fb_link2' => $request->fb_link2,
                'fb_link3' => $request->fb_link3,
                'fb_link4' => $request->fb_link4,
                'fb_link5' => $request->fb_link5,
                'domain1' => $request->domain1,
                'domain2' => $request->domain2,
                'domain3' => $request->domain3,
                'agency_id' => $request->agency,
                'ad_acc_type' => $request->ad_acc_type,
                'dollar_rate' => $request->dollar_rate,
                'status' => 'pending', // Default status
            ]);
        } else {
            AdAccount::create([
                'client_id' => $request->client_name,
                'ad_acc_name' => $request->ad_acc_name,
                'bm_id' => $request->bm_id,
                'fb_link1' => $request->fb_link1,
                'fb_link2' => $request->fb_link2,
                'fb_link3' => $request->fb_link3,
                'fb_link4' => $request->fb_link4,
                'fb_link5' => $request->fb_link5,
                'domain1' => $request->domain1,
                'domain2' => $request->domain2,
                'domain3' => $request->domain3,
                'agency_id' => $request->agency,
                'ad_acc_type' => $request->ad_acc_type_select,
                'dollar_rate' => $request->dollar_rate,
                'status' => 'pending', // Default status
            ]);
        }

        SystemNotification::create([
            'notification' => "A new ad account {$request->ad_acc_name} request submitted by " . auth()->user()->name,
            'notifiable_id' => $request->client_name,
        ]);


        return redirect()->route('ad-account.index')->with('success', 'Ad Account Application submitted successfully');
    }

    public function show($id)
    {
        $adAccount = AdAccount::findOrFail($id);
        $totalAmountUsd = Refill::where('ad_account_id', $id)
            ->where('status', 'approved')
            ->sum('amount_dollar');
        return view('template.home.ad_account.show', compact('adAccount', 'totalAmountUsd'));
    }

    public function myaccountshow($id)
    {
        $adAccount = AdAccount::findOrFail($id);
        $refills = Refill::where('ad_account_id', $id)->orderBy('created_at', 'desc')->get();
        $totalAmountUsd = Refill::where('ad_account_id', $id)
            ->where('status', 'approved')
            ->sum('amount_dollar');

        $otherAdAccounts = AdAccount::where('id', '!=', $id)
            ->where('client_id', $adAccount->client_id)
            ->where('status', 'approved')
            ->get();
        return view('template.home.ad_account.myaccountshow', compact('adAccount', 'refills', 'totalAmountUsd', 'otherAdAccounts'));
    }

    public function edit($id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'manager') {
            return redirect('/');
        }
        $adAccount = AdAccount::findOrFail($id);
        $agencies = Agencies::all();
        $customers = User::where('role', 'customer')->get();
        return view('template.home.ad_account.edit', compact('adAccount', 'agencies', 'customers'));
    }

    public function update(Request $request, $id)
    {

        $adAccount = AdAccount::findOrFail($id);
        $adAccount->update([
            'client_id' => $request->client_name,
            'ad_acc_name' => $request->ad_acc_name,
            'ad_acc_id' => $request->ad_acc_id,
            'bm_id' => $request->bm_id,
            'fb_link1' => $request->fb_link1,
            'fb_link2' => $request->fb_link2,
            'fb_link3' => $request->fb_link3,
            'fb_link4' => $request->fb_link4,
            'fb_link5' => $request->fb_link5,
            'domain1' => $request->domain1,
            'domain2' => $request->domain2,
            'domain3' => $request->domain3,
            'agency_id' => $request->agency,
            'ad_acc_type' => $request->ad_acc_type,
            'dollar_rate' => $request->dollar_rate,
            'status' => $request->status ?? 'pending',
        ]);

        SystemNotification::create([
            'notification' => "Ad account {$request->ad_acc_name} edited by " . auth()->user()->name,
        ]);

        return redirect()->route('ad-account.index')->with('success', 'Ad Account Application updated successfully.');
    }

    public function close(Request $request, $id)
    {
        $adAccount = AdAccount::findOrFail($id);
        $adAccount->update([
            'isActive' => 0,
        ]);

        $adAccounts = AdAccount::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        SystemNotification::create([
            'notification' => "Ad account {$request->ad_acc_name} closed by " . auth()->user()->name,
        ]);

        return view('template.home.ad_account.myaccount', compact('adAccounts'));
    }

    public function active(Request $request, $id)
    {
        $adAccount = AdAccount::findOrFail($id);
        $adAccount->update([
            'isActive' => 1,
        ]);

        $adAccounts = AdAccount::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        SystemNotification::create([
            'notification' => "Ad account {$request->ad_acc_name} activited by " . auth()->user()->name,
        ]);
        return view('template.home.ad_account.myaccount', compact('adAccounts'));
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/');
        }
        $adAccount = AdAccount::findOrFail($id);
        $adAccount->delete();

        SystemNotification::create([
            'notification' => "Ad account {$adAccount->ad_acc_name} removed by " . auth()->user()->name,
        ]);

        return redirect()->route('ad-account.index')->with('success', 'Ad Account Application deleted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        if (auth()->user()->role == 'customer') {
            return redirect('/');
        }
        $request->validate([
            'status' => 'required|string|in:pending,in-review,approved,canceled,rejected',

        ]);

        $adAccount = AdAccount::findOrFail($id);
        $adAccount->update([
            'status' => $request->status,
            'assign' => auth()->user()->name
        ]);

        SystemNotification::create([
            'notification' => "Ad account {$adAccount->ad_acc_name} status changed to {$request->status} by " . auth()->user()->name,
            'notifiable_id' => $adAccount->client_id,
        ]);

        return back()->with('success', 'Status updated successfully.');
    }

    // app/Http/Controllers/AdAccountController.php

    public function updateStatusAjax(Request $request, $id)
    {
        if (auth()->user()->role == 'customer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|string|in:pending,in-review,approved,canceled,rejected',
        ]);

        $adAccount = AdAccount::findOrFail($id);
        $adAccount->update([
            'status' => $request->status,
            'assign' => auth()->user()->name
        ]);

        SystemNotification::create([
            'notification' => "Ad account {$adAccount->ad_acc_name} status changed to {$request->status} by " . auth()->user()->name,
            'notifiable_id' => $adAccount->client_id,
        ]);

        return response()->json(['success' => 'Status updated successfully.', 'status' => $request->status]);
    }



    public function transfer(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/');
        }
        $request->validate([
            'transfer_amount' => 'required|numeric|min:0.01',
            'recipient_account' => 'required|exists:ad_accounts,id',
        ]);

        $adAccount = AdAccount::findOrFail($id);
        $recipientAccount = AdAccount::findOrFail($request->recipient_account);

        Refill::create(
            [
                'client_id' => $adAccount->client_id,
                'ad_account_id' => $adAccount->id,
                'amount_dollar' => - ($request['transfer_amount']),
                'amount_taka' => - ($adAccount->dollar_rate * $request['transfer_amount']),
                'payment_method' => 'Transferred',
                'status' => 'approved',
                'sent_to_agency' => '1',
            ]
        );

        Refill::create(
            [
                'client_id' => $recipientAccount->client_id,
                'ad_account_id' => $recipientAccount->id,
                'amount_dollar' => $request['transfer_amount'],
                'amount_taka' => ($adAccount->dollar_rate * $request['transfer_amount']),
                'payment_method' => 'Transferred',
                'status' => 'approved',
                'sent_to_agency' => '1',
            ]
        );

        return redirect()->route('my-account.show', $adAccount->id)->with('success', 'Amount transferred successfully.');
    }
}
