<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'subscription']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by user name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by payment gateway
        if ($request->has('gateway') && $request->gateway !== 'all') {
            $query->where('payment_gateway', $request->gateway);
        }

        $payments = $query->latest()->paginate(20);

        // Calculate statistics
        $stats = [
            'total' => Payment::sum('amount'),
            'completed' => Payment::where('status', 'completed')->sum('amount'),
            'pending' => Payment::where('status', 'pending')->sum('amount'),
            'failed' => Payment::where('status', 'failed')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        $payment->load(['user', 'subscription']);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Approve a pending payment
     */
    public function approve(Payment $payment)
    {
        try {
            if ($payment->status !== 'pending') {
                return back()->with('error', 'Only pending payments can be approved.');
            }

            $payment->update(['status' => 'completed']);

            return back()->with('success', 'Payment approved successfully.');
        } catch (\Exception $e) {
            \Log::error('Admin payment approval failed: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while approving the payment.');
        }
    }

    /**
     * Reject a pending payment
     */
    public function reject(Payment $payment)
    {
        try {
            if ($payment->status !== 'pending') {
                return back()->with('error', 'Only pending payments can be rejected.');
            }

            $payment->update(['status' => 'failed']);

            return back()->with('success', 'Payment rejected successfully.');
        } catch (\Exception $e) {
            \Log::error('Admin payment rejection failed: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while rejecting the payment.');
        }
    }
}