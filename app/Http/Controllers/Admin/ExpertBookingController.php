<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpertBooking;
use App\Models\User;

class ExpertBookingController extends Controller
{
    /**
     * Show all expert bookings
     */
    public function index(Request $request)
    {
        $query = ExpertBooking::with('user')
            ->orderBy('session_date', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('booking_ref', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => ExpertBooking::count(),
            'pending' => ExpertBooking::where('status', 'pending')->count(),
            'confirmed' => ExpertBooking::where('status', 'confirmed')->count(),
            'completed' => ExpertBooking::where('status', 'completed')->count(),
            'upcoming' => ExpertBooking::whereIn('status', ['pending', 'confirmed'])
                ->where('session_date', '>', now())
                ->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    /**
     * Confirm a booking
     */
    public function confirm($id)
    {
        $booking = ExpertBooking::findOrFail($id);
        $booking->confirm();

        return redirect()->back()
            ->with('success', 'Booking confirmed successfully');
    }

    /**
     * Complete a booking
     */
    public function complete($id)
    {
        $booking = ExpertBooking::findOrFail($id);
        $booking->complete();

        return redirect()->back()
            ->with('success', 'Booking marked as completed');
    }

    /**
     * Cancel a booking
     */
    public function cancel($id)
    {
        $booking = ExpertBooking::findOrFail($id);
        $booking->cancel();

        return redirect()->back()
            ->with('success', 'Booking cancelled successfully');
    }

    /**
     * Update booking details
     */
    public function update(Request $request, $id)
    {
        $booking = ExpertBooking::findOrFail($id);

        $validated = $request->validate([
            'session_date' => 'required|date',
            'meeting_link' => 'nullable|url',
            'admin_notes' => 'nullable|string',
        ]);

        $booking->update($validated);

        return redirect()->back()
            ->with('success', 'Booking updated successfully');
    }

    /**
     * Delete a booking
     */
    public function destroy($id)
    {
        $booking = ExpertBooking::findOrFail($id);
        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully');
    }
}
