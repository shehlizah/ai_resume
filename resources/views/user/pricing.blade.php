<x-layouts.app :title="$title ?? 'Pricing Plans'">
  <div class="row g-4">

    <!-- Header -->
    <div class="col-lg-12 text-center mb-4">
      <h2 class="fw-bold mb-2">Simple Plans for Your Career</h2>
      <p class="text-muted mb-0">Choose the plan that fits your needs</p>
    </div>

    <!-- Plans Comparison -->
    <div class="col-lg-12">
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
    <div class="col-lg-12 text-center mt-4">
      <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary btn-lg me-3">
        <i class="bx bx-home me-2"></i> Stay on Free Plan
      </a>
      <script async src="https://js.stripe.com/v3/pricing-table.js"></script>
      <stripe-pricing-table pricing-table-id="prctbl_1STdVTDfpo67wO4diuVN6NRA"
      publishable-key="pk_live_51Ngsx6Dfpo67wO4dGZRpVZ1UpDMKFoaY1tFikV4ToYcbvkSBxzb2bNpDqyuvrjADJuJ18AbdZMbVIv1Fm4ufsEqr00iCQyBPdH">
      </stripe-pricing-table>
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
