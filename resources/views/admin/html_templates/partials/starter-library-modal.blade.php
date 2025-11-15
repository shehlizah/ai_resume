{{-- resources/views/admin/templates/partials/starter-library-modal.blade.php --}}

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
  starterModal.addEventListener('show.bs.modal', function () {
    loadStarterTemplates();
  });

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
    document.getElementById('loadingStarters').style.display = 'none';
    
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
  document.getElementById('cloneFromPreview').addEventListener('click', function() {
    if (currentTemplateId) {
      const previewModalInstance = bootstrap.Modal.getInstance(previewModal);
      previewModalInstance.hide();
      cloneTemplate(currentTemplateId, 'Template');
    }
  });
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