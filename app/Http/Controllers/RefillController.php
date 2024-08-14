<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agencies;
use App\Models\AdAccount;
use App\Models\Refill;
use App\Models\AgencyTransaction;
use App\Models\Settings;
use App\Models\SystemNotification;
use Carbon\Carbon;

class RefillController extends Controller
{

    public function index(Request $request)
    {
        if (auth()->user()->role == 'customer') {
            $customers = User::where('id', auth()->user()->id)
                ->get();
            $paymentMethods = Settings::where('setting_name', 'Refill Payment Method')->get();

            $query = Refill::with('client', 'adAccount')
                ->where('client_id', auth()->user()->id)
                ->where('payment_method', '!=', 'Transferred')
                ->orderBy('created_at', 'desc');

            $refillCount = Refill::with('client', 'adAccount')
                ->where('client_id', auth()->user()->id)
                ->where('payment_method', '!=', 'Transferred')
                ->count();

            $refills = $query->paginate(50); // Adjust the number of items per page as needed

            if ($request->ajax()) {
                return view('template.home.refill_application.filtered_data', compact('refills'))->render();
            }

            return view('template.home.refill_application.index', compact('refills', 'refillCount', 'customers', 'paymentMethods'));
        } else {
            $customers = User::where('role', 'customer')->get();
            $paymentMethods = Settings::where('setting_name', 'Refill Payment Method')->get();

            $query = Refill::with('client', 'adAccount')
                ->where('payment_method', '!=', 'Transferred')
                ->orderBy('created_at', 'desc');

            $refillCount = Refill::with('client', 'adAccount')
                ->where('payment_method', '!=', 'Transferred')
                ->count();

            $refills = $query->paginate(50); // Adjust the number of items per page as needed

            if ($request->ajax()) {
                return view('template.home.refill_application.filtered_data', compact('refills'))->render();
            }

            return view('template.home.refill_application.index', compact('refills', 'refillCount', 'customers', 'paymentMethods'));
        }
    }

    public function generateDateRangeRefill(Request $request)
    {

        if (auth()->user()->role == 'customer') {
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
            $customers = User::where('id', auth()->user()->id)
                ->get();
            $paymentMethods = Settings::where('setting_name', 'Refill Payment Method')->get();

            $query = Refill::with('client', 'adAccount')
                ->whereBetween('refills.created_at', [$startDate, $endDate])
                ->where('client_id', auth()->user()->id)
                ->where('payment_method', '!=', 'Transferred')
                ->orderBy('created_at', 'desc');

            $refillCount = Refill::with('client', 'adAccount')
                ->whereBetween('refills.created_at', [$startDate, $endDate])
                ->where('client_id', auth()->user()->id)
                ->where('payment_method', '!=', 'Transferred')
                ->count();

            $refills = $query->get(); // Adjust the number of items per page as needed

            return view('template.home.refill_application.index_date_range', [
                'refills' => $refills,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'refillCount' => $refillCount,
                'customers' => $customers,
                'paymentMethods' => $paymentMethods,
            ]);
        } else {
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
            $customers = User::where('role', 'customer')->get();
            $paymentMethods = Settings::where('setting_name', 'Refill Payment Method')->get();

            $query = Refill::whereBetween('refills.created_at', [$startDate, $endDate])
                ->with('client', 'adAccount')
                ->where('payment_method', '!=', 'Transferred')
                ->orderBy('created_at', 'desc');

            $refillCount = Refill::whereBetween('refills.created_at', [$startDate, $endDate])
                ->with('client', 'adAccount')
                ->where('payment_method', '!=', 'Transferred')
                ->count();

            $refills = $query->get(); // Adjust the number of items per page as needed

            return view('template.home.refill_application.index_date_range', [
                'refills' => $refills,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'refillCount' => $refillCount,
                'customers' => $customers,
                'paymentMethods' => $paymentMethods,
            ]);
        }
    }


