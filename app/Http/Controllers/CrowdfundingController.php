<?php

namespace App\Http\Controllers;

use App\Models\Crowdfunding;
use Illuminate\Http\Request;
use Laravel\Cashier\Payment;

class CrowdfundingController extends Controller
{
    public function intent(Request $request)
    {
        // $request->validate([
        //     'amount' => 'required|numeric|min:1',
        // ]);

        // $payment = $request->user()->payWith(
        //     $request->amount,
        //     ['card', 'bancontact']
        // );

        return auth()->user()->createSetupIntent();
    }
}
