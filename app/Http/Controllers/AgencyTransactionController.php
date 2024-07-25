<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgencyTransaction;
use App\Models\Refill;
use App\Models\SystemNotification;

class AgencyTransactionController extends Controller
{
    public function sendToAgency($id)
    {
        if (auth()->user()->role == 'customer') {
            return response()->json(['success' => false, 'message' => 'Unauthorized.']);
        }

        $refill = Refill::findOrFail($id);

        $refill->update(['sent_to_agency' => 1, 'assign' => auth()->user()->name]);

        SystemNotification::create([
            'notification' => "Refill amount of {$refill->amount_dollar} has been sent to agency by " . auth()->user()->name,
        ]);

        return response()->json(['success' => true, 'message' => 'Deposit sent to agency successfully.']);
    }
}
