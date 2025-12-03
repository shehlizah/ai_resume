<x-layouts.app :title="$title ?? 'Edit Template'">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">✏️ Edit Template: {{ $template->name }}</h5>
      <div class="d-flex gap-2">
        <a href="{{ route('admin.templates.preview', $template->id) }}" class="btn btn-info btn-sm" target="_blank">
          <i class="bx bx-show me-1"></i> Preview Template
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

      <form method="POST" action="{{ route('admin.templates.update', $template->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Basic Info -->
        <div class="row mb-3">
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

        <div class="mb-4">
          <label for="description" class="form-label">Description</label>
          <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $template->description) }}</textarea>
        </div>

        <!-- HTML/CSS Editor Section -->
        <div class="card bg-primary bg-opacity-10 border-primary mb-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3">
              <i class="bx bx-code-alt text-primary"></i> HTML & CSS Code
            </h6>

            <!-- Current Template Info -->
            <div class="alert alert-success mb-3">
              <div class="d-flex align-items-center">
                <i class="bx bx-check-circle fs-4 me-2"></i>
                <div class="flex-grow-1">
                  <strong>Template Active</strong>
                  <br>
                  <small class="text-muted">
                    Last Updated: {{ $template->updated_at->format('M d, Y h:i A') }}
                  </small>
                </div>
                <a href="{{ route('admin.templates.preview', $template->id) }}" class="btn btn-sm btn-success" target="_blank">
                  <i class="bx bx-show"></i> Quick Preview
                </a>
              </div>
            </div>

            <!-- HTML Content -->
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <label for="html_content" class="form-label mb-0">
                  HTML Content <span class="text-danger">*</span>
                </label>
              </div>
              <textarea
                class="form-control font-monospace"
                id="html_content"
                name="html_content"
                rows="15"
                required
                style="font-size: 13px; tab-size: 2;"
                placeholder="Enter your HTML template code here...">{{ old('html_content', $template->html_content) }}</textarea>
              <small class="text-muted">
                <i class="bx bx-info-circle"></i> Edit your resume template HTML structure
              </small>
            </div>

            <!-- CSS Content -->
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <label for="css_content" class="form-label mb-0">
                  CSS Styles <span class="text-danger">*</span>
                </label>
              </div>
              <textarea
                class="form-control font-monospace"
                id="css_content"
                name="css_content"
                rows="15"
                required
                style="font-size: 13px; tab-size: 2;"
                placeholder="Enter your CSS styles here...">{{ old('css_content', $template->css_content) }}</textarea>
              <small class="text-muted">
                <i class="bx bx-info-circle"></i> Customize the styling for your template
              </small>
            </div>

            <div class="alert alert-info mb-0">
              <div class="d-flex">
                <i class="bx bx-info-circle fs-4 me-2"></i>
                <div>
                  <strong>Template Variables:</strong>
                  <p class="mb-1 mt-2">Use these placeholders in your HTML for dynamic content:</p>
                  <code class="d-block mb-1">{{'{{'}}name}}, {{'{{'}}email}}, {{'{{'}}phone}}, {{'{{'}}address}}</code>
                  <code class="d-block mb-1">{{'{{'}}summary}}, {{'{{'}}experience}}, {{'{{'}}education}}, {{'{{'}}skills}}</code>
                  <small class="text-muted">These will be replaced with actual user data when the template is used.</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Preview Image Section -->
        <div class="card bg-light mb-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3">
              <i class="bx bx-image text-info"></i> Preview Thumbnail
            </h6>

            <!-- Current Preview Status -->
            @if($template->preview_image && Storage::exists($template->preview_image))
              <div class="mb-3">
                <div class="d-flex align-items-start gap-3">
                  <img src="{{ asset($template->preview_image) }}"
                       alt="Current preview"
                       class="img-thumbnail"
                       style="max-width: 200px; max-height: 280px; object-fit: cover;">

                  <div class="flex-grow-1">
                    @php
                      $isAutoGenerated = str_contains($template->preview_image, 'preview_');
                    @endphp

                    @if($isAutoGenerated)
                      <span class="badge bg-info mb-2">
                        <i class="bx bx-magic-wand"></i> Auto-Generated from HTML/CSS
                      </span>
                      <p class="text-muted small mb-0">
                        This preview was automatically generated from your template's HTML/CSS.
                      </p>
                    @else
                      <span class="badge bg-secondary mb-2">
                        <i class="bx bx-image-alt"></i> Custom Uploaded Image
                      </span>
                      <p class="text-muted small mb-2">
                        You're using a custom preview image.
                      </p>

                      <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="remove_custom_preview" name="remove_custom_preview" value="1">
                        <label class="form-check-label text-danger" for="remove_custom_preview">
                          <i class="bx bx-trash"></i> Remove custom image & use auto-generated preview
                        </label>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            @else
              <div class="alert alert-secondary mb-3">
                <i class="bx bx-image"></i> No preview image available - one will be auto-generated on save
              </div>
            @endif

            <!-- Upload Custom Preview -->
            <div class="mb-0">
              <label for="preview_image" class="form-label">
                Upload Custom Preview Image (Optional)
                <span class="badge bg-warning text-dark ms-2">Override Auto-Generated</span>
              </label>
              <input type="file" class="form-control" id="preview_image" name="preview_image" accept="image/*">
              <small class="text-muted">
                <i class="bx bx-info-circle"></i> Upload a custom image to override the auto-generated preview. Leave empty to keep current.
              </small>
            </div>
          </div>
        </div>

        <!-- Settings -->
        <div class="card bg-light mb-4">
          <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bx bx-cog"></i> Template Settings</h6>

            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="is_premium" name="is_premium" value="1"
                         {{ old('is_premium', $template->is_premium) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_premium">
                    <i class="bx bx-crown text-warning"></i> <strong>Premium Template</strong>
                    <br>
                    <small class="text-muted">Mark as premium (optional)</small>
                  </label>
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                         {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_active">
                    <i class="bx bx-check-circle text-success"></i> <strong>Active</strong>
                    <br>
                    <small class="text-muted">Make available to users</small>
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
            <i class="bx bx-save me-1"></i> Save Changes
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
