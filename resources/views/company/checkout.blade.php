<x-layouts.app :title="__('Checkout')">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="mb-4">
                    <a href="{{ route('company.dashboard') }}" class="text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>

                <!-- Item Summary -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="mb-1">{{ $item['name'] }}</h6>
                                @if($type === 'package')
                                    <p class="text-muted mb-0">{{ $item['jobs'] }} job postings</p>
                                @else
                                    <p class="text-muted mb-0">{{ $item['description'] }}</p>
                                    @if(isset($item['period']))
                                        <small class="text-muted">Valid for 1 {{ $item['period'] }}</small>
                                    @endif
                                @endif
                            </div>
                            <div class="text-end">
                                <h5 class="mb-0 text-primary">IDR {{ number_format($item['price'], 0) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
                @endif

                <!-- Payment Methods -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Select Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills mb-4" id="paymentTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="manual-tab" data-bs-toggle="pill" data-bs-target="#manual" type="button" role="tab">
                                    <i class="bi bi-bank"></i> Bank Transfer
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="stripe-tab" data-bs-toggle="pill" data-bs-target="#stripe" type="button" role="tab">
                                    <i class="bi bi-credit-card"></i> Stripe (Credit Card)
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="paymentTabsContent">
                            <!-- Manual Bank Transfer -->
                            <div class="tab-pane fade show active" id="manual" role="tabpanel">
                                <div class="alert alert-info">
                                    <h6><i class="bi bi-info-circle"></i> Bank Transfer Instructions</h6>
                                    <p class="mb-2">Please transfer the payment to:</p>
                                    <div class="ms-3">
                                        <p class="mb-1"><strong>Bank:</strong> BCA</p>
                                        <p class="mb-1"><strong>Account Number:</strong> 1234567890</p>
                                        <p class="mb-1"><strong>Account Name:</strong> AI Resume Builder</p>
                                        <p class="mb-1"><strong>Amount:</strong> IDR {{ number_format($item['price'], 0) }}</p>
                                    </div>
                                    <p class="mt-2 mb-0 small">After completing the transfer, upload your payment proof below. Admin will verify and approve your payment within 24 hours.</p>
                                </div>

                                <form action="{{ route('company.payment.manual') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="item_type" value="{{ $type }}">
                                    <input type="hidden" name="item_slug" value="{{ $item['slug'] }}">

                                    <div class="mb-3">
                                        <label class="form-label">Account Name (Transfer Sender) <span class="text-danger">*</span></label>
                                        <input type="text" name="bank_account_name" class="form-control @error('bank_account_name') is-invalid @enderror" required>
                                        @error('bank_account_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Transfer Date <span class="text-danger">*</span></label>
                                        <input type="date" name="transfer_date" class="form-control @error('transfer_date') is-invalid @enderror" value="{{ date('Y-m-d') }}" required>
                                        @error('transfer_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Payment Proof (Screenshot/Photo) <span class="text-danger">*</span></label>
                                        <input type="file" name="payment_proof" class="form-control @error('payment_proof') is-invalid @enderror" accept="image/*" required>
                                        <small class="text-muted">Upload screenshot of successful transfer. Max 5MB.</small>
                                        @error('payment_proof')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="bi bi-upload"></i> Submit Payment Proof
                                    </button>
                                </form>
                            </div>

                            <!-- Stripe Payment -->
                            <div class="tab-pane fade" id="stripe" role="tabpanel">
                                <div class="alert alert-info">
                                    <h6><i class="bi bi-credit-card"></i> Pay Securely with Stripe</h6>
                                    <p class="mb-0">You will be redirected to Stripe's secure payment page to complete your purchase using a credit or debit card.</p>
                                </div>

                                <form action="{{ route('company.payment.stripe') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="item_type" value="{{ $type }}">
                                    <input type="hidden" name="item_slug" value="{{ $item['slug'] }}">

                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="bi bi-shield-check text-success me-2" style="font-size: 1.5rem;"></i>
                                                <div>
                                                    <div class="fw-semibold">Secure Payment</div>
                                                    <small class="text-muted">Your payment is processed securely through Stripe</small>
                                                </div>
                                            </div>
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-1"><i class="bi bi-check-circle text-success me-2"></i> Instant activation</li>
                                                <li class="mb-1"><i class="bi bi-check-circle text-success me-2"></i> Secure card processing</li>
                                                <li class="mb-1"><i class="bi bi-check-circle text-success me-2"></i> No manual approval needed</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="bi bi-credit-card"></i> Pay IDR {{ number_format($item['price'], 0) }} with Stripe
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2"><i class="bi bi-question-circle"></i> Need Help?</h6>
                        <p class="text-muted mb-0 small">
                            If you have any issues with payment, please contact our support team at <a href="mailto:support@airesume.com">support@airesume.com</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
