<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class FarmerSettingsController extends Controller
{
    public function index()
    {
        return view('farmer.settings');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => ['required'],
            'new_password' => ['required', 'confirmed', Password::min(6)->mixedCase()->numbers()->symbols()],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Old password is incorrect']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    public function uploadDocuments(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();

        // Delete previous document if it exists
        if ($user->verification_document && Storage::disk('public')->exists($user->verification_document)) {
            Storage::disk('public')->delete($user->verification_document);
        }

        $file = $request->file('document');
        $filename = 'verification_' . $user->id . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents', $filename, 'public');

        $user->verification_document = $path;
        $user->is_verified = false;
        $user->save();

        return back()->with('success', 'Document submitted for verification.');
    }
}
