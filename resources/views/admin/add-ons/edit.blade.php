<x-layouts.app :title="'Edit Add-On'">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="bx bx-edit me-2"></i> Edit Add-On
            </h4>
            <a href="{{ route('admin.add-ons.show', $addOn) }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.add-ons.update', $addOn) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Same form fields as create, but with values from $addOn -->
                    <div class="row">
                        <div class="col-lg-8">
                            <h5 class="mb-3">Basic Information</h5>

                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $addOn->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" 
                                       value="{{ old('slug', $addOn->slug) }}" required>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $addOn->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="price" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                               value="{{ old('price', $addOn->price) }}" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Type <span class="text-danger">*</span></label>
                                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="job_links" {{ old('type', $addOn->type) === 'job_links' ? 'selected' : '' }}>Job Links</option>
                                        <option value="interview_prep" {{ old('type', $addOn->type) === 'interview_prep' ? 'selected' : '' }}>Interview Preparation</option>
                                        <option value="custom" {{ old('type', $addOn->type) === 'custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Icon (BoxIcons class)</label>
                                <input type="text" name="icon" class="form-control" 
                                       value="{{ old('icon', $addOn->icon) }}" 
                                       placeholder="e.g., bx-briefcase">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Features (one per line)</label>
                                <textarea name="features_text" rows="6" class="form-control">{{ old('features_text', $addOn->features ? implode("\n", $addOn->features) : '') }}</textarea>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <h5 class="mb-3">Settings</h5>

                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                               {{ old('is_active', $addOn->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Sort Order</label>
                                        <input type="number" name="sort_order" class="form-control" 
                                               value="{{ old('sort_order', $addOn->sort_order) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Update Add-On
                        </button>
                        <a href="{{ route('admin.add-ons.show', $addOn) }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>