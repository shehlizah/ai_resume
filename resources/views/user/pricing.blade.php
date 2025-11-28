<x-layouts.app :title="$title ?? 'Pricing Plans'">
  <div class="row g-4">

    <!-- Header -->
    <div class="col-lg-12 text-center mb-4">
      <h2 class="fw-bold mb-2">Simple Plans for Your Career</h2>
      <p class="text-muted mb-0">Choose the plan that fits your needs</p>
    </div>

    <!-- Plans Comparison -->
    <div class="col-lg-12">
      <!-- Pro Plan Details Card -->
      <div class="alert alert-info border-0 mb-4">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h5 class="mb-2"><i class="bx bx-crown me-2"></i>Pro Plan Details</h5>
            <p class="mb-0"><strong>$19.99 USD / Month</strong></p>
            <small class="text-muted">Unlimited access to all premium features. Cancel anytime.</small>
          </div>
          <div class="col-md-4 text-md-end">
            <div class="mb-2">
              <span class="badge bg-info">Monthly Billing</span>
              <span class="badge bg-secondary">Auto-Renewal</span>
            </div>
            <small class="text-muted d-block">Free 7-day trial available</small>
          </div>
        </div>
      </div>

      <!-- What's Included in Pro -->
      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <div class="text-center p-3 bg-light rounded">
            <i class="bx bx-infinity text-primary mb-2" style="font-size: 1.5rem;"></i>
            <h6>Unlimited Everything</h6>
            <small class="text-muted">Resumes, jobs, applications</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="text-center p-3 bg-light rounded">
            <i class="bx bx-bot text-primary mb-2" style="font-size: 1.5rem;"></i>
            <h6>AI Features</h6>
            <small class="text-muted">Unlimited AI assistance</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="text-center p-3 bg-light rounded">
            <i class="bx bx-user-check text-primary mb-2" style="font-size: 1.5rem;"></i>
            <h6>Expert Coaching</h6>
            <small class="text-muted">1-on-1 interview prep</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="text-center p-3 bg-light rounded">
            <i class="bx bx-shield-alt text-primary mb-2" style="font-size: 1.5rem;"></i>
            <h6>Premium Templates</h6>
            <small class="text-muted">All design templates</small>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered border-light">
          <thead class="table-light">
            <tr>
              <th class="px-4 py-3">Feature</th>
              <th class="px-4 py-3 text-center">
                <h5 class="mb-1">Free</h5>
                <small class="text-muted">Get Started</small>
              </th>
              <th class="px-4 py-3 text-center">
                <h5 class="mb-1">Pro</h5>
                <small class="text-muted">$19.99/month</small>
              </th>
            </tr>
          </thead>
          <tbody>
            <!-- Resume Builder -->
            <tr>
              <td class="px-4 py-3">
                <strong>Resume Builder</strong>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
            </tr>
            <tr>
              <td class="px-4 py-3 ps-5"><small>AI Suggestions</small></td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
            </tr>
            <tr>
              <td class="px-4 py-3 ps-5"><small>Basic Templates</small></td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
            </tr>
            <tr>
              <td class="px-4 py-3 ps-5"><small>Premium Templates</small></td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-x text-danger" style="font-size: 1.5rem;"></i>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
            </tr>
            <tr>
              <td class="px-4 py-3 ps-5"><small>Resume Scoring</small></td>
              <td class="px-4 py-3 text-center">
                <span class="badge bg-light text-dark">Basic</span>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="badge bg-success">Advanced</span>
              </td>
            </tr>

            <!-- Cover Letters -->
            <tr>
              <td class="px-4 py-3">
                <strong>Cover Letters</strong>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
            </tr>
            <tr>
              <td class="px-4 py-3 ps-5"><small>AI-Powered Generator</small></td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
            </tr>

            <!-- Job Finder -->
            <tr>
              <td class="px-4 py-3">
                <strong>Job Finder</strong>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="badge bg-light text-dark">Limited</span>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="badge bg-success">Unlimited</span>
              </td>
            </tr>
            <tr>
              <td class="px-4 py-3 ps-5"><small>Recommended Jobs</small></td>
              <td class="px-4 py-3 text-center">
                <small class="text-muted">5 views/day</small>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
            </tr>
            <tr>
              <td class="px-4 py-3 ps-5"><small>Search by Location</small></td>
              <td class="px-4 py-3 text-center">
                <small class="text-muted">5 views/day</small>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
            </tr>
            <tr>
              <td class="px-4 py-3 ps-5"><small>Job Applications</small></td>
              <td class="px-4 py-3 text-center">
                <small class="text-muted">1/day</small>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
            </tr>

            <!-- Interview Preparation -->
            <tr>
              <td class="px-4 py-3">
                <strong>Interview Preparation</strong>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="badge bg-light text-dark">Basic</span>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="badge bg-success">Full Access</span>
              </td>
            </tr>
            <tr>
              <td class="px-4 py-3 ps-5"><small>Practice Questions</small></td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
            </tr>
            <tr>
              <td class="px-4 py-3 ps-5"><small>AI Mock Interview</small></td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-x text-danger" style="font-size: 1.5rem;"></i>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
            </tr>
            <tr>
              <td class="px-4 py-3 ps-5"><small>AI Scoring & Feedback</small></td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-x text-danger" style="font-size: 1.5rem;"></i>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
            </tr>
            <tr>
              <td class="px-4 py-3 ps-5"><small>Expert Interview Sessions</small></td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-x text-danger" style="font-size: 1.5rem;"></i>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success" style="font-size: 1.5rem;"></i>
              </td>
            </tr>

            <!-- Other Features -->
            <tr>
              <td class="px-4 py-3">
                <strong>Other</strong>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="badge bg-light text-dark">Limited</span>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="badge bg-success">Premium</span>
              </td>
            </tr>
            <tr>
              <td class="px-4 py-3 ps-5"><small>Ads</small></td>
              <td class="px-4 py-3 text-center">
                <small class="text-muted">Shown</small>
              </td>
              <td class="px-4 py-3 text-center">
                <i class="bx bx-check text-success"></i> <small>Ad-Free</small>
              </td>
            </tr>
            <tr class="table-active">
              <td class="px-4 py-3">
                <strong></strong>
              </td>
              <td class="px-4 py-3 text-center">
                <strong>Free</strong>
              </td>
              <td class="px-4 py-3 text-center">
                <strong>$19.99/month</strong>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- CTA Buttons -->
    <div class="col-lg-12 mt-4">
      <div class="row g-3">
        <!-- Back to Dashboard -->
        <div class="col-lg-12 text-center mb-4">
          <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary btn-lg">
            <i class="bx bx-home me-2"></i> Stay on Free Plan
          </a>
        </div>

        <!-- Payment Information Section -->
        <div class="col-lg-12 mb-4">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
              <h6 class="mb-0">
                <i class="bx bx-info-circle me-1"></i> Payment Information
              </h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <h6 class="mb-2">Test Card (For Testing)</h6>
                  <div class="p-3 bg-light rounded">
                    <p class="mb-1"><strong>Card Number:</strong></p>
                    <code class="d-block mb-3">4242 4242 4242 4242</code>

                    <p class="mb-1"><strong>Expiry Date:</strong></p>
                    <code class="d-block mb-3">12/25</code>

                    <p class="mb-1"><strong>CVC:</strong></p>
                    <code class="d-block mb-3">123</code>

                    <p class="mb-0"><strong>Postal Code:</strong></p>
                    <code>12345</code>
                  </div>
                  <small class="text-warning d-block mt-2">
                    <i class="bx bx-test-tube me-1"></i>Only works in TEST mode
                  </small>
                </div>

                <div class="col-md-6">
                  <h6 class="mb-2">Billing Details</h6>
                  <div class="p-3 bg-light rounded">
                    <div class="mb-2">
                      <strong>Monthly Price:</strong>
                      <span class="float-end text-primary fw-bold">$19.99</span>
                    </div>
                    <hr class="my-2">
                    <div class="mb-2">
                      <strong>Billing Frequency:</strong>
                      <span class="float-end">Monthly (auto-renewal)</span>
                    </div>
                    <hr class="my-2">
                    <div class="mb-2">
                      <strong>Trial Period:</strong>
                      <span class="float-end">7 days (if available)</span>
                    </div>
                    <hr class="my-2">
                    <div>
                      <strong>Next Charge:</strong>
                      <span class="float-end">After trial or immediately</span>
                    </div>
                  </div>
                  <small class="text-muted d-block mt-2">
                    <i class="bx bx-check me-1"></i>Cancel anytime from settings
                  </small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Testing & Production Pricing Tables -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning bg-opacity-10 border-0">
              <h6 class="mb-0">
                <i class="bx bx-test-tube me-1"></i> TEST MODE
              </h6>
              <small class="text-muted">Use test card: 4242 4242 4242 4242</small>
            </div>
            <div class="card-body p-0">
              <script async src="https://js.stripe.com/v3/pricing-table.js"></script>
              <stripe-pricing-table pricing-table-id="prctbl_1STdoCDfpo67wO4dX6Kr1bDX"
              publishable-key="pk_test_51Ngsx6Dfpo67wO4dIywhaxJ5DczHuc6BZdgestz0BZHLcG85vD3QpmNYWEYmTmD2qwMMj0UEonJnwdcoiY6J6mHg006USXIY5Y">
              </stripe-pricing-table>
            </div>
          </div>
        </div>

        <!-- Live Pricing -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-success bg-opacity-10 border-0">
              <h6 class="mb-0">
                <i class="bx bx-check-circle me-1"></i> PRODUCTION
              </h6>
              <small class="text-muted">Real payment processing</small>
            </div>
            <div class="card-body p-0">
              <script async src="https://js.stripe.com/v3/pricing-table.js"></script>
              <stripe-pricing-table pricing-table-id="prctbl_1STdVTDfpo67wO4diuVN6NRA"
              publishable-key="pk_live_51Ngsx6Dfpo67wO4dGZRpVZ1UpDMKFoaY1tFikV4ToYcbvkSBxzb2bNpDqyuvrjADJuJ18AbdZMbVIv1Fm4ufsEqr00iCQyBPdH">
              </stripe-pricing-table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .table-bordered td {
      border-color: #e9ecef;
    }

    .table-light {
      background-color: #f8f9fa;
    }

    .badge-sm {
      font-size: 0.75rem;
    }
  </style>
</x-layouts.app>
