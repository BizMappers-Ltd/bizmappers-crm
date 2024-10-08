<?php

namespace App\Http\Controllers;

use App\Models\Agencies;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException; // Import the ValidationException class
use App\Models\SystemNotification;

class AgencyController extends Controller
{

    public function index()
    {
        // Fetch all products
        $agencies = Agencies::all();

        return view('template.home.agencies.all_agency', compact('agencies')); // Pass data to view
    }

    public function add_agency()
    {
        return view('template.home.agencies.add_agency');
    }

    public function store(Request $request)
    {
        if ($request['ad_account_type'] == 'Both') {
            if ($request->has('own_commission_type')) {
                Agencies::create(
                    [
                        'agency_name' => $request['agency_name'],
                        'location' => $request['location'],
                        'commission_type' => $request['own_commission_type'],
                        'dollar_rate' => $request['dollar_rate'],
                        'percentage_rate' => $request['percentage_rate'],
                        'ad_account_type' => 'Card Line',
                    ]
                );
                Agencies::create(
                    [
                        'agency_name' => $request['agency_name'],
                        'location' => $request['location'],
                        'commission_type' => $request['own_commission_type'],
                        'dollar_rate' => $request['dollar_rate'],
                        'percentage_rate' => $request['percentage_rate'],
                        'ad_account_type' => 'Credit Line',
                    ]
                );
            } else {
                Agencies::create(
                    [
                        'agency_name' => $request['agency_name'],
                        'location' => $request['location'],
                        'commission_type' => $request['commission_type'],
                        'dollar_rate' => $request['dollar_rate'],
                        'percentage_rate' => $request['percentage_rate'],
                        'ad_account_type' => 'Card Line',
                    ]
                );
                Agencies::create(
                    [
                        'agency_name' => $request['agency_name'],
                        'location' => $request['location'],
                        'commission_type' => $request['commission_type'],
                        'dollar_rate' => $request['dollar_rate'],
                        'percentage_rate' => $request['percentage_rate'],
                        'ad_account_type' => 'Credit Line',
                    ]
                );
            }
        } else {
            if ($request->has('own_commission_type')) {
                Agencies::create(
                    [
                        'agency_name' => $request['agency_name'],
                        'location' => $request['location'],
                        'commission_type' => $request['own_commission_type'],
                        'dollar_rate' => $request['dollar_rate'],
                        'percentage_rate' => $request['percentage_rate'],
                        'ad_account_type' => $request['ad_account_type'],
                    ]
                );
            } else {
                Agencies::create(
                    [
                        'agency_name' => $request['agency_name'],
                        'location' => $request['location'],
                        'commission_type' => $request['commission_type'],
                        'dollar_rate' => $request['dollar_rate'],
                        'percentage_rate' => $request['percentage_rate'],
                        'ad_account_type' => $request['ad_account_type'],
                    ]
                );
            }
        }

        SystemNotification::create([
            'notification' => "A new agency {$request['agency_name']} created by " . auth()->user()->name,

        ]);


        return redirect()->route('all-agency')->with('success', 'Ad Account Agency added successfully.'); // Redirect after creation
    }

    public function details($id)
    {

        $agency = Agencies::findOrFail($id);

        return view('template.home.agencies.agency_details', compact('agency')); // Pass agency data to view
    }

    public function update($id)
    {
        $agency = Agencies::findOrFail($id);

        return view('template.home.agencies.update_agency', compact('agency')); // Pass agency data to view
    }

    public function storeUpdate(Request $request, $id)
    {
        $agency = Agencies::find($id);

        if ($request->has('own_commission_type')) {
            $agency->update([
                'agency_name' => $request['agency_name'],
                'location' => $request['location'],
                'commission_type' => $request['own_commission_type'],
                'dollar_rate' => $request['dollar_rate'],
                'percentage_rate' => $request['percentage_rate'],
                'ad_account_type' => $request['ad_account_type'],
            ]);
        } else {
            $agency->update([
                'agency_name' => $request['agency_name'],
                'location' => $request['location'],
                'commission_type' => $request['commission_type'],
                'dollar_rate' => $request['dollar_rate'],
                'percentage_rate' => $request['percentage_rate'],
                'ad_account_type' => $request['ad_account_type'],
            ]);
        }

        SystemNotification::create([
            'notification' => "Agency {$request['agency_name']} updated by " . auth()->user()->name,

        ]);

        // Redirect to success page or perform other actions
        return redirect()->route('all-agency')->with('success', 'Ad Account Agency updated successfully.');
    }


    public function destroy($id)
    {
        $agency = Agencies::findOrFail($id);
        $agency->delete();

        SystemNotification::create([
            'notification' => "Agency {$agency->agency_name} removed by " . auth()->user()->name,

        ]);

        return redirect()->route('all-agency')->with('success', 'Ad Account Agency deleted successfully.');
    }
}
