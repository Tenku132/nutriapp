<?php

namespace App\Http\Controllers\Buyer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class BuyerAddressController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('buyer.profile.address', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->address = $request->address;
        $user->save();

        return redirect()->route('buyer.profile.address')->with('success', 'Address updated successfully.');
    }
}
