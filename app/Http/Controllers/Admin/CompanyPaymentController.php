<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanyPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = CompanyPayment::with(['user', 'reviewer'])
            ->orderByDesc('created_at');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method !== '') {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by item type
        if ($request->has('item_type') && $request->item_type !== '') {
            $query->where('item_type', $request->item_type);
        }

        $payments = $query->paginate(20);

        $stats = [
            'total' => CompanyPayment::count(),
            'pending' => CompanyPayment::where('status', 'pending')->count(),
            'approved' => CompanyPayment::where('status', 'approved')->count(),
            'rejected' => CompanyPayment::where('status', 'rejected')->count(),
            'total_revenue' => CompanyPayment::where('status', 'approved')->sum('amount'),
        ];

        return view('admin.company-payments.index', compact('payments', 'stats'));
    }

    public function show(CompanyPayment $payment)
    {
        $payment->load(['user', 'reviewer']);
        
        return view('admin.company-payments.show', compact('payment'));
    }

    public function approve(CompanyPayment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Only pending payments can be approved.');
        }

        $payment->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Payment has been approved successfully.');
    }

    public function reject(Request $request, CompanyPayment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Only pending payments can be rejected.');
        }

        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $payment->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Payment has been rejected.');
    }

    public function downloadProof(CompanyPayment $payment)
    {
        if (!$payment->payment_proof) {
            abort(404, 'Payment proof not found');
        }

        $filePath = 'public/' . $payment->payment_proof;
        
        if (!Storage::exists($filePath)) {
            abort(404, 'File not found');
        }

        return Storage::download($filePath);
    }
}
