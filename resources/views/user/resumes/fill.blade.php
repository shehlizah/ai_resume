<x-layouts.app :title="$title ?? 'Fill Resume Details'">
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Fill Your Resume Details</h5>
      <a href="{{ route('user.resumes.choose') }}" class="btn btn-secondary btn-sm">
        ← Back
      </a>
    </div>
    <div class="card-body">
      <div class="text-center mb-4">
        @if($template->preview_image)
          <img src="{{ asset($template->preview_image) }}"
               alt="Preview"
               class="img-thumbnail"
               style="max-width: 180px; border-radius: 8px;">
        @else
          <div class="bg-light d-inline-block p-4 rounded">
            <i class="bx bx-file" style="font-size: 48px; color: #ddd;"></i>
          </div>
        @endif
        <h6 class="mt-3">{{ $template->name }}</h6>
        <p class="text-muted small">{{ $template->description }}</p>
      </div>
      
      <!-- Show any errors -->
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bx bx-error-circle me-2"></i>
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      
      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Please fix the following errors:</strong>
          <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      
      <!-- Form (normal submission) -->
      <form method="POST" 
            action="{{ route('user.resumes.generate') }}" 
            id="resumeForm">
        @csrf
        <input type="hidden" name="template_id" value="{{ $template->id }}">
        
        <!-- Personal Info -->
        <div class="card mb-4">
          <div class="card-body">
            <h6 class="mb-3"><i class="bx bx-user"></i> Personal Details</h6>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Full Name *</label>
                <input type="text" 
                       name="name" 
                       class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name') }}"
                       required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Job Title *</label>
                <input type="text" 
                       name="title" 
                       class="form-control @error('title') is-invalid @enderror" 
                       value="{{ old('title') }}"
                       required>
                @error('title')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Email *</label>
                <input type="email" 
                       name="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email') }}"
                       required>
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Phone *</label>
                <input type="text" 
                       name="phone" 
                       class="form-control @error('phone') is-invalid @enderror" 
                       value="{{ old('phone') }}"
                       required>
                @error('phone')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>
        
        <!-- Experience -->
        <div class="card mb-4">
          <div class="card-body">
            <h6 class="mb-3"><i class="bx bx-briefcase"></i> Experience</h6>
            <textarea name="experience" 
                      rows="5" 
                      class="form-control @error('experience') is-invalid @enderror"
                      placeholder="Add your work history...">{{ old('experience') }}</textarea>
            <small class="text-muted">Example: Senior Developer at ABC Corp (2020-Present)</small>
            @error('experience')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        
        <!-- Skills -->
        <div class="card mb-4">
          <div class="card-body">
            <h6 class="mb-3"><i class="bx bx-star"></i> Skills</h6>
            <textarea name="skills" 
                      rows="4" 
                      class="form-control @error('skills') is-invalid @enderror"
                      placeholder="List your skills...">{{ old('skills') }}</textarea>
            <small class="text-muted">Example: PHP, Laravel, Vue.js, MySQL</small>
            @error('skills')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        
        <!-- Education -->
        <div class="card mb-4">
          <div class="card-body">
            <h6 class="mb-3"><i class="bx bx-book"></i> Education</h6>
            <textarea name="education" 
                      rows="4" 
                      class="form-control @error('education') is-invalid @enderror"
                      placeholder="Add your education...">{{ old('education') }}</textarea>
            <small class="text-muted">Example: BS Computer Science, XYZ University (2018)</small>
            @error('education')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        
        <!-- Submit -->
        <div class="alert alert-info">
          <i class="bx bx-info-circle me-2"></i>
          Your resume will be generated and opened in a new tab!
        </div>
        
        <button type="submit" class="btn btn-primary btn-lg w-100" id="generateBtn">
          <i class="bx bx-file me-1"></i> Generate Resume PDF
        </button>
      </form>
    </div>
  </div>

  <!-- Loading overlay -->
  <script>
    document.getElementById('resumeForm').addEventListener('submit', function() {
      const btn = document.getElementById('generateBtn');
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating PDF...';
      btn.disabled = true;
    });
  </script>
</x-layouts.app>