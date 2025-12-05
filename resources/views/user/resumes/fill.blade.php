<x-layouts.app :title="$title ?? 'Fill Resume Details'">
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Fill Your Resume Details</h5>
      <a href="{{ route('user.resumes.choose') }}" class="btn btn-secondary btn-sm">
        ← Back
      </a>
    </div>
    <div class="card-body">
      <div class="text-center mb-4">
        @if($template->preview_image)
          <img src="{{ asset($template->preview_image) }}"
               alt="Preview"
               class="img-thumbnail"
               style="max-width: 180px; border-radius: 8px;">
        @else
          <div class="bg-light d-inline-block p-4 rounded">
            <i class="bx bx-file" style="font-size: 48px; color: #ddd;"></i>
          </div>
        @endif
        <h6 class="mt-3">{{ $template->name }}</h6>
        <p class="text-muted small">{{ $template->description }}</p>
      </div>

      <!-- Show any errors -->
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bx bx-error-circle me-2"></i>
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

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

      <!-- AI Info Alert -->
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bx bx-sparkles me-2"></i>
        <strong>AI-Powered Resume Builder!</strong> Click the <strong>✨ Generate with AI</strong> buttons to automatically generate professional content for each section.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>

      <!-- Form (normal submission) -->
      <form method="POST"
            action="{{ route('user.resumes.generate') }}"
            id="resumeForm"
            enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="template_id" value="{{ $template->id }}">

        <!-- Personal Info -->
        <div class="card mb-4">
          <div class="card-body">
            <h6 class="mb-3"><i class="bx bx-user"></i> Personal Details</h6>
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
          </div>
        </div>

        <!-- Professional Summary -->
        <div class="card mb-4">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0"><i class="bx bx-file-blank"></i> Professional Summary</h6>
              <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#summaryAIModal">
                <i class="bx bx-sparkles"></i> Generate with AI
              </button>
            </div>
            <textarea name="summary"
                      rows="3"
                      class="form-control @error('summary') is-invalid @enderror"
                      id="summaryField"
                      placeholder="Write a brief professional summary about yourself...">{{ old('summary') }}</textarea>
            <small class="text-muted">A 2-3 sentence overview of your professional background and goals</small>
            @error('summary')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <!-- Experience -->
        <div class="card mb-4">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0"><i class="bx bx-briefcase"></i> Experience</h6>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addExperienceField()">
                <i class="bx bx-plus"></i> Add More
              </button>
            </div>
            <div id="experienceContainer">
              <div class="mb-4 p-3 border-bottom" id="experienceWrapper0">
                <label class="form-label fw-500 mt-2 mb-2">Experience 1 <span class="badge bg-secondary ms-2">Entry</span></label>
                <div class="row g-2 mb-2">
                  <div class="col-md-4">
                    <input type="text" name="job_title[]" id="job_title0" class="form-control" placeholder="Job Title (e.g., Senior Developer)" value="{{ old('job_title.0') }}">
                  </div>
                  <div class="col-md-4">
                    <input type="text" name="company[]" id="company0" class="form-control" placeholder="Company (e.g., Tech Corp)" value="{{ old('company.0') }}">
                  </div>
                  <div class="col-md-2">
                    <input type="text" name="start_date[]" id="start_date0" class="form-control" placeholder="Start (e.g., Jan 2020)" value="{{ old('start_date.0') }}">
                  </div>
                  <div class="col-md-2">
                    <input type="text" name="end_date[]" id="end_date0" class="form-control" placeholder="End (e.g., Present)" value="{{ old('end_date.0') }}">
                  </div>
                </div>
                <div class="mb-2">
                  <textarea name="responsibilities[]" id="responsibilities0" rows="4" class="form-control" placeholder="Responsibilities / achievements (one per line)">{{ old('responsibilities.0') }}</textarea>
                </div>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="showExperienceModalForIndex(0)"><i class="bx bx-sparkles"></i> Generate with AI</button>
                  <button type="button" class="btn btn-sm btn-danger" onclick="document.getElementById('experienceWrapper0').remove()"><i class="bx bx-trash"></i> Remove</button>
                </div>
              </div>
            </div>
            <small class="text-muted">Example: Senior Developer at ABC Corp (2020-Present) - Led development team and managed projects</small>
            @error('experience.0')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <!-- Skills -->
        <div class="card mb-4">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0"><i class="bx bx-star"></i> Skills</h6>
              <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#skillsAIModal">
                <i class="bx bx-sparkles"></i> Generate with AI
              </button>
            </div>
            <textarea name="skills"
                      rows="4"
                      class="form-control @error('skills') is-invalid @enderror"
                      id="skillsField"
                      placeholder="List your skills...">{{ old('skills') }}</textarea>
            <small class="text-muted">Example: PHP, Laravel, Vue.js, MySQL, AWS, Docker</small>
            @error('skills')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <!-- Education -->
        <div class="card mb-4">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0"><i class="bx bx-book"></i> Education</h6>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addEducationField()">
                <i class="bx bx-plus"></i> Add More
              </button>
            </div>
            <div id="educationContainer">
              <div class="mb-4 p-3 border-bottom" id="educationWrapper0">
                <label class="form-label fw-500 mt-2 mb-2">Education 1 <span class="badge bg-secondary ms-2">Entry</span></label>
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
                <div class="mb-2">
                  <textarea name="education_details[]" id="education_details0" rows="3" class="form-control" placeholder="Details / honors (one per line)">{{ old('education_details.0') }}</textarea>
                </div>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="showEducationModalForIndex(0)"><i class="bx bx-sparkles"></i> Generate with AI</button>
                  <button type="button" class="btn btn-sm btn-danger" onclick="document.getElementById('educationWrapper0').remove()"><i class="bx bx-trash"></i> Remove</button>
                </div>
              </div>
            </div>
            <small class="text-muted">Example: BS Computer Science, XYZ University (2018) - GPA: 3.8/4.0</small>
            @error('education.0')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <!-- Submit -->
        <div class="alert alert-info">
          <i class="bx bx-info-circle me-2"></i>
          Your resume preview will be generated and opened in a new tab!
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100" id="generateBtn">
          <i class="bx bx-show me-1"></i> Generate Preview
        </button>
      </form>
    </div>
  </div>

  <!-- AI Modal for Experience -->
  <div class="modal fade" id="experienceAIModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bx bx-sparkles text-warning"></i> Generate Experience with AI
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="experienceAIForm">
            <div class="mb-3">
              <label class="form-label">Job Title</label>
              <input type="text" class="form-control" id="aiJobTitle" placeholder="e.g., Senior Developer">
            </div>
            <div class="mb-3">
              <label class="form-label">Company</label>
              <input type="text" class="form-control" id="aiCompany" placeholder="e.g., Tech Corp">
            </div>
            <div class="mb-3">
              <label class="form-label">Years of Experience</label>
              <input type="number" class="form-control" id="aiYears" placeholder="e.g., 5" min="0">
            </div>
            <div class="mb-3">
              <label class="form-label">Key Responsibilities</label>
              <textarea class="form-control" id="aiResponsibilities" rows="3" placeholder="e.g., Led team, managed projects..."></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="generateExperienceAI()" id="experienceAIBtn">
            <i class="bx bx-sparkles me-1"></i> Generate
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- AI Modal for Skills -->
  <div class="modal fade" id="skillsAIModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bx bx-sparkles text-warning"></i> Generate Skills with AI
          </h5>
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
            <div class="mb-3">
              <label class="form-label">Technologies / Fields</label>
              <textarea class="form-control" id="aiSkillsFields" rows="2" placeholder="e.g., PHP, Laravel, React, AWS..."></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="generateSkillsAI()" id="skillsAIBtn">
            <i class="bx bx-sparkles me-1"></i> Generate
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- AI Modal for Education -->
  <div class="modal fade" id="educationAIModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bx bx-sparkles text-warning"></i> Generate Education with AI
          </h5>
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
            <div class="mb-3">
              <label class="form-label">Graduation Year</label>
              <input type="number" class="form-control" id="aiGraduationYear" placeholder="e.g., 2020" min="1950">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="generateEducationAI()" id="educationAIBtn">
            <i class="bx bx-sparkles me-1"></i> Generate
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- AI Modal for Summary -->
  <div class="modal fade" id="summaryAIModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bx bx-sparkles text-warning"></i> Generate Professional Summary with AI
          </h5>
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
            <div class="mb-3">
              <label class="form-label">Career Goal / Focus</label>
              <input type="text" class="form-control" id="aiSummaryGoal" placeholder="e.g., Seeking senior developer roles in fintech">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="generateSummaryAI()" id="summaryAIBtn">
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

    // Form submission
    document.getElementById('resumeForm').addEventListener('submit', function() {
      const btn = document.getElementById('generateBtn');
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating PDF...';
      btn.disabled = true;
    });

    // Show experience modal for a specific index
    function showExperienceModalForIndex(index) {
      currentExperienceIndex = index;
      // Clear the modal fields
      document.getElementById('aiJobTitle').value = '';
      document.getElementById('aiCompany').value = '';
      document.getElementById('aiYears').value = '';
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
      const jobTitle = document.getElementById('aiJobTitle').value;
      const company = document.getElementById('aiCompany').value;
      const years = document.getElementById('aiYears').value;
      const responsibilities = document.getElementById('aiResponsibilities').value;

      if (!jobTitle || !company || !years) {
        alert('Please fill in all required fields');
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
            job_title: jobTitle,
            company: company,
            years: years,
            responsibilities: responsibilities
          })
        });

        const data = await response.json();
        if (data.success) {
          // If current index specified, fill structured fields at that index
          if (currentExperienceIndex !== null) {
            const idx = currentExperienceIndex;
            const titleEl = document.getElementById('job_title' + idx);
            const companyEl = document.getElementById('company' + idx);
            const respEl = document.getElementById('responsibilities' + idx);
            if (titleEl) titleEl.value = jobTitle;
            if (companyEl) companyEl.value = company;
            if (respEl) respEl.value = data.content;
            currentExperienceIndex = null;
          } else {
            // Find first empty structured block
            const firstTitle = document.getElementById('job_title0');
            const firstResp = document.getElementById('responsibilities0');
            if (firstTitle && firstTitle.value.trim() === '' && firstResp && firstResp.value.trim() === '') {
              firstTitle.value = jobTitle;
              document.getElementById('company0').value = company;
              firstResp.value = data.content;
            } else {
              // Add new structured block and fill it
              addExperienceField();
              const newIdx = experienceCount - 1;
              const titleEl = document.getElementById('job_title' + newIdx);
              const companyEl = document.getElementById('company' + newIdx);
              const respEl = document.getElementById('responsibilities' + newIdx);
              if (titleEl) titleEl.value = jobTitle;
              if (companyEl) companyEl.value = company;
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
      fieldWrapper.className = 'mb-4 p-3 border-top';
      fieldWrapper.id = 'experienceWrapper' + idx;

      const label = document.createElement('label');
      label.className = 'form-label fw-500 mt-2 mb-2';
      label.innerHTML = 'Experience ' + (idx + 1) + ' <span class="badge bg-secondary ms-2">Entry</span>';

      const row = document.createElement('div');
      row.className = 'row g-2 mb-2';

      const col1 = document.createElement('div'); col1.className = 'col-md-4';
      const jobInput = document.createElement('input'); jobInput.type = 'text'; jobInput.name = 'job_title[]'; jobInput.id = 'job_title' + idx; jobInput.className = 'form-control'; jobInput.placeholder = 'Job Title (e.g., Senior Developer)';
      col1.appendChild(jobInput);

      const col2 = document.createElement('div'); col2.className = 'col-md-4';
      const compInput = document.createElement('input'); compInput.type = 'text'; compInput.name = 'company[]'; compInput.id = 'company' + idx; compInput.className = 'form-control'; compInput.placeholder = 'Company (e.g., Tech Corp)';
      col2.appendChild(compInput);

      const col3 = document.createElement('div'); col3.className = 'col-md-2';
      const startInput = document.createElement('input'); startInput.type = 'text'; startInput.name = 'start_date[]'; startInput.id = 'start_date' + idx; startInput.className = 'form-control'; startInput.placeholder = 'Start (e.g., Jan 2020)';
      col3.appendChild(startInput);

      const col4 = document.createElement('div'); col4.className = 'col-md-2';
      const endInput = document.createElement('input'); endInput.type = 'text'; endInput.name = 'end_date[]'; endInput.id = 'end_date' + idx; endInput.className = 'form-control'; endInput.placeholder = 'End (e.g., Present)';
      col4.appendChild(endInput);

      row.appendChild(col1); row.appendChild(col2); row.appendChild(col3); row.appendChild(col4);

      const respDiv = document.createElement('div'); respDiv.className = 'mb-2';
      const respArea = document.createElement('textarea'); respArea.name = 'responsibilities[]'; respArea.id = 'responsibilities' + idx; respArea.rows = 4; respArea.className = 'form-control'; respArea.placeholder = 'Responsibilities / achievements (one per line)';
      respDiv.appendChild(respArea);

      const btnDiv = document.createElement('div'); btnDiv.className = 'd-flex gap-2';
      const aiBtn = document.createElement('button'); aiBtn.type = 'button'; aiBtn.className = 'btn btn-sm btn-outline-primary'; aiBtn.innerHTML = '<i class="bx bx-sparkles"></i> Generate with AI';
      aiBtn.onclick = function() { showExperienceModalForIndex(idx); };
      const removeBtn = document.createElement('button'); removeBtn.type = 'button'; removeBtn.className = 'btn btn-sm btn-danger'; removeBtn.innerHTML = '<i class="bx bx-trash"></i> Remove'; removeBtn.onclick = function() { fieldWrapper.remove(); };
      btnDiv.appendChild(aiBtn); btnDiv.appendChild(removeBtn);

      fieldWrapper.appendChild(label);
      fieldWrapper.appendChild(row);
      fieldWrapper.appendChild(respDiv);
      fieldWrapper.appendChild(btnDiv);

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
      fieldWrapper.className = 'mb-4 p-3 border-top';
      fieldWrapper.id = 'educationWrapper' + idx;

      const label = document.createElement('label');
      label.className = 'form-label fw-500 mt-2 mb-2';
      label.innerHTML = 'Education ' + (idx + 1) + ' <span class="badge bg-secondary ms-2">Entry</span>';

      const row1 = document.createElement('div'); row1.className = 'row g-2 mb-2';
      const col1 = document.createElement('div'); col1.className = 'col-md-6';
      const degInput = document.createElement('input'); degInput.type = 'text'; degInput.name = 'degree[]'; degInput.id = 'degree' + idx; degInput.className = 'form-control'; degInput.placeholder = 'Degree (e.g., BSc Computer Science)';
      col1.appendChild(degInput);
      const col2 = document.createElement('div'); col2.className = 'col-md-6';
      const fieldInput = document.createElement('input'); fieldInput.type = 'text'; fieldInput.name = 'field_of_study[]'; fieldInput.id = 'field_of_study' + idx; fieldInput.className = 'form-control'; fieldInput.placeholder = 'Field of Study';
      col2.appendChild(fieldInput);
      row1.appendChild(col1); row1.appendChild(col2);

      const row2 = document.createElement('div'); row2.className = 'row g-2 mb-2';
      const col3 = document.createElement('div'); col3.className = 'col-md-8';
      const univInput = document.createElement('input'); univInput.type = 'text'; univInput.name = 'university[]'; univInput.id = 'university' + idx; univInput.className = 'form-control'; univInput.placeholder = 'University / Institution';
      col3.appendChild(univInput);
      const col4 = document.createElement('div'); col4.className = 'col-md-4';
      const gradInput = document.createElement('input'); gradInput.type = 'text'; gradInput.name = 'graduation_year[]'; gradInput.id = 'graduation_year' + idx; gradInput.className = 'form-control'; gradInput.placeholder = 'Year (e.g., 2018)';
      col4.appendChild(gradInput);
      row2.appendChild(col3); row2.appendChild(col4);

      const detailsDiv = document.createElement('div'); detailsDiv.className = 'mb-2';
      const detailsArea = document.createElement('textarea'); detailsArea.name = 'education_details[]'; detailsArea.id = 'education_details' + idx; detailsArea.rows = 3; detailsArea.className = 'form-control'; detailsArea.placeholder = 'Details / honors (one per line)';
      detailsDiv.appendChild(detailsArea);

      const btnDiv = document.createElement('div'); btnDiv.className = 'd-flex gap-2';
      const aiBtn = document.createElement('button'); aiBtn.type = 'button'; aiBtn.className = 'btn btn-sm btn-outline-primary'; aiBtn.innerHTML = '<i class="bx bx-sparkles"></i> Generate with AI'; aiBtn.onclick = function() { showEducationModalForIndex(idx); };
      const removeBtn = document.createElement('button'); removeBtn.type = 'button'; removeBtn.className = 'btn btn-sm btn-danger'; removeBtn.innerHTML = '<i class="bx bx-trash"></i> Remove'; removeBtn.onclick = function() { fieldWrapper.remove(); };
      btnDiv.appendChild(aiBtn); btnDiv.appendChild(removeBtn);

      fieldWrapper.appendChild(label);
      fieldWrapper.appendChild(row1);
      fieldWrapper.appendChild(row2);
      fieldWrapper.appendChild(detailsDiv);
      fieldWrapper.appendChild(btnDiv);

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
        padding: 1rem;
      }

      .form-label {
        font-size: 0.9rem;
      }

      .btn-sm {
        font-size: 0.8rem;
        padding: 0.375rem 0.75rem;
      }
    }
  </style>
</x-layouts.app>
