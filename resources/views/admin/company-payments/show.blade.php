<x-layouts.app :title="__('Payment Details')">
    <div class="mb-4">
        <a href="{{ route('admin.company-payments.index') }}" class="text-decoration-none">
            <i class="bx bx-arrow-back"></i> Back to Payments
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Payment Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Payment #{{ $payment->id }}</h5>
                    @if($payment->status === 'pending')
                        <span class="badge bg-warning">Pending Review</span>
                    @elseif($payment->status === 'approved')
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-danger">Rejected</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Company/User</small>
                            <div class="fw-medium">{{ $payment->user->name }}</div>
                            <small class="text-muted">{{ $payment->user->email }}</small>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Item Type</small>
                            <span class="badge bg-label-{{ $payment->item_type === 'package' ? 'primary' : 'info' }}">
                                {{ ucfirst($payment->item_type) }}
                            </span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Payment Method</small>
                            @if($payment->payment_method === 'stripe')
                                <span class="badge bg-label-success">
                                    <i class="bx bx-credit-card"></i> Stripe
                                </span>
                            @else
                                <span class="badge bg-label-warning">
                                    <i class="bx bx-bank"></i> Manual Transfer
                                </span>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Item Name</small>
                            <div class="fw-medium">{{ $payment->item_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Amount</small>
                            <div class="fw-bold text-primary h5 mb-0">IDR {{ number_format($payment->amount, 0) }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Submitted Date</small>
                            <div>{{ $payment->created_at->format('M d, Y h:i A') }}</div>
                        </div>
                        @if($payment->reviewed_at)
                            <div class="col-md-6">
                                <small class="text-muted d-block">Reviewed Date</small>
                                <div>{{ $payment->reviewed_at->format('M d, Y h:i A') }}</div>
                            </div>
                        @endif
                    </div>

                    @if($payment->reviewed_by && $payment->reviewer)
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <small class="text-muted d-block">Reviewed By</small>
                                <div>{{ $payment->reviewer->name }}</div>
                            </div>
                        </div>
                    @endif

                    @if($payment->admin_notes)
                        <div class="alert alert-info mb-0">
                            <small class="text-muted d-block mb-1">Admin Notes</small>
                            <div>{{ $payment->admin_notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Manual Payment Details -->
            @if($payment->payment_method === 'manual')
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Bank Transfer Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Account Name (Sender)</small>
                                <div class="fw-medium">{{ $payment->bank_account_name }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Transfer Date</small>
                                <div>{{ optional($payment->transfer_date)->format('M d, Y') }}</div>
                            </div>
                        </div>

                        @if($payment->payment_proof)
                            <div>
                                <small class="text-muted d-block mb-2">Payment Proof</small>
                                <div class="mb-3">
                                    <img src="{{ asset('storage/app/' . $payment->payment_proof) }}"
                                         alt="Payment Proof"
                                         class="img-fluid rounded border"
                                         style="max-height: 500px; cursor: pointer;"
                                         onclick="window.open(this.src, '_blank')"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <div class="alert alert-warning" style="display: none;">
                                        <i class="bx bx-error"></i> Unable to load image.
                                        <br><small>Path: {{ asset('storage/app/' . $payment->payment_proof) }}</small>
                                    </div>
                                </div>
                                <a href="{{ route('admin.company-payments.download-proof', $payment) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-download"></i> Download Screenshot
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Stripe Payment Details -->
            @if($payment->payment_method === 'stripe')
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Stripe Payment Details</h5>
                    </div>
                    <div class="card-body">
                        @if($payment->stripe_session_id)
                            <div class="mb-2">
                                <small class="text-muted d-block">Session ID</small>
                                <code>{{ $payment->stripe_session_id }}</code>
                            </div>
                        @endif
                        @if($payment->stripe_payment_intent)
                            <div class="mb-2">
                                <small class="text-muted d-block">Payment Intent</small>
                                <code>{{ $payment->stripe_payment_intent }}</code>
                            </div>
                        @endif
                        <div class="alert alert-info mb-0 mt-3">
                            <i class="bx bx-info-circle"></i> Stripe payments are automatically approved upon successful payment.
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    @if($payment->status === 'pending')
                        <div class="alert alert-warning">
                            <i class="bx bx-time"></i> This payment is awaiting your review
                        </div>

                        <form action="{{ route('admin.company-payments.approve', $payment) }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Approve this payment?')">
                                <i class="bx bx-check-circle"></i> Approve Payment
                            </button>
                        </form>

                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bx bx-x-circle"></i> Reject Payment
                        </button>
                    @else
                        <div class="alert alert-{{ $payment->status === 'approved' ? 'success' : 'danger' }}">
                            <i class="bx bx-{{ $payment->status === 'approved' ? 'check' : 'x' }}-circle"></i>
                            This payment has been {{ $payment->status }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Quick Info</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <small class="text-muted">Payment ID:</small>
                            <div class="fw-medium">#{{ $payment->id }}</div>
                        </li>
                        <li class="mb-2">
                            <small class="text-muted">Item Slug:</small>
                            <div><code>{{ $payment->item_slug }}</code></div>
                        </li>
                        <li class="mb-2">
                            <small class="text-muted">Created:</small>
                            <div>{{ $payment->created_at->diffForHumans() }}</div>
                        </li>
                        @if($payment->updated_at != $payment->created_at)
                            <li>
                                <small class="text-muted">Last Updated:</small>
                                <div>{{ $payment->updated_at->diffForHumans() }}</div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.company-payments.reject', $payment) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">Please provide a reason for rejecting this payment:</p>
                        <div class="mb-3">
                            <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea name="admin_notes" class="form-control" rows="4" required placeholder="E.g., Payment proof is unclear, incorrect amount, etc."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
