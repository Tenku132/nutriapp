<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FarmerDocument;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\FarmerVerificationStatusNotification;

class FarmerVerificationReviewController extends Controller
{
    public function index()
    {
        $documents = FarmerDocument::with('farmer')
            ->latest()
            ->paginate(15);

        return view('admin.verifications.index', compact('documents'));
    }

    public function approve($id)
    {
        $document = FarmerDocument::findOrFail($id);
        $document->status = 'approved';
        $document->admin_note = null;
        $document->save();

        // Mark the farmer as verified
        $document->farmer->is_verified = true;
        $document->farmer->save();

        // Notify farmer of approval ✅
        $document->farmer->notify(new FarmerVerificationStatusNotification('approved'));

        return back()->with('success', 'Farmer has been verified.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'required|string|max:1000',
        ]);

        $document = FarmerDocument::findOrFail($id);
        $document->status = 'rejected';
        $document->admin_note = $request->admin_note;
        $document->save();

        // Notify farmer of rejection ❌
        $document->farmer->notify(new FarmerVerificationStatusNotification('rejected', $request->admin_note));

        return back()->with('error', 'Document rejected with note.');
    }
}
