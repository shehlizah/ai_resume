<x-layouts.app :title="$title ?? 'Edit Template'">
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">✏️ Edit Template: {{ $template->name }}</h5>
          <div class="d-flex gap-2">
            <a href="{{ route('admin.templates.preview', $template->id) }}" 
               class="btn btn-info btn-sm" 
               target="_blank">
              <i class="bx bx-show me-1"></i> Full Preview
            </a>
            <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary btn-sm">
              ← Back
            </a>
          </div>
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

          <form method="POST" action="{{ route('admin.templates.update', $template->id) }}" enctype="multipart/form-data" id="templateForm">
            @csrf
            @method('PUT')

            <!-- Basic Info Section -->
            <div class="row mb-4">
              <div class="col-md-6">
                <label for="name" class="form-label">Template Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $template->name) }}" required>
              </div>

              <div class="col-md-6">
                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                <select class="form-select" id="category" name="category" required>
                  @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ old('category', $template->category) == $cat ? 'selected' : '' }}>
                      {{ ucfirst($cat) }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="row mb-4">
              <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $template->description) }}</textarea>
              </div>
            </div>

            <!-- Settings -->
            <div class="row mb-4">
              <div class="col-md-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="is_premium" name="is_premium" value="1" 
                         {{ old('is_premium', $template->is_premium) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_premium">
                    <i class="bx bx-crown text-warning"></i> Premium Template
                  </label>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                         {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_active">
                    <i class="bx bx-check-circle text-success"></i> Active
                  </label>
                </div>
              </div>
            </div>

            <!-- Preview Image Upload -->
            <div class="row mb-4">
              <div class="col-md-6">
                <label for="preview_image" class="form-label">Preview Image</label>
                <input type="file" class="form-control" id="preview_image" name="preview_image" accept="image/*">
                @if($template->preview_image)
                  <div class="mt-2">
                    <img src="{{ asset('storage/'.$template->preview_image) }}" alt="Current preview" style="max-width: 200px; border-radius: 8px;">
                  </div>
                @endif
              </div>
            </div>

            <hr class="my-4">

            <!-- Editor Tabs -->
            <ul class="nav nav-tabs mb-3" role="tablist">
              <li class="nav-item">
                <button class="nav-link active" id="html-tab" data-bs-toggle="tab" data-bs-target="#html-editor" type="button">
                  <i class="bx bx-code-alt me-1"></i> HTML Editor
                </button>
              </li>
              <li class="nav-item">
                <button class="nav-link" id="css-tab" data-bs-toggle="tab" data-bs-target="#css-editor" type="button">
                  <i class="bx bx-paint me-1"></i> CSS Editor
                </button>
              </li>
              <li class="nav-item">
                <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#file-upload" type="button">
                  <i class="bx bx-upload me-1"></i> Upload Files
                </button>
              </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
              <!-- HTML Editor Tab -->
              <div class="tab-pane fade show active" id="html-editor">
                <div class="row">
                  <div class="col-lg-6">
                    <label class="form-label fw-bold">HTML Code</label>
                    <!--<textarea id="htmlCode" name="html_code" class="form-control code-editor" rows="20">{{ old('html_code', $htmlContent) }}</textarea>-->
                    <textarea id="htmlCode" name="html_code" class="form-control" rows="20">{{ $htmlContent ?? '' }}</textarea>
                    
                  </div>
                  
                  <div class="col-lg-6">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <label class="form-label fw-bold mb-0">Live Preview</label>
                      <button type="button" class="btn btn-sm btn-primary" onclick="updatePreview()">
                        <i class="bx bx-refresh me-1"></i> Refresh
                      </button>
                    </div>
                    <div id="livePreview" class="preview-container">
                      <!-- Preview will appear here -->
                    </div>
                  </div>
                </div>
              </div>

              <!-- CSS Editor Tab -->
              <div class="tab-pane fade" id="css-editor">
                <div class="row">
                  <div class="col-lg-6">
                    <label class="form-label fw-bold">CSS Code</label>
                                        <textarea id="css_code" name="css_code" class="form-control" rows="20">{{ $cssContent ?? '' }}</textarea>

                    <!--<textarea id="cssCode" name="css_code" class="form-control code-editor" rows="20">{{ old('css_code', $cssContent) }}</textarea>-->
                  </div>
                  <div class="col-lg-6">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <label class="form-label fw-bold mb-0">Live Preview</label>
                      <button type="button" class="btn btn-sm btn-primary" onclick="updatePreview()">
                        <i class="bx bx-refresh me-1"></i> Refresh
                      </button>
                    </div>
                    <div id="cssLivePreview" class="preview-container">
                      <!-- Preview will appear here -->
                    </div>
                  </div>
                </div>
              </div>

              <!-- File Upload Tab -->
              <div class="tab-pane fade" id="file-upload">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="template_file" class="form-label">Upload HTML File</label>
                    <input type="file" class="form-control" id="template_file" name="template_file" accept=".html,.htm">
                    @if($template->template_file)
                      <small class="text-muted d-block mt-2">
                        <i class="bx bx-check-circle text-success"></i> Current: {{ basename($template->template_file) }}
                      </small>
                    @endif
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="css_file" class="form-label">Upload CSS File</label>
                    <input type="file" class="form-control" id="css_file" name="css_file" accept=".css">
                    @if($template->css_file)
                      <small class="text-muted d-block mt-2">
                        <i class="bx bx-check-circle text-success"></i> Current: {{ basename($template->css_file) }}
                      </small>
                    @endif
                  </div>
                </div>
                <div class="alert alert-info">
                  <i class="bx bx-info-circle me-1"></i>
                  <strong>Note:</strong> Uploaded files will override the code in the editor tabs. You can also edit the code directly in the editor tabs instead of uploading files.
                </div>
              </div>
            </div>

            <hr class="my-4">

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-between">
              <a href="{{ route('admin.templates.index') }}" class="btn btn-label-secondary">
                <i class="bx bx-x me-1"></i> Cancel
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bx bx-save me-1"></i> Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript for Live Preview -->
  <script>
    let htmlCode = document.getElementById('htmlCode');
    let cssCode = document.getElementById('cssCode');
    let livePreview = document.getElementById('livePreview');
    let cssLivePreview = document.getElementById('cssLivePreview');

    // Update preview function
    function updatePreview() {
      const html = htmlCode.value;
      const css = cssCode.value;
      
      // Create combined HTML with CSS
      const combinedHTML = `
        <style>${css}</style>
        ${html}
      `;
      
      // Update both preview panels
      livePreview.innerHTML = combinedHTML;
      cssLivePreview.innerHTML = combinedHTML;
      
      console.log('Preview updated!');
    }

    // Auto-update preview on typing (with debounce)
    let debounceTimer;
    function debounceUpdate() {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(updatePreview, 1000);
    }

    htmlCode.addEventListener('input', debounceUpdate);
    cssCode.addEventListener('input', debounceUpdate);

    // Initial preview load
    document.addEventListener('DOMContentLoaded', function() {
      updatePreview();
    });

    // Tab switching also updates preview
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(button => {
      button.addEventListener('shown.bs.tab', function() {
        updatePreview();
      });
    });

    // Handle file uploads - load content into editors
    document.getElementById('template_file').addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
          htmlCode.value = event.target.result;
          updatePreview();
          
          // Show success message
          alert('HTML file loaded into editor! You can now edit it.');
        };
        reader.readAsText(file);
      }
    });

    document.getElementById('css_file').addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
          cssCode.value = event.target.result;
          updatePreview();
          
          // Show success message
          alert('CSS file loaded into editor! You can now edit it.');
        };
        reader.readAsText(file);
      }
    });
  </script>

  <style>
    .code-editor {
      font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
      font-size: 13px;
      line-height: 1.6;
      resize: vertical;
    }
    
    .preview-container {
      border: 1px solid #d9dee3;
      padding: 20px;
      min-height: 400px;
      max-height: 600px;
      overflow-y: auto;
      background: white;
      box-shadow: inset 0 0 5px rgba(0,0,0,0.05);
      border-radius: 8px;
    }
    
    .nav-tabs .nav-link {
      cursor: pointer;
    }
    
    .nav-tabs .nav-link.active {
      font-weight: 600;
    }
  </style>
</x-layouts.app>