<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCart;
use App\Services\AbandonedCartService;
use Illuminate\Http\Request;

class AbandonedCartController extends Controller
{
    public function index(Request $request)
    {
        $query = AbandonedCart::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        // Search by email or name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('email', 'like', "%{$search}%")
                             ->orWhere('name', 'like', "%{$search}%");
                })
                ->orWhere('session_data', 'like', "%{$search}%");
            });
        }

        $carts = $query->paginate(20);

        // Get statistics
        $stats = AbandonedCartService::getStats();

        return view('admin.abandoned-carts.index', compact('carts', 'stats'));
    }

    public function show($id)
    {
        $cart = AbandonedCart::with('user')->findOrFail($id);
        return view('admin.abandoned-carts.show', compact('cart'));
    }

    public function destroy($id)
    {
        $cart = AbandonedCart::findOrFail($id);
        $cart->delete();

        return redirect()->route('admin.abandoned-carts.index')
            ->with('success', 'Abandoned cart record deleted successfully.');
    }

    public function markCompleted($id)
    {
        $cart = AbandonedCart::findOrFail($id);
        $cart->markCompleted();

        return redirect()->back()
            ->with('success', 'Cart marked as completed.');
    }

    public function sendReminder($id)
    {
        $cart = AbandonedCart::with('user')->findOrFail($id);
        
        if (!$cart->user) {
            return redirect()->back()
                ->with('error', 'Cannot send reminder: No user associated with this cart.');
        }

        // Check if email should be sent
        if (!$cart->shouldSendRecoveryEmail()) {
            $nextEmailTime = $cart->getTimeUntilNextEmail();
            $message = 'Email cannot be sent yet.';
            
            if ($nextEmailTime) {
                $message .= ' Next email can be sent at ' . $nextEmailTime->format('M d, Y H:i') . '.';
            } elseif ($cart->recovery_email_sent_count >= 3) {
                $message = 'Maximum recovery emails (3) have already been sent.';
            }
            
            return redirect()->back()->with('error', $message);
        }

        // Send appropriate notification based on type
        switch ($cart->type) {
            case 'signup':
                $cart->user->notify(new \App\Notifications\IncompleteSignupReminder($cart));
                break;
            case 'resume':
                $cart->user->notify(new \App\Notifications\IncompleteResumeReminder($cart));
                break;
            case 'pdf_preview':
            case 'payment':
                $cart->user->notify(new \App\Notifications\PaymentAbandonedReminder($cart));
                break;
        }

        $cart->markRecoveryEmailSent();

        return redirect()->back()
            ->with('success', 'Recovery email ' . $cart->recovery_email_sent_count . ' sent successfully.');
    }
}
