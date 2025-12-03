<x-layouts.app :title="$title ?? 'Templates'">
  <!-- Success Message -->
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">All Templates</h5>
      <div class="d-flex gap-2">
        <!--<button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#starterLibraryModal">-->
        <!--  <i class="bx bx-library me-1"></i> Browse Starter Library-->
        <!--</button>-->
        <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
          <i class="bx bx-plus me-1"></i> Add New Template
        </a>
      </div>
    </div>

    <!-- Filter/Stats Bar -->
    <div class="card-body border-bottom">
      <div class="row g-3 align-items-center">
        <div class="col-md-3">
          <div class="text-center">
            <h4 class="mb-0">{{ $templates->count() }}</h4>
            <small class="text-muted">Total Templates</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="text-center">
            <h4 class="mb-0 text-success">{{ $templates->where('is_active', true)->count() }}</h4>
            <small class="text-muted">Active</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="text-center">
            <h4 class="mb-0 text-warning">{{ $templates->where('is_premium', true)->count() }}</h4>
            <small class="text-muted">Premium</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="text-center">
            <h4 class="mb-0 text-info">{{ $templates->where('is_premium', false)->count() }}</h4>
            <small class="text-muted">Free</small>
          </div>
        </div>
      </div>
    </div>

    <div class="card-body">
      @if($templates->isEmpty())
        <div class="text-center py-5">
          <i class="bx bx-file display-1 text-muted"></i>
          <p class="text-muted mt-3 mb-4">No templates available yet.</p>
          <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
            Create Your First Template
          </a>
        </div>
      @else
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th style="width: 80px;">Preview</th>
                <th>Name</th>
                <th>Category</th>
                <th>Type</th>
                <th>Status</th>
                <th>Version</th>
                <th style="width: 200px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($templates as $template)
                <tr>
                  <td>
                    @if($template->preview_image)
                      <img src="{{ asset($template->preview_image) }}"
                           alt="{{ $template->name }}"
                           class="img-thumbnail"
                           style="width: 60px; height: 80px; object-fit: cover;">
                    @else
                      <div class="bg-light d-flex align-items-center justify-content-center"
                           style="width: 60px; height: 80px;">
                        <i class="bx bx-image text-muted"></i>
                      </div>
                    @endif
                  </td>
                  <td>
                    <strong>{{ $template->name }}</strong>
                    @if($template->description)
                      <br><small class="text-muted">{{ Str::limit($template->description, 50) }}</small>
                    @endif
                  </td>
                  <td>
                    <span class="badge bg-label-secondary">{{ ucfirst($template->category) }}</span>
                  </td>
                  <td>
                    @if($template->is_premium)
                      <span class="badge bg-label-warning">
                        <i class="bx bx-crown"></i> Premium
                      </span>
                    @else
                      <span class="badge bg-label-success">Free</span>
                    @endif
                  </td>
                  <td>
                    @if($template->is_active)
                      <span class="badge bg-label-success">Active</span>
                    @else
                      <span class="badge bg-label-secondary">Inactive</span>
                    @endif
                  </td>
                  <!--<td>-->
                  <!--  <span class="badge bg-label-info">{{ $template->sort_order }}</span>-->
                  <!--</td>-->
                  <td>
                    <small class="text-muted">v{{ $template->version }}</small>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-horizontal-rounded me-1"></i> Manage
                      </button>

                      <div class="dropdown-menu dropdown-menu-end">
                        <!-- PREVIEW BUTTON - NEW! -->
                        <a class="dropdown-item" href="{{ route('admin.templates.preview', $template->id) }}" target="_blank">
                          <i class="bx bx-show me-1"></i> Preview
                        </a>

                        <!-- Edit -->
                        <a class="dropdown-item" href="{{ route('admin.templates.edit', $template->id) }}">
                          <i class="bx bx-edit me-1"></i> Edit
                        </a>

                        <!-- Toggle Active/Inactive -->
                        <form action="{{ route('admin.templates.toggle-active', $template->id) }}"
                              method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="dropdown-item">
                            <i class="bx {{ $template->is_active ? 'bx-hide' : 'bx-show' }} me-1"></i>
                            {{ $template->is_active ? 'Deactivate' : 'Activate' }}
                          </button>
                        </form>

                        <!-- Duplicate -->
                        <form action="{{ route('admin.templates.duplicate', $template->id) }}"
                              method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="dropdown-item">
                            <i class="bx bx-copy me-1"></i> Duplicate
                          </button>
                        </form>

                        <div class="dropdown-divider"></div>

                        <!-- Delete -->
                        <form action="{{ route('admin.templates.destroy', $template->id) }}"
                              method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this template?')"
                              class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="dropdown-item text-danger">
                            <i class="bx bx-trash me-1"></i> Delete
                          </button>
                        </form>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>

  <!-- Starter Library Modal -->
  <div class="modal fade" id="starterLibraryModal" tabindex="-1" aria-labelledby="starterLibraryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="starterLibraryModalLabel">
            <i class="bx bx-library me-2"></i>Template Starter Library
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted mb-4">Choose a professional template to start with. You can customize it after importing.</p>

          <div class="row g-4" id="starterTemplates">
            <!-- Loading state -->
            <div class="col-12 text-center" id="loadingStarters">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Template Preview Modal -->
  <div class="modal fade" id="templatePreviewModal" tabindex="-1" aria-labelledby="templatePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="templatePreviewModalLabel">Template Preview</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <iframe id="previewFrame" style="width: 100%; height: 600px; border: 1px solid #e2e8f0; border-radius: 8px;"></iframe>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="cloneFromPreview">
            <i class="bx bx-copy me-1"></i> Clone This Template
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Clone Template Form Modal -->
  <div class="modal fade" id="cloneTemplateModal" tabindex="-1" aria-labelledby="cloneTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="cloneTemplateForm" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="cloneTemplateModalLabel">Clone Template</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="cloneTemplateName" class="form-label">New Template Name</label>
              <input type="text" class="form-control" id="cloneTemplateName" name="name" required>
              <small class="text-muted">Give your customized template a unique name</small>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="customizeNow" name="customize" value="1" checked>
              <label class="form-check-label" for="customizeNow">
                Customize immediately after cloning
              </label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <i class="bx bx-check me-1"></i> Clone Template
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const starterModal = document.getElementById('starterLibraryModal');
    const previewModal = document.getElementById('templatePreviewModal');
    const cloneModal = document.getElementById('cloneTemplateModal');
    let currentTemplateId = null;

    // Load starter templates when modal is opened
    if (starterModal) {
      starterModal.addEventListener('show.bs.modal', function () {
        loadStarterTemplates();
      });
    }

    function loadStarterTemplates() {
      fetch('/admin/templates/starters/list')
        .then(response => response.json())
        .then(data => {
          renderStarterTemplates(data.templates);
        })
        .catch(error => {
          console.error('Error loading templates:', error);
          document.getElementById('loadingStarters').innerHTML =
            '<div class="alert alert-danger">Failed to load templates. Please try again.</div>';
        });
    }

    function renderStarterTemplates(templates) {
      const container = document.getElementById('starterTemplates');
      const loadingDiv = document.getElementById('loadingStarters');
      if (loadingDiv) loadingDiv.style.display = 'none';

      if (templates.length === 0) {
        container.innerHTML = '<div class="col-12 text-center"><p class="text-muted">No starter templates available yet.</p></div>';
        return;
      }

      container.innerHTML = templates.map(template => `
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 template-card" style="cursor: pointer;">
            <div class="position-relative">
              <div class="ratio ratio-4x3">
                <img src="${template.preview_url}" alt="${template.name}" class="card-img-top object-fit-cover">
              </div>
              ${template.is_premium ? '<span class="badge bg-warning position-absolute top-0 end-0 m-2"><i class="bx bx-crown"></i> Premium</span>' : ''}
            </div>
            <div class="card-body">
              <h6 class="card-title mb-2">${template.name}</h6>
              <p class="text-muted small mb-3">${template.description || 'Professional resume template'}</p>
              <div class="d-flex gap-2 mb-3">
                <span class="badge bg-label-secondary">${template.category}</span>
                ${template.features.slice(0, 2).map(f => `<span class="badge bg-label-info">${f}</span>`).join('')}
              </div>
              <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary flex-fill" onclick="previewTemplate(${template.id})">
                  <i class="bx bx-show me-1"></i> Preview
                </button>
                <button class="btn btn-sm btn-primary flex-fill" onclick="cloneTemplate(${template.id}, '${template.name}')">
                  <i class="bx bx-copy me-1"></i> Clone
                </button>
              </div>
            </div>
          </div>
        </div>
      `).join('');
    }

    // Preview template function
    window.previewTemplate = function(templateId) {
      currentTemplateId = templateId;

      fetch(`/admin/templates/starters/${templateId}/content`)
        .then(response => response.json())
        .then(data => {
          const iframe = document.getElementById('previewFrame');
          const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

          const fullHtml = `
            <!DOCTYPE html>
            <html>
            <head>
              <meta charset="UTF-8">
              <style>${data.css}</style>
            </head>
            <body>${data.html}</body>
            </html>
          `;

          iframeDoc.open();
          iframeDoc.write(fullHtml);
          iframeDoc.close();

          const previewModalInstance = new bootstrap.Modal(previewModal);
          previewModalInstance.show();
        });
    };

    // Clone template function
    window.cloneTemplate = function(templateId, templateName) {
      currentTemplateId = templateId;
      document.getElementById('cloneTemplateName').value = templateName + ' (Custom)';
      document.getElementById('cloneTemplateForm').action = `/admin/templates/starters/${templateId}/clone`;

      const cloneModalInstance = new bootstrap.Modal(cloneModal);
      cloneModalInstance.show();
    };

    // Clone from preview button
    const cloneFromPreviewBtn = document.getElementById('cloneFromPreview');
    if (cloneFromPreviewBtn) {
      cloneFromPreviewBtn.addEventListener('click', function() {
        if (currentTemplateId) {
          const previewModalInstance = bootstrap.Modal.getInstance(previewModal);
          if (previewModalInstance) previewModalInstance.hide();
          cloneTemplate(currentTemplateId, 'Template');
        }
      });
    }
  });
  </script>

  <style>
  .template-card {
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .template-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }
  </style>
</x-layouts.app>
