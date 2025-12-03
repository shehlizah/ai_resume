<x-layouts.app>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">
                <span class="text-muted fw-light">Admin /</span> Expert Bookings
            </h4>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Total Bookings</span>
                                <div class="d-flex align-items-end mt-2">
                                    <h3 class="mb-0 me-2">{{ $statistics['total'] ?? 0 }}</h3>
                                </div>
                            </div>
                            <span class="badge bg-label-primary rounded p-2">
                                <i class="bx bx-calendar bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Pending</span>
                                <div class="d-flex align-items-end mt-2">
                                    <h3 class="mb-0 me-2">{{ $statistics['pending'] ?? 0 }}</h3>
                                </div>
                            </div>
                            <span class="badge bg-label-warning rounded p-2">
                                <i class="bx bx-time bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Confirmed</span>
                                <div class="d-flex align-items-end mt-2">
                                    <h3 class="mb-0 me-2">{{ $statistics['confirmed'] ?? 0 }}</h3>
                                </div>
                            </div>
                            <span class="badge bg-label-info rounded p-2">
                                <i class="bx bx-check-circle bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Completed</span>
                                <div class="d-flex align-items-end mt-2">
                                    <h3 class="mb-0 me-2">{{ $statistics['completed'] ?? 0 }}</h3>
                                </div>
                            </div>
                            <span class="badge bg-label-success rounded p-2">
                                <i class="bx bx-check-double bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.bookings.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Name, email, or booking ref..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Session Type</label>
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="interview" {{ request('type') == 'interview' ? 'selected' : '' }}>Interview Prep</option>
                                <option value="resume" {{ request('type') == 'resume' ? 'selected' : '' }}>Resume Review</option>
                                <option value="career" {{ request('type') == 'career' ? 'selected' : '' }}>Career Coaching</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Bookings</h5>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Booking Ref</th>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Session Date</th>
                            <th>Type</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td>
                                <strong>{{ $booking->booking_ref }}</strong>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">{{ $booking->name }}</span>
                                    <small class="text-muted">
                                        @if($booking->user)
                                            <a href="{{ url('admin/users/' . $booking->user_id) }}">{{ $booking->user->email }}</a>
                                        @else
                                            {{ $booking->email }}
                                        @endif
                                    </small>
                                </div>
                            </td>
                            <td>
                                <small>
                                    {{ $booking->email }}<br>
                                    {{ $booking->phone ?? 'N/A' }}
                                </small>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ $booking->session_date->format('M d, Y') }}</span>
                                    <small class="text-muted">{{ $booking->session_date->format('h:i A') }}</small>
                                </div>
                            </td>
                            <td>
                                @if($booking->session_type === 'interview')
                                    <span class="badge bg-label-primary">Interview Prep</span>
                                @elseif($booking->session_type === 'resume')
                                    <span class="badge bg-label-info">Resume Review</span>
                                @else
                                    <span class="badge bg-label-success">Career Coaching</span>
                                @endif
                            </td>
                            <td>{{ $booking->duration }} min</td>
                            <td>
                                @if($booking->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="badge bg-info">Confirmed</span>
                                @elseif($booking->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-icon" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if($booking->status === 'pending')
                                            <form method="POST" action="{{ route('admin.bookings.confirm', $booking->id) }}" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bx bx-check me-1"></i> Confirm
                                                </button>
                                            </form>
                                        @endif
                                        @if($booking->status === 'confirmed')
                                            <form method="POST" action="{{ route('admin.bookings.complete', $booking->id) }}" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bx bx-check-double me-1"></i> Mark Complete
                                                </button>
                                            </form>
                                        @endif
                                        @if(in_array($booking->status, ['pending', 'confirmed']))
                                            <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editModal{{ $booking->id }}">
                                                <i class="bx bx-edit me-1"></i> Edit Details
                                            </a>
                                            <form method="POST" action="{{ route('admin.bookings.cancel', $booking->id) }}" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-warning" 
                                                        onclick="return confirm('Cancel this booking?')">
                                                    <i class="bx bx-x me-1"></i> Cancel Booking
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.bookings.destroy', $booking->id) }}" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" 
                                                    onclick="return confirm('Delete this booking? This cannot be undone.')">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Booking: {{ $booking->booking_ref }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.bookings.update', $booking->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Session Date & Time</label>
                                                <input type="datetime-local" 
                                                       name="session_date" 
                                                       class="form-control" 
                                                       value="{{ $booking->session_date->format('Y-m-d\TH:i') }}"
                                                       required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Meeting Link</label>
                                                <input type="url" 
                                                       name="meeting_link" 
                                                       class="form-control" 
                                                       value="{{ $booking->meeting_link }}"
                                                       placeholder="https://zoom.us/j/...">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Admin Notes</label>
                                                <textarea name="admin_notes" 
                                                          class="form-control" 
                                                          rows="3">{{ $booking->admin_notes }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="bx bx-calendar-x bx-lg text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No bookings found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($bookings->hasPages())
            <div class="card-footer">
                {{ $bookings->links() }}
            </div>
            @endif
        </div>
    </div>
</x-layouts.app>