    public function pending()
    {
        $refills = Refill::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->where('payment_method', '!=', 'Transferred')
            ->get();
        $refillCount = Refill::where('status', 'pending')
            ->where('payment_method', '!=', 'Transferred')
            ->count();
        return view('template.home.refill_application.pending', compact('refills', 'refillCount'));
    }
    public function refill_application()
    {
        $customers = User::where('role', 'customer')->get();
        $paymentMethods = Settings::where('setting_name', 'Refill Payment Method')->get();
        return view('template.home.refill_application.refill_application', compact('customers', 'paymentMethods'));
    }


    // new refill for customer
    public function newRefill($id)
    {
        $customer = User::where('id', $id)->get();
        $adaccount = AdAccount::where('client_id', $id)->get();
        $paymentMethods = Settings::where('setting_name', 'Refill Payment Method')->get();

        return view('template.home.refill_application.refill_application_new', compact('customer', 'paymentMethods', 'adaccount'));
    }
    // *************

    public function refill_application_id(AdAccount $adAccount)
    {
        $paymentMethods = Settings::where('setting_name', 'Refill Payment Method')->get();

        return view('template.home.refill_application.refill_application_id', compact('adAccount', 'paymentMethods')); // Pass the ad account to the refill view
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'ad_account_id' => 'required|exists:ad_accounts,id',
            'amount_taka' => 'nullable|numeric',
            'amount_dollar' => 'nullable|numeric',
            'payment_method' => 'required|string|max:255',
            'screenshot' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'new_date' => 'nullable|date', // Add validation for new_date
        ]);

        $data = $request->all();

        if ($request->hasFile('screenshot')) {
            $data['screenshot'] = $request->file('screenshot')->store('screenshots', 'public');
        }

        $refill = new Refill($data);

        // Check if new_date is provided and set the created_at attribute
        if ($request->filled('new_date')) {
            $refill->created_at = $request->input('new_date');
        }

        $refill->save();

        SystemNotification::create([
            'notification' => "Refill request of amount " . $request->input('amount_dollar') . " for ad account submitted by " . auth()->user()->name
        ]);

        return redirect()->route('refills.index')->with('success', 'Refill application submitted successfully.');
    }



    public function show($id)
    {
        $refill = Refill::with('client', 'adAccount')->findOrFail($id);
        return view('template.home.refill_application.show', compact('refill'));
    }

    public function edit($id)
    {
        if (auth()->user()->role == 'customer') {
            return redirect('/');
        }
        $refill = Refill::findOrFail($id);
        $paymentMethods = Settings::where('setting_name', 'Refill Payment Method')->get();
        $customers = User::where('role', 'customer')->get();
        $adAccounts = AdAccount::where('client_id', $refill->client_id)->get();
        return view('template.home.refill_application..edit', compact('refill', 'customers', 'adAccounts', 'paymentMethods'));
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'manager') {
            return redirect('/');
        }

        $refill = Refill::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('screenshot')) {
            $data['screenshot'] = $request->file('screenshot')->store('screenshots', 'public');
        }

        if ($request->has('new_date')) {
            $newDate = Carbon::parse($request->new_date);
            // Preserve original time from existing 'created_at'
            $newDateTime = $newDate->format('Y-m-d') . ' ' . $refill->created_at->format('H:i:s');
            $refill->created_at = $newDateTime;
        }

        $refill->update($data);

        return redirect()->route('refills.index')->with('success', 'Refill application updated successfully.');
    }

    public function approve(Request $request, $id)
    {

        $refill = Refill::findOrFail($id);

        $agency = $refill->adAccount->agency;
        $agencyTransactionData = [
            'refills_id' => $refill->id,
            'cl_rate' => $refill->adAccount->dollar_rate,
            'refill_usd' => $refill->amount_dollar,
            'refill_tk' => $refill->amount_taka,
            'agency_charge_type' => $agency->commission_type,
        ];

        if ($agency->commission_type == 'Percentage') {
            $agencyRate = $agency->percentage_rate;
            $refill_act_usd = (($agencyRate / 100) * $refill->amount_dollar) + $refill->amount_dollar;
            $agencyTransactionData['refill_act_usd'] = $refill_act_usd;
            $agencyTransactionData['agency_charge'] = ($agencyRate / 100) * $refill->amount_dollar;
            $agencyTransactionData['agency_rate'] = $agencyRate;
        } elseif ($agency->commission_type == 'Dollar Rate') {
            $refill_act_tk = $agency->dollar_rate * $refill->amount_dollar;
            $agencyTransactionData['refill_act_tk'] = $refill_act_tk;
            $agencyTransactionData['agency_rate'] = $agency->dollar_rate;
        } elseif ($agency->commission_type == 'Own Account') {
            // No additional fields needed for 'Own Account'
        }

        AgencyTransaction::create($agencyTransactionData);

        $refill->update([
            'status' => 'approved',
            'assign' => auth()->user()->name
        ]);

        SystemNotification::create([
            'notification' => "Refill request status changed by " . auth()->user()->name
        ]);

        return back();
    }

    public function reject(Request $request, $id)
    {

        $refill = Refill::findOrFail($id);
        $refill->update([
            'status' => 'rejected',
            'assign' => auth()->user()->name
        ]);

        SystemNotification::create([
            'notification' => "Refill request status changed by " . auth()->user()->name
        ]);

        return back();
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/');
        }
        $refill = Refill::findOrFail($id);
        $refill->delete();

        return redirect()->route('refills.index')->with('success', 'Refill application deleted successfully.');
    }

    public function getClientAdAccounts($client_id)
    {
        $adAccounts = AdAccount::where('client_id', $client_id)
            ->where('status', 'approved')
            ->get();
        return response()->json($adAccounts);
    }

    public function getAdAccountDetails($id)
    {
        $adAccount = AdAccount::findOrFail($id);
        return response()->json($adAccount);
    }

    public function updateStatus(Request $request, $id)
    {
        // Redirect customer role to home page
        if (auth()->user()->role == 'customer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate the request
        $request->validate([
            'status' => 'required|string|in:pending,approved,rejected',
        ]);

        $refill = Refill::findOrFail($id);

        // Check if the status is changing from 'approved' to something else
        if ($refill->status == 'approved' && $request->status != 'approved') {
            AgencyTransaction::where('refills_id', $refill->id)->delete();
        }

        // Process only if status is approved
        if ($request->status == 'approved') {
            $agency = $refill->adAccount->agency;
            $agencyTransactionData = [
                'refills_id' => $refill->id,
                'cl_rate' => $refill->adAccount->dollar_rate,
                'refill_usd' => $refill->amount_dollar,
                'refill_tk' => $refill->amount_taka,
                'agency_charge_type' => $agency->commission_type,
            ];

            if ($agency->commission_type == 'Percentage') {
                $agencyRate = $agency->percentage_rate;
                $refill_act_usd = (($agencyRate / 100) * $refill->amount_dollar) + $refill->amount_dollar;
                $agencyTransactionData['refill_act_usd'] = $refill_act_usd;
                $agencyTransactionData['agency_charge'] = ($agencyRate / 100) * $refill->amount_dollar;
                $agencyTransactionData['agency_rate'] = $agencyRate;
            } elseif ($agency->commission_type == 'Dollar Rate') {
                $refill_act_tk = $agency->dollar_rate * $refill->amount_dollar;
                $agencyTransactionData['refill_act_tk'] = $refill_act_tk;
                $agencyTransactionData['agency_rate'] = $agency->dollar_rate;
            } elseif ($agency->commission_type == 'Own Account') {
                // No additional fields needed for 'Own Account'
            }

            AgencyTransaction::create($agencyTransactionData);
        }

        // Update refill status and assign user
        $refill->update([
            'status' => $request->status,
            'assign' => auth()->user()->name
        ]);

        // Create a system notification
        SystemNotification::create([
            'notification' => "Refill request status changed by " . auth()->user()->name
        ]);

        // Respond with success message
        return response()->json(['success' => 'Status updated successfully.']);
    }
}
