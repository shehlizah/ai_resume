<x-layouts.app :title="$title ?? 'Add Template'">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">{{ $title }}</h5>
      <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
    <div class="card-body">
      <form action="{{ route('admin.templates.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
          <!-- Template Name -->
          <div class="col-md-6 mb-3">
            <label for="name" class="form-label">Template Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                   id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Category -->
          <div class="col-md-6 mb-3">
            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
            <select class="form-select @error('category') is-invalid @enderror" 
                    id="category" name="category" required>
              <option value="">Select Category</option>
              @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>
                  {{ ucfirst($cat) }}
                </option>
              @endforeach
            </select>
            @error('category')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <!-- Description -->
        <div class="mb-3">
          <label for="description" class="form-label">Description</label>
          <textarea class="form-control @error('description') is-invalid @enderror" 
                    id="description" name="description" rows="3">{{ old('description') }}</textarea>
          @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="row">
          <!-- Preview Image -->
          <div class="col-md-4 mb-3">
            <label for="preview_image" class="form-label">Preview Image</label>
            <input type="file" class="form-control @error('preview_image') is-invalid @enderror" 
                   id="preview_image" name="preview_image" accept="image/*">
            <small class="text-muted">JPG, PNG, WEBP (Max 2MB)</small>
            @error('preview_image')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Template HTML File -->
          <div class="col-md-4 mb-3">
            <label for="template_file" class="form-label">HTML Template File</label>
            <input type="file" class="form-control @error('template_file') is-invalid @enderror" 
                   id="template_file" name="template_file" accept=".html,.htm">
            <small class="text-muted">HTML file (Max 1MB)</small>
            @error('template_file')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- CSS File -->
          <div class="col-md-4 mb-3">
            <label for="css_file" class="form-label">CSS File</label>
            <input type="file" class="form-control @error('css_file') is-invalid @enderror" 
                   id="css_file" name="css_file" accept=".css">
            <small class="text-muted">CSS file (Max 512KB)</small>
            @error('css_file')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="row">
          <!-- Version -->
          <div class="col-md-3 mb-3">
            <label for="version" class="form-label">Version</label>
            <input type="text" class="form-control @error('version') is-invalid @enderror" 
                   id="version" name="version" value="{{ old('version', '1.0') }}">
            @error('version')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Sort Order -->
          <div class="col-md-3 mb-3">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
            @error('sort_order')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Premium Status -->
          <div class="col-md-3 mb-3">
            <label class="form-label d-block">Status</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="is_premium" 
                     name="is_premium" value="1" {{ old('is_premium') ? 'checked' : '' }}>
              <label class="form-check-label" for="is_premium">Premium</label>
            </div>
          </div>

          <!-- Active Status -->
          <div class="col-md-3 mb-3">
            <label class="form-label d-block">&nbsp;</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="is_active" 
                     name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">Active</label>
            </div>
          </div>
        </div>

        <!-- Features (Multi-select tags) -->
        <div class="mb-4">
          <label class="form-label">Features (Optional)</label>
          <div class="row">
            @php
              $availableFeatures = ['ATS-Friendly', 'Single Page', 'Two Pages', 'Colorful', 'Minimalist', 'Photo Support', 'Icon Support'];
            @endphp
            @foreach($availableFeatures as $feature)
              <div class="col-md-3 mb-2">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="features[]" 
                         value="{{ Str::slug($feature) }}" id="feature_{{ Str::slug($feature) }}">
                  <label class="form-check-label" for="feature_{{ Str::slug($feature) }}">
                    {{ $feature }}
                  </label>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Create Template</button>
          <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</x-layouts.app>