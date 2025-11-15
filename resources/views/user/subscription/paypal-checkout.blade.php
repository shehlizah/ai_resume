<x-layouts.app :title="$title ?? 'PayPal Checkout'">
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="row justify-content-center">
      <div class="col-lg-6">
        
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="bx bxl-paypal text-info me-2"></i>
              Complete Payment with PayPal
            </h5>
          </div>
          <div class="card-body">
            
            <!-- Order Summary -->
            <div class="mb-4">
              <h5>{{ $plan->name }} Plan</h5>
              <div class="d-flex justify-content-between">
                <span>Amount:</span>
                <strong class="text-primary">${{ number_format($amount, 2) }} / {{ $billingPeriod }}</strong>
              </div>
            </div>

            <!-- PayPal Button Container -->
            <div id="paypal-button-container"></div>

            <!-- Loading State -->
            <div id="loading-state" class="text-center py-4 d-none">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Processing...</span>
              </div>
              <p class="mt-2 text-muted">Processing your payment...</p>
            </div>

            <!-- Cancel Link -->
            <div class="text-center mt-3">
              <a href="{{ route('user.pricing') }}" class="text-muted">
                <i class="bx bx-arrow-back me-1"></i> Cancel and return to pricing
              </a>
            </div>

          </div>
        </div>

      </div>
    </div>

  </div>

  <!-- PayPal SDK -->
  <script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&vault=true&intent=subscription"></script>
  
  <script>
    paypal.Buttons({
      style: {
        shape: 'rect',
        color: 'gold',
        layout: 'vertical',
        label: 'subscribe'
      },
      
      createSubscription: function(data, actions) {
        // Show loading state
        document.getElementById('paypal-button-container').style.display = 'none';
        document.getElementById('loading-state').classList.remove('d-none');

        return actions.subscription.create({
          'plan_id': 'YOUR_PAYPAL_PLAN_ID', // You'll need to create plans in PayPal Dashboard
          'custom_id': '{{ auth()->id() }}',
          'application_context': {
            'brand_name': '{{ config('app.name') }}',
            'shipping_preference': 'NO_SHIPPING',
            'user_action': 'SUBSCRIBE_NOW'
          }
        });
      },

      onApprove: function(data, actions) {
        // Payment approved, send to success handler
        fetch('{{ route('user.payment.paypal.success') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            orderID: data.orderID,
            subscriptionID: data.subscriptionID
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            window.location.href = '{{ route('user.subscription.dashboard') }}';
          } else {
            alert('Payment verification failed. Please contact support.');
            window.location.href = '{{ route('user.pricing') }}';
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred. Please try again.');
          window.location.href = '{{ route('user.pricing') }}';
        });
      },

      onCancel: function(data) {
        // Payment canceled
        window.location.href = '{{ route('user.payment.paypal.cancel') }}';
      },

      onError: function(err) {
        console.error('PayPal error:', err);
        alert('An error occurred with PayPal. Please try again or use a different payment method.');
        window.location.href = '{{ route('user.pricing') }}';
      }
      
    }).render('#paypal-button-container');
  </script>

</x-layouts.app>