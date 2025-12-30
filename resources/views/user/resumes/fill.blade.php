<x-layouts.app :title="$title ?? 'Fill Resume Details'">
  <style>
    /* Resume Builder Improvements */
    .form-control, .form-select {
      height: 38px;
      font-size: 0.95rem;
    }
    
    textarea.form-control {
      height: auto;
    }
    
    .step-card .card-body {
      padding: 1.25rem 1.5rem;
    }
    
    .experience-item, .education-item {
      padding: 1rem;
      margin-bottom: 1rem;
      border: 1px solid #e8eaf6;
      border-radius: 0.5rem;
      background: #fafbff;
    }
    
    .experience-item:hover, .education-item:hover {
      border-color: #667eea;
      background: #f8f9ff;
    }
    
    .btn-sm {
      padding: 0.35rem 0.75rem;
      font-size: 0.875rem;
      white-space: nowrap;
    }
    
    .btn-outline-primary {
      border-color: #667eea;
      color: #667eea;
    }
    
    .btn-outline-primary:hover {
      background: #667eea;
      color: white;
    }
    
    .btn-outline-danger {
      border-color: #dc3545;
      color: #dc3545;
    }
    
    .btn-outline-danger:hover {
      background: #dc3545;
      color: white;
    }
    
    .gap-2 {
      gap: 0.5rem !important;
    }
    
    .next-step-btn {
      background: #667eea;
      color: white;
      border: none;
      padding: 0.5rem 1.5rem;
      font-size: 0.9rem;
      font-weight: 500;
      border-radius: 0.375rem;
      transition: all 0.3s ease;
    }
    
    .next-step-btn:hover {
      background: #5568d3;
      color: white;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .sticky-submit-wrapper {
      background: white;
      padding: 1rem 0;
      border-top: 2px solid #e8eaf6;
      margin-top: 2rem;
    }
    
    .sticky-submit-wrapper .btn {
      height: 50px;
      font-size: 1rem;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }
    
    /* Modal Improvements */
    .modal-dialog {
      max-width: 500px;
    }
    
    .modal-body {
      padding: 1.25rem;
    }
    
    .modal-header {
      padding: 1rem 1.25rem;
      border-bottom: 1px solid #e8eaf6;
    }
    
    .modal-footer {
      padding: 1rem 1.25rem;
      border-top: 1px solid #e8eaf6;
      justify-content: flex-end;
    }
    
    .modal-footer .btn {
      min-width: 100px;
    }
    
    /* Compact spacing */
    .row.g-2 {
      row-gap: 0.75rem !important;
    }
    
    .mb-2 {
      margin-bottom: 0.75rem !important;
    }
    
    /* Helper text */
    .helper-text {
      font-size: 0.85rem;
      color: #6c757d;
      margin-top: 0.5rem;
      display: block;
    }
    
    /* Section headers with AI button inline */
    .section-header-with-ai {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }
    
    .section-header-with-ai h6 {
      margin: 0;
    }
    
    /* Reduce textarea heights */
    #summaryField {
      min-height: 70px;
    }
    
    textarea[name="responsibilities[]"] {
      min-height: 85px;
    }
    
    textarea[name="education_details[]"] {
      min-height: 70px;
    }
    
    #skillsField {
      min-height: 80px;
    }
    
    /* Empty state */
    .empty-state {
      text-align: center;
      padding: 1.5rem;
      color: #6c757d;
      font-size: 0.9rem;
    }
  </style>
  
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Fill Your Resume Details</h5>
      <a href="{{ route('user.resumes.choose') }}" class="btn btn-secondary btn-sm">
        <i class="bx bx-arrow-back"></i> Back to Templates
      </a>
    </div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Please fix the following errors:</strong>
          <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <!-- Stepper and Step-based Form -->
      <form method="POST"
            action="{{ route('user.resumes.generate') }}"
            id="resumeForm"
            enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="template_id" value="{{ $template->id }}">

        <!-- Progress Indicator -->
        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="text-muted" id="progressText">Step 1 of 5</small>
            <small class="text-muted">Complete all required sections</small>
          </div>
          <div class="progress" style="height: 4px;">
            <div class="progress-bar bg-primary" id="progressBar" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>

        <!-- Accordion Container -->
        <div class="accordion" id="accordionSteps">

        <!-- Step 1: Personal Details -->
        <div class="card mb-3 step-card" data-step="1">
          <div class="card-header" data-bs-toggle="collapse" data-bs-target="#personalSection" role="button" aria-expanded="true" aria-controls="personalSection" style="cursor: pointer;">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-0"><i class="bx bx-user me-2 text-primary"></i> Step 1: Personal Details <span class="text-danger">*</span></h6>
                <small class="text-muted">Your name, contact info, and photo</small>
              </div>
              <i class="bx bx-chevron-down"></i>
            </div>
          </div>
          <div class="collapse show" id="personalSection">
            <div class="card-body">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Full Name *</label>
                  <input type="text"
                         name="name"
                         class="form-control @error('name') is-invalid @enderror"
                         value="{{ old('name') }}"
                         required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label">Job Title *</label>
                  <input type="text"
                         name="title"
                         class="form-control @error('title') is-invalid @enderror"
                         value="{{ old('title') }}"
                         required>
                  @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Email *</label>
                  <input type="email"
                         name="email"
                         class="form-control @error('email') is-invalid @enderror"
                         value="{{ old('email') }}"
                         required>
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label">Phone *</label>
                  <input type="text"
                         name="phone"
                         class="form-control @error('phone') is-invalid @enderror"
                         value="{{ old('phone') }}"
                         required>
                  @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-12">
                  <label class="form-label">Address</label>
                  <input type="text"
                         name="address"
                         class="form-control @error('address') is-invalid @enderror"
                         value="{{ old('address') }}"
                         placeholder="e.g., 123 Main St, New York, NY 10001">
                  @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <!-- Profile Picture Upload -->
              <div class="row mb-3">
                <div class="col-md-12">
                  <label class="form-label">Profile Picture (Optional)</label>
                  <div class="border rounded p-3 text-center" style="background: #f8f9fa;">
                    <div id="picturePreview" class="mb-3" style="display: none;">
                      <img id="picturePreviewImg" src="" alt="Preview" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #667eea;">
                    </div>
                    <div id="pictureUploadZone">
                      <i class="bx bx-image-add" style="font-size: 2rem; color: #667eea;"></i>
                      <p class="mb-1 mt-2"><strong>Upload Your Photo</strong></p>
                      <small class="text-muted">JPG, PNG (Max 2MB) • Recommended: 300x300px</small>
                    </div>
                    <input type="file"
                           name="profile_picture"
                           id="profilePictureInput"
                           class="form-control mt-2 @error('profile_picture') is-invalid @enderror"
                           accept="image/jpeg,image/png,image/jpg"
                           style="display: none;">
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="document.getElementById('profilePictureInput').click()">
                      <i class="bx bx-upload"></i> Choose Photo
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removePictureBtn" style="display: none;">
                      <i class="bx bx-trash"></i> Remove
                    </button>
                    @error('profile_picture')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                  <small class="text-muted d-block mt-1">
                    <i class="bx bx-info-circle"></i> Your photo will appear on the resume if the template supports it
                  </small>
                </div>
              </div>
              <div class="text-end mt-3">
                <button type="button" class="btn next-step-btn" onclick="goToStep(2)">
                  Next <i class="bx bx-chevron-right ms-1"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 2: Professional Summary -->
        <div class="card mb-3 step-card" data-step="2">
          <div class="card-header" data-bs-toggle="collapse" data-bs-target="#summarySection" role="button" aria-expanded="false" aria-controls="summarySection" style="cursor: pointer;">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-0"><i class="bx bx-file-blank me-2 text-primary"></i> Step 2: Professional Summary</h6>
                <small class="text-muted">2–3 lines that explain who you are professionally</small>
              </div>
              <i class="bx bx-chevron-down"></i>
            </div>
          </div>
          <div class="collapse" id="summarySection">
            <div class="card-body">
              <div class="section-header-with-ai">
                <label class="form-label mb-0">Professional Summary</label>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#summaryAIModal">
                  <i class="bx bx-sparkles"></i> Generate with AI
                </button>
              </div>
              <textarea name="summary"
                      rows="3"
                      class="form-control @error('summary') is-invalid @enderror"
                      id="summaryField"
                      placeholder="A brief professional summary about yourself...">{{ old('summary') }}</textarea>
              <small class="helper-text">2-3 sentences highlighting your professional background and goals</small>
              @error('summary')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="text-end mt-3">
                <button type="button" class="btn next-step-btn" onclick="goToStep(3)">
                  Next <i class="bx bx-chevron-right ms-1"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 3: Experience -->
        <div class="card mb-3 step-card" data-step="3">
          <div class="card-header" data-bs-toggle="collapse" data-bs-target="#experienceSection" role="button" aria-expanded="false" aria-controls="experienceSection" style="cursor: pointer;">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-0"><i class="bx bx-briefcase me-2 text-primary"></i> Step 3: Experience</h6>
                <small class="text-muted">Your work history, roles, and achievements</small>
              </div>
              <i class="bx bx-chevron-down"></i>
            </div>
          </div>
          <div class="collapse" id="experienceSection">
            <div class="card-body">
            <div class="d-flex justify-content-end align-items-center mb-3">
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addExperienceField()">
                <i class="bx bx-plus"></i> Add More
              </button>
            </div>
            <div id="experienceContainer">
              <div class="experience-item" id="experienceWrapper0">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <label class="form-label fw-500 mb-0">Experience 1</label>
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="showExperienceModalForIndex(0)">
                      <i class="bx bx-sparkles"></i> Generate with AI
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="document.getElementById('experienceWrapper0').remove()">
                      <i class="bx bx-trash"></i> Remove
                    </button>
                  </div>
                </div>
                <div class="row g-2 mb-2">
                  <div class="col-md-6">
                    <input type="text" name="job_title[]" id="job_title0" class="form-control" placeholder="Job Title" value="{{ old('job_title.0') }}">
                  </div>
                  <div class="col-md-6">
                    <input type="text" name="company[]" id="company0" class="form-control" placeholder="Company" value="{{ old('company.0') }}">
                  </div>
                </div>
                <div class="row g-2 mb-2">
                  <div class="col-md-6">
                    <input type="text" name="start_date[]" id="start_date0" class="form-control" placeholder="Start (e.g., Jan 2020)" value="{{ old('start_date.0') }}">
                  </div>
                  <div class="col-md-6">
                    <input type="text" name="end_date[]" id="end_date0" class="form-control" placeholder="End (e.g., Present)" value="{{ old('end_date.0') }}">
                  </div>
                </div>
                <div class="mb-0">
                  <textarea name="responsibilities[]" id="responsibilities0" rows="3" class="form-control" placeholder="Key responsibilities and achievements (use bullet points)">{{ old('responsibilities.0') }}</textarea>
                  <small class="helper-text">Use bullet points. Start with action verbs (e.g., Led, Developed, Managed)</small>
                </div>
              </div>
            </div>
            @error('experience.0')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="text-end mt-3">
              <button type="button" class="btn next-step-btn" onclick="goToStep(4)">
                Next <i class="bx bx-chevron-right ms-1"></i>
              </button>
            </div>
          </div>
        </div>
        </div>

        <!-- Step 4: Skills -->
        <div class="card mb-3 step-card" data-step="4">
          <div class="card-header" data-bs-toggle="collapse" data-bs-target="#skillsSection" role="button" aria-expanded="false" aria-controls="skillsSection" style="cursor: pointer;">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-0"><i class="bx bx-star me-2 text-primary"></i> Step 4: Skills</h6>
                <small class="text-muted">Most relevant skills for the job you want</small>
              </div>
              <i class="bx bx-chevron-down"></i>
            </div>
          </div>
          <div class="collapse" id="skillsSection">
            <div class="card-body">
              {{-- <div class="section-header-with-ai">
                <label class="form-label mb-0">Skills</label>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#skillsAIModal">
                  <i class="bx bx-sparkles"></i> Generate with AI
                </button>
              </div> --}}
              <label class="form-label">Skills</label>
            <textarea name="skills"
                      rows="3"
                      class="form-control @error('skills') is-invalid @enderror"
                      id="skillsField"
                      placeholder="List your key skills (comma-separated)">{{ old('skills') }}</textarea>
            <small class="helper-text">Example: PHP, Laravel, Vue.js, MySQL, AWS, Docker, Project Management</small>
            @error('skills')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="text-end mt-3">
              <button type="button" class="btn next-step-btn" onclick="goToStep(5)">
                Next <i class="bx bx-chevron-right ms-1"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Step 5: Education -->
        <div class="card mb-3 step-card" data-step="5">
          <div class="card-header" data-bs-toggle="collapse" data-bs-target="#educationSection" role="button" aria-expanded="false" aria-controls="educationSection" style="cursor: pointer;">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-0"><i class="bx bx-book me-2 text-primary"></i> Step 5: Education</h6>
                <small class="text-muted">Your degrees, schools, and graduation years</small>
              </div>
              <i class="bx bx-chevron-down"></i>
            </div>
          </div>
          <div class="collapse" id="educationSection">
            <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              {{-- <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#educationAIModal">
                <i class="bx bx-sparkles"></i> Generate with AI
              </button> --}}
              <div></div>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addEducationField()">
                <i class="bx bx-plus"></i> Add More
              </button>
            </div>
            <div id="educationContainer">
              <div class="education-item" id="educationWrapper0">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <label class="form-label fw-500 mb-0">Education 1</label>
                  <button type="button" class="btn btn-sm btn-outline-danger" onclick="document.getElementById('educationWrapper0').remove()">
                    <i class="bx bx-trash"></i> Remove
                  </button>
                </div>
                <div class="row g-2 mb-2">
                  <div class="col-md-6">
                    <input type="text" name="degree[]" id="degree0" class="form-control" placeholder="Degree (e.g., BSc Computer Science)" value="{{ old('degree.0') }}">
                  </div>
                  <div class="col-md-6">
                    <input type="text" name="field_of_study[]" id="field_of_study0" class="form-control" placeholder="Field of Study" value="{{ old('field_of_study.0') }}">
                  </div>
                </div>
                <div class="row g-2 mb-2">
                  <div class="col-md-8">
                    <input type="text" name="university[]" id="university0" class="form-control" placeholder="University / Institution" value="{{ old('university.0') }}">
                  </div>
                  <div class="col-md-4">
                    <input type="text" name="graduation_year[]" id="graduation_year0" class="form-control" placeholder="Year (e.g., 2018)" value="{{ old('graduation_year.0') }}">
                  </div>
                </div>
                <div class="mb-0">
                  <textarea name="education_details[]" id="education_details0" rows="2" class="form-control" placeholder="Honors, achievements, or relevant coursework (optional)">{{ old('education_details.0') }}</textarea>
                </div>
              </div>
            </div>
            @error('education.0')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        </div><!-- End Accordion Container -->

        <!-- Submit Button -->
        <div class="mt-4 sticky-submit-wrapper">
          <button type="submit" class="btn btn-primary btn-lg w-100" id="generateBtn">
            <i class="bx bx-show me-2"></i> Preview Resume
          </button>
          <small class="text-center d-block text-muted mt-2">You can edit your resume before downloading</small>
        </div>
      </form>
    </div>
  </div>

  <!-- AI Modal for Experience -->
  <div class="modal fade" id="experienceAIModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title mb-0">
            <i class="bx bx-sparkles text-warning"></i> Generate Experience with AI
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="experienceAIForm">
            <div class="mb-3">
              <label class="form-label">Your Roles & Responsibilities</label>
              <textarea class="form-control" id="aiResponsibilities" rows="4" placeholder="e.g., Led development team, managed cloud infrastructure, coordinated with stakeholders..."></textarea>
              <small class="text-muted mt-2 d-block">
                <i class="bx bx-info-circle"></i> Add your roles/responsibilities and AI will generate professional content which you can edit later too
              </small>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-sm btn-primary" onclick="generateExperienceAI()" id="experienceAIBtn">
            <i class="bx bx-sparkles me-1"></i> Generate
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- AI Modal for Skills -->
  <div class="modal fade" id="skillsAIModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title mb-0">
            <i class="bx bx-sparkles text-warning"></i> Generate Skills with AI
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="skillsAIForm">
            <div class="mb-3">
              <label class="form-label">Job Title / Role</label>
              <input type="text" class="form-control" id="aiSkillsRole" placeholder="e.g., Full Stack Developer">
            </div>
            <div class="mb-3">
              <label class="form-label">Experience Level</label>
              <select class="form-select" id="aiSkillsLevel">
                <option value="">-- Select Level --</option>
                <option value="junior">Junior (0-2 years)</option>
                <option value="mid">Mid-Level (2-5 years)</option>
                <option value="senior">Senior (5+ years)</option>
              </select>
            </div>
            <div class="mb-0">
              <label class="form-label">Technologies / Fields</label>
              <textarea class="form-control" id="aiSkillsFields" rows="2" placeholder="e.g., PHP, Laravel, React, AWS..."></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-sm btn-primary" onclick="generateSkillsAI()" id="skillsAIBtn">
            <i class="bx bx-sparkles me-1"></i> Generate
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- AI Modal for Education -->
  <div class="modal fade" id="educationAIModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title mb-0">
            <i class="bx bx-sparkles text-warning"></i> Generate Education with AI
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="educationAIForm">
            <div class="mb-3">
              <label class="form-label">Degree Type</label>
              <input type="text" class="form-control" id="aiDegree" placeholder="e.g., Bachelor of Science">
            </div>
            <div class="mb-3">
              <label class="form-label">Field of Study</label>
              <input type="text" class="form-control" id="aiFieldOfStudy" placeholder="e.g., Computer Science">
            </div>
            <div class="mb-3">
              <label class="form-label">University Name</label>
              <input type="text" class="form-control" id="aiUniversity" placeholder="e.g., XYZ University">
            </div>
            <div class="mb-0">
              <label class="form-label">Graduation Year</label>
              <input type="number" class="form-control" id="aiGraduationYear" placeholder="e.g., 2020" min="1950">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-sm btn-primary" onclick="generateEducationAI()" id="educationAIBtn">
            <i class="bx bx-sparkles me-1"></i> Generate
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- AI Modal for Summary -->
  <div class="modal fade" id="summaryAIModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title mb-0">
            <i class="bx bx-sparkles text-warning"></i> Generate Professional Summary with AI
          </h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="summaryAIForm">
            <div class="mb-3">
              <label class="form-label">Job Title / Role</label>
              <input type="text" class="form-control" id="aiSummaryRole" placeholder="e.g., Full Stack Developer">
            </div>
            <div class="mb-3">
              <label class="form-label">Years of Experience</label>
              <input type="number" class="form-control" id="aiSummaryYears" placeholder="e.g., 5" min="0">
            </div>
            <div class="mb-3">
              <label class="form-label">Key Skills / Specializations</label>
              <textarea class="form-control" id="aiSummarySkills" rows="2" placeholder="e.g., Web Development, Cloud Architecture, Team Leadership..."></textarea>
            </div>
            <div class="mb-0">
              <label class="form-label">Career Goal / Focus</label>
              <input type="text" class="form-control" id="aiSummaryGoal" placeholder="e.g., Seeking senior developer roles in fintech">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-sm btn-primary" onclick="generateSummaryAI()" id="summaryAIBtn">
            <i class="bx bx-sparkles me-1"></i> Generate
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Loading overlay -->
  <script>
    let currentExperienceIndex = null;
    let currentEducationIndex = null;

    // Navigation function for steps
    function goToStep(stepNumber) {
      // Close current section
      const currentCollapse = document.querySelector('.collapse.show');
      if (currentCollapse) {
        const bsCollapse = bootstrap.Collapse.getInstance(currentCollapse);
        if (bsCollapse) {
          bsCollapse.hide();
        }
      }
      
      // Open target section
      const targetSections = {
        2: 'summarySection',
        3: 'experienceSection',
        4: 'skillsSection',
        5: 'educationSection'
      };
      
      const targetId = targetSections[stepNumber];
      if (targetId) {
        setTimeout(() => {
          const targetSection = document.getElementById(targetId);
          if (targetSection) {
            const bsCollapse = new bootstrap.Collapse(targetSection, {
              toggle: true
            });
            
            // Scroll to the section
            targetSection.closest('.card').scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }, 300);
      }
    }

    // Form submission
    document.getElementById('resumeForm').addEventListener('submit', function() {
      const btn = document.getElementById('generateBtn');
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating PDF...';
      btn.disabled = true;
    });

    // Show experience modal for a specific index
    function showExperienceModalForIndex(index) {
      currentExperienceIndex = index;
      // Clear the modal field
      document.getElementById('aiResponsibilities').value = '';
      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('experienceAIModal'));
      modal.show();
    }

    // Show education modal for a specific index
    function showEducationModalForIndex(index) {
      currentEducationIndex = index;
      // Clear the modal fields
      document.getElementById('aiDegree').value = '';
      document.getElementById('aiFieldOfStudy').value = '';
      document.getElementById('aiUniversity').value = '';
      document.getElementById('aiGraduationYear').value = '';
      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('educationAIModal'));
      modal.show();
    }

    // AI Generation Functions
    async function generateExperienceAI() {
      const responsibilities = document.getElementById('aiResponsibilities').value;

      if (!responsibilities || responsibilities.trim() === '') {
        alert('Please add your roles and responsibilities');
        return;
      }

      const btn = document.getElementById('experienceAIBtn');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating...';

      try {
        const response = await fetch('{{ route("user.resumes.generate-experience-ai") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
          },
          body: JSON.stringify({
            responsibilities: responsibilities
          })
        });

        const data = await response.json();
        if (data.success) {
          // If current index specified, fill responsibilities at that index
          if (currentExperienceIndex !== null) {
            const idx = currentExperienceIndex;
            const respEl = document.getElementById('responsibilities' + idx);
            if (respEl) respEl.value = data.content;
            currentExperienceIndex = null;
          } else {
            // Find first empty structured block
            const firstResp = document.getElementById('responsibilities0');
            if (firstResp && firstResp.value.trim() === '') {
              firstResp.value = data.content;
            } else {
              // Add new structured block and fill it
              addExperienceField();
              const newIdx = experienceCount - 1;
              const respEl = document.getElementById('responsibilities' + newIdx);
              if (respEl) respEl.value = data.content;
            }
          }
          bootstrap.Modal.getInstance(document.getElementById('experienceAIModal')).hide();
        } else {
          alert('Error: ' + data.message);
        }
      } catch (error) {
        alert('Error generating content: ' + error.message);
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-sparkles me-1"></i> Generate';
      }
    }

    // Add multiple structured experience entries
    let experienceCount = 1;
    function addExperienceField() {
      const container = document.getElementById('experienceContainer');

      const idx = experienceCount;
      const fieldWrapper = document.createElement('div');
      fieldWrapper.className = 'experience-item';
      fieldWrapper.id = 'experienceWrapper' + idx;

      const headerDiv = document.createElement('div');
      headerDiv.className = 'd-flex justify-content-between align-items-center mb-2';

      const label = document.createElement('label');
      label.className = 'form-label fw-500 mb-0';
      label.innerHTML = 'Experience ' + (idx + 1);

      const btnGroup = document.createElement('div');
      btnGroup.className = 'd-flex gap-2';

      const aiBtn = document.createElement('button');
      aiBtn.type = 'button';
      aiBtn.className = 'btn btn-sm btn-outline-primary';
      aiBtn.innerHTML = '<i class="bx bx-sparkles"></i> Generate with AI';
      aiBtn.onclick = function() { showExperienceModalForIndex(idx); };

      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'btn btn-sm btn-outline-danger';
      removeBtn.innerHTML = '<i class="bx bx-trash"></i> Remove';
      removeBtn.onclick = function() { fieldWrapper.remove(); };

      btnGroup.appendChild(aiBtn);
      btnGroup.appendChild(removeBtn);
      headerDiv.appendChild(label);
      headerDiv.appendChild(btnGroup);

      const row1 = document.createElement('div');
      row1.className = 'row g-2 mb-2';

      const col1 = document.createElement('div'); col1.className = 'col-md-6';
      const jobInput = document.createElement('input'); 
      jobInput.type = 'text'; 
      jobInput.name = 'job_title[]'; 
      jobInput.id = 'job_title' + idx; 
      jobInput.className = 'form-control'; 
      jobInput.placeholder = 'Job Title';
      col1.appendChild(jobInput);

      const col2 = document.createElement('div'); col2.className = 'col-md-6';
      const compInput = document.createElement('input'); 
      compInput.type = 'text'; 
      compInput.name = 'company[]'; 
      compInput.id = 'company' + idx; 
      compInput.className = 'form-control'; 
      compInput.placeholder = 'Company';
      col2.appendChild(compInput);

      row1.appendChild(col1); 
      row1.appendChild(col2);

      const row2 = document.createElement('div');
      row2.className = 'row g-2 mb-2';

      const col3 = document.createElement('div'); col3.className = 'col-md-6';
      const startInput = document.createElement('input'); 
      startInput.type = 'text'; 
      startInput.name = 'start_date[]'; 
      startInput.id = 'start_date' + idx; 
      startInput.className = 'form-control'; 
      startInput.placeholder = 'Start (e.g., Jan 2020)';
      col3.appendChild(startInput);

      const col4 = document.createElement('div'); col4.className = 'col-md-6';
      const endInput = document.createElement('input'); 
      endInput.type = 'text'; 
      endInput.name = 'end_date[]'; 
      endInput.id = 'end_date' + idx; 
      endInput.className = 'form-control'; 
      endInput.placeholder = 'End (e.g., Present)';
      col4.appendChild(endInput);

      row2.appendChild(col3); 
      row2.appendChild(col4);

      const respDiv = document.createElement('div'); 
      respDiv.className = 'mb-0';
      const respArea = document.createElement('textarea'); 
      respArea.name = 'responsibilities[]'; 
      respArea.id = 'responsibilities' + idx; 
      respArea.rows = 3; 
      respArea.className = 'form-control'; 
      respArea.placeholder = 'Key responsibilities and achievements (use bullet points)';
      respDiv.appendChild(respArea);

      const helperText = document.createElement('small');
      helperText.className = 'helper-text';
      helperText.innerHTML = 'Use bullet points. Start with action verbs (e.g., Led, Developed, Managed)';
      respDiv.appendChild(helperText);

      fieldWrapper.appendChild(headerDiv);
      fieldWrapper.appendChild(row1);
      fieldWrapper.appendChild(row2);
      fieldWrapper.appendChild(respDiv);

      container.appendChild(fieldWrapper);
      experienceCount++;
    }

    async function generateSkillsAI() {
      const role = document.getElementById('aiSkillsRole').value;
      const level = document.getElementById('aiSkillsLevel').value;
      const fields = document.getElementById('aiSkillsFields').value;

      if (!role || !level) {
        alert('Please fill in all required fields');
        return;
      }

      const btn = document.getElementById('skillsAIBtn');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating...';

      try {
        const response = await fetch('{{ route("user.resumes.generate-skills-ai") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
          },
          body: JSON.stringify({
            role: role,
            level: level,
            fields: fields
          })
        });

        const data = await response.json();
        if (data.success) {
          document.getElementById('skillsField').value = data.content;
          bootstrap.Modal.getInstance(document.getElementById('skillsAIModal')).hide();
        } else {
          alert('Error: ' + data.message);
        }
      } catch (error) {
        alert('Error generating content: ' + error.message);
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-sparkles me-1"></i> Generate';
      }
    }

    async function generateEducationAI() {
      const degree = document.getElementById('aiDegree').value;
      const fieldOfStudy = document.getElementById('aiFieldOfStudy').value;
      const university = document.getElementById('aiUniversity').value;
      const graduationYear = document.getElementById('aiGraduationYear').value;

      if (!degree || !fieldOfStudy || !university || !graduationYear) {
        alert('Please fill in all required fields');
        return;
      }

      const btn = document.getElementById('educationAIBtn');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating...';

      try {
        const response = await fetch('{{ route("user.resumes.generate-education-ai") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
          },
          body: JSON.stringify({
            degree: degree,
            field_of_study: fieldOfStudy,
            university: university,
            graduation_year: graduationYear
          })
        });

        const data = await response.json();
        if (data.success) {
          if (currentEducationIndex !== null) {
            const idx = currentEducationIndex;
            const degreeEl = document.getElementById('degree' + idx);
            const fieldEl = document.getElementById('field_of_study' + idx);
            const univEl = document.getElementById('university' + idx);
            const gradEl = document.getElementById('graduation_year' + idx);
            const detailsEl = document.getElementById('education_details' + idx);
            if (degreeEl) degreeEl.value = degree;
            if (fieldEl) fieldEl.value = fieldOfStudy;
            if (univEl) univEl.value = university;
            if (gradEl) gradEl.value = graduationYear;
            if (detailsEl) detailsEl.value = data.content;
            currentEducationIndex = null;
          } else {
            const firstDegree = document.getElementById('degree0');
            const firstDetails = document.getElementById('education_details0');
            if (firstDegree && firstDegree.value.trim() === '' && firstDetails && firstDetails.value.trim() === '') {
              firstDegree.value = degree;
              document.getElementById('field_of_study0').value = fieldOfStudy;
              document.getElementById('university0').value = university;
              document.getElementById('graduation_year0').value = graduationYear;
              firstDetails.value = data.content;
            } else {
              addEducationField();
              const newIdx = educationCount - 1;
              const degreeEl = document.getElementById('degree' + newIdx);
              const fieldEl = document.getElementById('field_of_study' + newIdx);
              const univEl = document.getElementById('university' + newIdx);
              const gradEl = document.getElementById('graduation_year' + newIdx);
              const detailsEl = document.getElementById('education_details' + newIdx);
              if (degreeEl) degreeEl.value = degree;
              if (fieldEl) fieldEl.value = fieldOfStudy;
              if (univEl) univEl.value = university;
              if (gradEl) gradEl.value = graduationYear;
              if (detailsEl) detailsEl.value = data.content;
            }
          }
          bootstrap.Modal.getInstance(document.getElementById('educationAIModal')).hide();
        } else {
          alert('Error: ' + data.message);
        }
      } catch (error) {
        alert('Error generating content: ' + error.message);
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-sparkles me-1"></i> Generate';
      }
    }

    // Add multiple structured education entries
    let educationCount = 1;
    function addEducationField() {
      const container = document.getElementById('educationContainer');
      const idx = educationCount;
      const fieldWrapper = document.createElement('div');
      fieldWrapper.className = 'education-item';
      fieldWrapper.id = 'educationWrapper' + idx;

      const headerDiv = document.createElement('div');
      headerDiv.className = 'd-flex justify-content-between align-items-center mb-2';

      const label = document.createElement('label');
      label.className = 'form-label fw-500 mb-0';
      label.innerHTML = 'Education ' + (idx + 1);

      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'btn btn-sm btn-outline-danger';
      removeBtn.innerHTML = '<i class="bx bx-trash"></i> Remove';
      removeBtn.onclick = function() { fieldWrapper.remove(); };

      headerDiv.appendChild(label);
      headerDiv.appendChild(removeBtn);

      const row1 = document.createElement('div'); 
      row1.className = 'row g-2 mb-2';
      
      const col1 = document.createElement('div'); 
      col1.className = 'col-md-6';
      const degInput = document.createElement('input'); 
      degInput.type = 'text'; 
      degInput.name = 'degree[]'; 
      degInput.id = 'degree' + idx; 
      degInput.className = 'form-control'; 
      degInput.placeholder = 'Degree (e.g., BSc Computer Science)';
      col1.appendChild(degInput);
      
      const col2 = document.createElement('div'); 
      col2.className = 'col-md-6';
      const fieldInput = document.createElement('input'); 
      fieldInput.type = 'text'; 
      fieldInput.name = 'field_of_study[]'; 
      fieldInput.id = 'field_of_study' + idx; 
      fieldInput.className = 'form-control'; 
      fieldInput.placeholder = 'Field of Study';
      col2.appendChild(fieldInput);
      
      row1.appendChild(col1); 
      row1.appendChild(col2);

      const row2 = document.createElement('div'); 
      row2.className = 'row g-2 mb-2';
      
      const col3 = document.createElement('div'); 
      col3.className = 'col-md-8';
      const univInput = document.createElement('input'); 
      univInput.type = 'text'; 
      univInput.name = 'university[]'; 
      univInput.id = 'university' + idx; 
      univInput.className = 'form-control'; 
      univInput.placeholder = 'University / Institution';
      col3.appendChild(univInput);
      
      const col4 = document.createElement('div'); 
      col4.className = 'col-md-4';
      const gradInput = document.createElement('input'); 
      gradInput.type = 'text'; 
      gradInput.name = 'graduation_year[]'; 
      gradInput.id = 'graduation_year' + idx; 
      gradInput.className = 'form-control'; 
      gradInput.placeholder = 'Year (e.g., 2018)';
      col4.appendChild(gradInput);
      
      row2.appendChild(col3); 
      row2.appendChild(col4);

      const detailsDiv = document.createElement('div'); 
      detailsDiv.className = 'mb-0';
      const detailsArea = document.createElement('textarea'); 
      detailsArea.name = 'education_details[]'; 
      detailsArea.id = 'education_details' + idx; 
      detailsArea.rows = 2; 
      detailsArea.className = 'form-control'; 
      detailsArea.placeholder = 'Honors, achievements, or relevant coursework (optional)';
      detailsDiv.appendChild(detailsArea);

      fieldWrapper.appendChild(headerDiv);
      fieldWrapper.appendChild(row1);
      fieldWrapper.appendChild(row2);
      fieldWrapper.appendChild(detailsDiv);

      container.appendChild(fieldWrapper);
      educationCount++;
    }

    async function generateSummaryAI() {
      const role = document.getElementById('aiSummaryRole').value;
      const years = document.getElementById('aiSummaryYears').value;
      const skills = document.getElementById('aiSummarySkills').value;
      const goal = document.getElementById('aiSummaryGoal').value;

      if (!role || !years) {
        alert('Please fill in all required fields');
        return;
      }

      const btn = document.getElementById('summaryAIBtn');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating...';

      try {
        const response = await fetch('{{ route("user.resumes.generate-summary-ai") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
          },
          body: JSON.stringify({
            role: role,
            years: years,
            skills: skills,
            goal: goal
          })
        });

        const data = await response.json();
        if (data.success) {
          document.getElementById('summaryField').value = data.content;
          bootstrap.Modal.getInstance(document.getElementById('summaryAIModal')).hide();
        } else {
          alert('Error: ' + data.message);
        }
      } catch (error) {
        alert('Error generating content: ' + error.message);
      } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-sparkles me-1"></i> Generate';
      }
    }

    // Profile Picture Preview
    const profilePictureInput = document.getElementById('profilePictureInput');
    const picturePreview = document.getElementById('picturePreview');
    const picturePreviewImg = document.getElementById('picturePreviewImg');
    const pictureUploadZone = document.getElementById('pictureUploadZone');
    const removePictureBtn = document.getElementById('removePictureBtn');

    profilePictureInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
          alert('File size must be less than 2MB');
          e.target.value = '';
          return;
        }

        // Validate file type
        if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
          alert('Only JPG and PNG images are allowed');
          e.target.value = '';
          return;
        }

        // Preview the image
        const reader = new FileReader();
        reader.onload = function(e) {
          picturePreviewImg.src = e.target.result;
          picturePreview.style.display = 'block';
          pictureUploadZone.style.display = 'none';
          removePictureBtn.style.display = 'inline-block';
        };
        reader.readAsDataURL(file);
      }
    });

    removePictureBtn.addEventListener('click', function() {
      profilePictureInput.value = '';
      picturePreview.style.display = 'none';
      pictureUploadZone.style.display = 'block';
      removePictureBtn.style.display = 'none';
    });
  </script>

  <!-- Mobile Responsive Styles for Resume Form -->
  <style>
    @media (max-width: 768px) {
      .card-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem;
      }

      .card-header .btn {
        width: 100%;
      }

      .experience-entry,
      .education-entry,
      .certificate-entry {
        padding: 1rem;
      }

      .btn-group {
        flex-direction: column;
        width: 100%;
      }

      .btn-group .btn {
        width: 100%;
        margin-bottom: 0.5rem;
      }

      #experienceContainer .border,
      #educationContainer .border,
      #certificatesContainer .border {
        padding: 1rem !important;
        margin-bottom: 1rem;
      }
    }

    @media (max-width: 576px) {
      .row .col-md-6,
      .row .col-md-4 {
        margin-bottom: 1rem;
      }

      .card-body {
        padding: 1rem !important;
      }

      .form-label {
        font-size: 0.9rem;
      }

      .btn-sm {
        font-size: 0.8rem;
        padding: 0.375rem 0.75rem;
      }
    }

    /* Professional Step Form Styling */
    .step-card {
      border-radius: 4px;
      border: 1px solid #e5e7eb;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
      transition: box-shadow 0.2s;
    }
    .step-card:hover {
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    }
    .step-card .card-header {
      background: #fafafa;
      border-bottom: 1px solid #e5e7eb;
      padding: 1rem 1.25rem;
      transition: background-color 0.15s;
    }
    .step-card .card-header:hover {
      background: #f5f5f5;
    }
    .step-card .card-header h6 {
      font-weight: 600;
      color: #1f2937;
      font-size: 0.95rem;
    }
    .step-card .card-header small {
      display: block;
      margin-top: 0.25rem;
      color: #6b7280;
      font-size: 0.8125rem;
    }
    .step-card .card-header .bx-chevron-down {
      transition: transform 0.25s ease;
      color: #9ca3af;
    }
    .step-card .card-header[aria-expanded="true"] .bx-chevron-down {
      transform: rotate(180deg);
    }
    .step-card .card-body {
      padding: 1.5rem 1.25rem;
    }
    .step-card .form-control {
      border-radius: 4px;
      border: 1px solid #d1d5db;
      padding: 0.625rem 0.875rem;
      transition: border-color 0.15s, box-shadow 0.15s;
      font-size: 0.9375rem;
    }
    .step-card .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.08);
    }
    .step-card .form-label {
      font-weight: 500;
      color: #374151;
      font-size: 0.875rem;
      margin-bottom: 0.5rem;
    }
    .btn-primary {
      background: #667eea;
      border: none;
      box-shadow: 0 2px 4px rgba(102, 126, 234, 0.2);
      font-weight: 600;
      transition: all 0.15s;
      font-size: 1rem;
    }
    .btn-primary:hover {
      background: #5568d3;
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
    }
    .sticky-submit-wrapper {
      position: sticky;
      bottom: 0;
      background: white;
      padding: 1.25rem 0;
      margin: 0 -1.5rem -1.5rem;
      padding-left: 1.5rem;
      padding-right: 1.5rem;
      border-top: 1px solid #e5e7eb;
      box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.06);
      z-index: 100;
    }
    @media (max-width: 768px) {
      .sticky-submit-wrapper {
        padding: 1rem 1rem;
        margin: 0 -1rem -1rem;
      }
    }
    .btn-outline-secondary {
      border-width: 2px;
      font-weight: 500;
    }
    .btn-success {
      background: #10b981;
      border: none;
      box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
      font-weight: 600;
    }
    .btn-success:hover {
      background: #059669;
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    }
  </style>

  <script>
    // Accordion auto-advance logic for step-based resume form
    document.addEventListener('DOMContentLoaded', function() {
      const progressBar = document.getElementById('progressBar');
      const progressText = document.getElementById('progressText');
      const stepCards = document.querySelectorAll('.step-card');

      // Update progress bar and text when a section is shown
      document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(header) {
        header.addEventListener('click', function() {
          const stepCard = this.closest('.step-card');
          const stepNumber = parseInt(stepCard.getAttribute('data-step'));
          updateProgress(stepNumber);
        });
      });

      // Listen for collapse shown events to update progress
      document.querySelectorAll('.collapse').forEach(function(collapseEl) {
        collapseEl.addEventListener('shown.bs.collapse', function() {
          const stepCard = this.closest('.step-card');
          const stepNumber = parseInt(stepCard.getAttribute('data-step'));
          updateProgress(stepNumber);
        });
      });

      function updateProgress(stepNumber) {
        const progress = (stepNumber / 5) * 100;
        progressBar.style.width = progress + '%';
        progressBar.setAttribute('aria-valuenow', progress);
        progressText.textContent = 'Step ' + stepNumber + ' of 5';
      }

      // Auto-advance logic: check required fields and open next section
      function checkAndAdvance(currentSectionId, nextSectionId) {
        const currentSection = document.getElementById(currentSectionId);
        if (!currentSection) return;

        const requiredFields = currentSection.querySelectorAll('input[required], textarea[required]');
        let allFilled = true;

        requiredFields.forEach(function(field) {
          if (!field.value.trim()) {
            allFilled = false;
          }
        });

        // If all required fields are filled, auto-open next section
        if (allFilled && nextSectionId) {
          const nextSection = document.getElementById(nextSectionId);
          if (nextSection && !nextSection.classList.contains('show')) {
            setTimeout(function() {
              const bsCollapse = new bootstrap.Collapse(nextSection, {
                toggle: true
              });
            }, 300);
          }
        }
      }

      // Add blur event listeners to required fields for auto-advance
      const personalFields = document.querySelectorAll('#personalSection input, #personalSection textarea');
      personalFields.forEach(function(field) {
        field.addEventListener('blur', function() {
          checkAndAdvance('personalSection', 'summarySection');
        });
      });

      const summaryFields = document.querySelectorAll('#summarySection textarea');
      summaryFields.forEach(function(field) {
        field.addEventListener('blur', function() {
          checkAndAdvance('summarySection', 'experienceSection');
        });
      });

      const experienceFields = document.querySelectorAll('#experienceSection input, #experienceSection textarea');
      experienceFields.forEach(function(field) {
        field.addEventListener('blur', function() {
          checkAndAdvance('experienceSection', 'skillsSection');
        });
      });

      const skillsFields = document.querySelectorAll('#skillsSection textarea');
      skillsFields.forEach(function(field) {
        field.addEventListener('blur', function() {
          checkAndAdvance('skillsSection', 'educationSection');
        });
      });
    });
  </script>
</x-layouts.app>
