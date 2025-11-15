<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display all payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'subscription']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by gateway
        if ($request->filled('gateway')) {
            $query->where('payment_gateway', $request->gateway);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by transaction ID or user
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                               ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $payments = $query->latest()->paginate(20);

        // Statistics
        $stats = [
            'total_payments' => Payment::count(),
            'completed' => Payment::where('status', 'completed')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'revenue_today' => Payment::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount'),
            'revenue_month' => Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'stripe_revenue' => Payment::where('status', 'completed')
                ->where('payment_gateway', 'stripe')
                ->sum('amount'),
            'paypal_revenue' => Payment::where('status', 'completed')
                ->where('payment_gateway', 'paypal')
                ->sum('amount'),
        ];

        return view('admin.payments.index', [
            'title' => 'Payments',
            'payments' => $payments,
            'stats' => $stats,
        ]);
    }

    /**
     * Show payment details
     */
    public function show(Payment $payment)
    {
        $payment->load(['user', 'subscription']);

        return view('admin.payments.show', [
            'title' => 'Payment Details',
            'payment' => $payment,
        ]);
    }

    /**
     * Export payments to CSV
     */
    public function export(Request $request)
    {
        $query = Payment::with(['user', 'subscription']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('gateway')) {
            $query->where('payment_gateway', $request->gateway);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->get();

        $filename = 'payments_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Transaction ID',
                'User Name',
                'User Email',
                'Amount',
                'Currency',
                'Gateway',
                'Status',
                'Payment Type',
                'Date',
                'Paid At',
            ]);

            // Data
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->transaction_id,
                    $payment->user->name,
                    $payment->user->email,
                    $payment->amount,
                    $payment->currency,
                    $payment->payment_gateway,
                    $payment->status,
                    $payment->payment_type,
                    $payment->created_at->format('Y-m-d H:i:s'),
                    $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}