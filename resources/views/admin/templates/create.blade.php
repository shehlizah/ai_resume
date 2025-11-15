<x-layouts.app :title="$title ?? 'Create Template'">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">➕ Create New Template</h5>
      <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary btn-sm">
        ← Back to Templates
      </a>
    </div>

    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('admin.templates.store') }}" enctype="multipart/form-data">
        @csrf

        <!-- Basic Info -->
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="name" class="form-label">Template Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g., Professional Resume Blue">
          </div>

          <div class="col-md-6">
            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
            <select class="form-select" id="category" name="category" required>
              <option value="">Select Category</option>
              @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>
                  {{ ucfirst($cat) }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="mb-4">
          <label for="description" class="form-label">Description</label>
          <textarea class="form-control" id="description" name="description" rows="2" placeholder="Brief description of this template">{{ old('description') }}</textarea>
        </div>

        <!-- HTML/CSS Editor Section -->
        <div class="card bg-primary bg-opacity-10 border-primary mb-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3">
              <i class="bx bx-code-alt text-primary"></i> HTML & CSS Code
            </h6>
            
            <!-- HTML Content -->
            <div class="mb-4">
              <label for="html_content" class="form-label">
                HTML Content <span class="text-danger">*</span>
                <span class="badge bg-primary ms-2">Required</span>
              </label>
              <textarea 
                class="form-control font-monospace" 
                id="html_content" 
                name="html_content" 
                rows="15" 
                required
                style="font-size: 13px; tab-size: 2;"
                placeholder="Enter your HTML template code here...">{{ old('html_content') }}</textarea>
              <small class="text-muted">
                <i class="bx bx-info-circle"></i> Write your resume template HTML structure here
              </small>
            </div>

            <!-- CSS Content -->
            <div class="mb-3">
              <label for="css_content" class="form-label">
                CSS Styles <span class="text-danger">*</span>
                <span class="badge bg-primary ms-2">Required</span>
              </label>
              <textarea 
                class="form-control font-monospace" 
                id="css_content" 
                name="css_content" 
                rows="15" 
                required
                style="font-size: 13px; tab-size: 2;"
                placeholder="Enter your CSS styles here...">{{ old('css_content') }}</textarea>
              <small class="text-muted">
                <i class="bx bx-info-circle"></i> Add custom styling for your template
              </small>
            </div>

            <div class="alert alert-info mb-0">
              <div class="d-flex">
                <i class="bx bx-info-circle fs-4 me-2"></i>
                <div>
                  <strong>How it works:</strong>
                  <ul class="mb-0 mt-2">
                    <li>Write your resume template structure in HTML</li>
                    <li>Style it with custom CSS for a professional look</li>
                    <li>Preview thumbnail will be auto-generated from your HTML/CSS</li>
                    <li>Users will be able to customize and download this template</li>
                    <li>Use placeholders like <code>{{'{{'}}name}}</code>, <code>{{'{{'}}email}}</code>, <code>{{'{{'}}experience}}</code> for dynamic content</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Preview Image (Optional Override) -->
        <div class="mb-4">
          <label for="preview_image" class="form-label">
            Custom Preview Image (Optional)
            <span class="badge bg-info ms-2">Override Auto-Generated</span>
          </label>
          <input type="file" class="form-control" id="preview_image" name="preview_image" accept="image/*">
          <small class="text-muted">
            <i class="bx bx-magic-wand"></i> <strong>Auto-magic:</strong> We'll generate a preview from your HTML/CSS automatically. 
            Upload a custom image here only if you want to override it.
          </small>
        </div>

        <!-- Settings -->
        <div class="card bg-light mb-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bx bx-cog"></i> Template Settings</h6>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="is_premium" name="is_premium" value="1" {{ old('is_premium') ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_premium">
                    <i class="bx bx-crown text-warning"></i> <strong>Premium Template</strong>
                    <br>
                    <small class="text-muted">Mark as premium (optional)</small>
                  </label>
                </div>
              </div>
              
              <div class="col-md-6 mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                  <label class="form-check-label" for="is_active">
                    <i class="bx bx-check-circle text-success"></i> <strong>Active</strong>
                    <br>
                    <small class="text-muted">Make immediately available to users</small>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit -->
        <hr>
        <div class="d-flex justify-content-between align-items-center">
          <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary btn-lg">
            <i class="bx bx-x me-1"></i> Cancel
          </a>
          <button type="submit" class="btn btn-primary btn-lg">
            <i class="bx bx-save me-1"></i> Create Template
          </button>
        </div>
      </form>
    </div>
  </div>

  @push('scripts')
  <script>
    // Add tab support in textareas for better code editing
    document.querySelectorAll('textarea').forEach(textarea => {
      textarea.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
          e.preventDefault();
          const start = this.selectionStart;
          const end = this.selectionEnd;
          this.value = this.value.substring(0, start) + '  ' + this.value.substring(end);
          this.selectionStart = this.selectionEnd = start + 2;
        }
      });
    });
  </script>
  @endpush
</x-layouts.app>