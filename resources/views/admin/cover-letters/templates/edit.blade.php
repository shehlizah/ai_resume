<x-layouts.app :title="'Edit Template'">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Edit Template</h4>
            <a href="{{ route('admin.cover-letters.templates') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        <form action="{{ route('admin.cover-letters.templates.update', $template) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-body">
                    
                    <div class="mb-3">
                        <label class="form-label">Template Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" 
                               value="{{ old('name', $template->name) }}" 
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control" 
                               value="{{ old('description', $template->description) }}" 
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Template Content <span class="text-danger">*</span></label>
                        <textarea name="content" rows="20" class="form-control" 
                                  required>{{ old('content', $template->content) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" 
                               value="{{ old('sort_order', $template->sort_order) }}">
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                   {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active (visible to users)
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Update Template
                    </button>
                    <a href="{{ route('admin.cover-letters.templates') }}" class="btn btn-secondary">
                        Cancel
                    </a>

                </div>
            </div>

        </form>

    </div>
</x-layouts.app>