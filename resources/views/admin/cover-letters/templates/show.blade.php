<x-layouts.app :title="$template->name">
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Template: {{ $template->name }}</h4>
            <div class="btn-group">
                <a href="{{ route('admin.cover-letters.templates.edit', $template) }}" class="btn btn-primary">
                    <i class="bx bx-edit me-1"></i> Edit
                </a>
                <a href="{{ route('admin.cover-letters.templates') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Template Content</h5>
                    </div>
                    <div class="card-body">
                        <pre style="white-space: pre-wrap; font-family: monospace; background: #f8f9fa; padding: 20px; border-radius: 8px;">{{ $template->content }}</pre>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Template Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Name</small>
                            <strong>{{ $template->name }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Description</small>
                            <p>{{ $template->description }}</p>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-{{ $template->is_active ? 'success' : 'secondary' }}">
                                {{ $template->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Sort Order</small>
                            <strong>{{ $template->sort_order }}</strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Created</small>
                            <strong>{{ $template->created_at->format('M d, Y') }}</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Last Updated</small>
                            <strong>{{ $template->updated_at->format('M d, Y') }}</strong>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.cover-letters.templates.edit', $template) }}" class="btn btn-primary">
                                <i class="bx bx-edit me-1"></i> Edit Template
                            </a>
                            
                            <form action="{{ route('admin.cover-letters.templates.toggle', $template) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-{{ $template->is_active ? 'warning' : 'success' }} w-100">
                                    <i class="bx {{ $template->is_active ? 'bx-hide' : 'bx-show' }} me-1"></i>
                                    {{ $template->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            <form action="{{ route('admin.cover-letters.templates.delete', $template) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Delete this template?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bx bx-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>