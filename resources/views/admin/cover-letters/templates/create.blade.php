<x-layouts.app :title="'Add Template'">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Add Cover Letter Template</h4>
            <a href="{{ route('admin.cover-letters.templates') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        <form action="{{ route('admin.cover-letters.templates.store') }}" method="POST">
            @csrf

            <div class="card">
                <div class="card-body">
                    
                    <div class="mb-3">
                        <label class="form-label">Template Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" 
                               placeholder="e.g., Professional"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" 
                               value="{{ old('description') }}" 
                               placeholder="e.g., 3-paragraph formal"
                               required>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Template Content <span class="text-danger">*</span></label>
                        <textarea name="content" rows="20" class="form-control @error('content') is-invalid @enderror" 
                                  placeholder="Write the template content here..."
                                  required>{{ old('content') }}</textarea>
                        <small class="text-muted">Use placeholders like [Recipient Name], [Company Name], etc.</small>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active (visible to users)
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Save Template
                    </button>
                    <a href="{{ route('admin.cover-letters.templates') }}" class="btn btn-secondary">
                        Cancel
                    </a>

                </div>
            </div>

        </form>

    </div>
</x-layouts.app>