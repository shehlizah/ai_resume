<x-layouts.app :title="'Resume Generated Successfully'">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-body text-center py-5">
          <!-- Success Icon -->
          <div class="mb-4">
            <i class="bx bx-check-circle text-success" style="font-size: 80px;"></i>
          </div>
          
          <!-- Success Message -->
          <h3 class="mb-3">Resume Generated Successfully! 🎉</h3>
          <p class="text-muted mb-4">
            Your resume has been created and saved. The PDF should open automatically in a new tab.
          </p>
          
          <!-- Resume Info -->
          <div class="card bg-light mb-4">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 text-start">
                  <small class="text-muted">Name:</small>
                  <div class="fw-semibold">{{ json_decode($resume->data)->name ?? 'N/A' }}</div>
                </div>
                <div class="col-md-6 text-start">
                  <small class="text-muted">Template:</small>
                  <div class="fw-semibold">{{ $resume->template->name }}</div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="d-grid gap-3 d-md-flex justify-content-md-center mb-4">
            <a href="{{ route('user.resumes.view', $resume->id) }}" 
               target="_blank"
               class="btn btn-primary btn-lg">
              <i class="bx bx-show me-2"></i> View PDF
            </a>
            <a href="{{ route('user.resumes.download', $resume->id) }}" 
               class="btn btn-outline-primary btn-lg">
              <i class="bx bx-download me-2"></i> Download PDF
            </a>
          </div>
          
          <!-- Quick Links -->
          <div class="border-top pt-4 mt-4">
            <p class="text-muted mb-3">What would you like to do next?</p>
            <div class="d-flex flex-wrap gap-3 justify-content-center">
              <a href="{{ route('user.resumes.fill', $resume->template_id) }}" 
                 class="btn btn-outline-secondary">
                <i class="bx bx-copy me-1"></i> Create Similar Resume
              </a>
              <a href="{{ route('user.resumes.choose') }}" 
                 class="btn btn-outline-secondary">
                <i class="bx bx-plus me-1"></i> Choose Another Template
              </a>
              <a href="{{ route('user.resumes.index') }}" 
                 class="btn btn-outline-secondary">
                <i class="bx bx-folder me-1"></i> View All Resumes
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Helpful Tip -->
      <div class="alert alert-info mt-3">
        <i class="bx bx-info-circle me-2"></i>
        <strong>Tip:</strong> If the PDF didn't open automatically, click the "View PDF" button above or check if your browser blocked the popup.
      </div>
    </div>
  </div>

  <!-- Auto-open PDF in new tab -->
  <script>
    // Wait for page to load, then open PDF
    window.addEventListener('load', function() {
      // Small delay to ensure everything is ready
      setTimeout(function() {
        window.open("{{ route('user.resumes.view', $resume->id) }}", '_blank');
      }, 500);
    });
  </script>
</x-layouts.app>