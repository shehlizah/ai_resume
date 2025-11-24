<x-layouts.app :title="'Checkout - ' . $addOn->name">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <div class="mb-4">
                    <h4 class="fw-bold">🛒 Checkout</h4>
                    <p class="text-muted">Complete your purchase to get instant access</p>
                </div>

                <!-- Order Summary -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex">
                                <i class="bx {{ $addOn->icon ?? 'bx-gift' }} me-3" style="font-size: 2.5rem; color: #6366f1;"></i>
                                <div>
                                    <h6 class="mb-1">{{ $addOn->name }}</h6>
                                    <small class="text-muted">{{ Str::limit($addOn->description, 100) }}</small>
                                </div>
                            </div>
                            <h5 class="text-primary mb-0">${{ number_format($addOn->price, 2) }}</h5>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Subtotal</span>
                            <strong>${{ number_format($addOn->price, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Tax</span>
                            <strong>$0.00</strong>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Total</h5>
                            <h4 class="text-primary mb-0">${{ number_format($addOn->price, 2) }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Select Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.add-ons.purchase', $addOn) }}" method="POST" id="checkoutForm">
                            @csrf

                            <div class="mb-4">
                                <!-- Stripe Payment -->
                                <div class="form-check card p-3 mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" 
                                           id="stripe" value="stripe" checked>
                                    <label class="form-check-label w-100" for="stripe">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i class="bx bxl-stripe" style="font-size: 2rem; color: #635bff;"></i>
                                                <div class="ms-3">
                                                    <strong>Credit/Debit Card</strong>
                                                    <br>
                                                    <small class="text-muted">Powered by Stripe</small>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <i class="bx bxl-visa" style="font-size: 2rem; color: #1434cb;"></i>
                                                <i class="bx bxl-mastercard" style="font-size: 2rem; color: #eb001b;"></i>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- PayPal Payment -->
                        
                            </div>

                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                <strong>Secure Payment:</strong> All transactions are encrypted and secure. 
                                Your payment information is never stored on our servers.
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" target="_blank">Terms of Service</a> and 
                                    <a href="#" target="_blank">Refund Policy</a>
                                </label>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bx bx-lock-alt me-1"></i> Complete Purchase - ${{ number_format($addOn->price, 2) }}
                                </button>
                                <a href="{{ route('user.add-ons.show', $addOn) }}" class="btn btn-outline-secondary">
                                    Back to Add-On Details
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Guarantee -->
                <div class="card border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-check-shield text-success" style="font-size: 3rem;"></i>
                            <div class="ms-3">
                                <h6 class="mb-1">30-Day Money-Back Guarantee</h6>
                                <small class="text-muted">
                                    If you're not satisfied with your purchase, we'll refund you in full. No questions asked.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-layouts.app>