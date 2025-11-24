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
            id="resumeForm">
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
              <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#experienceAIModal">
                  <i class="bx bx-sparkles"></i> Generate
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addExperienceField()">
                  <i class="bx bx-plus"></i> Add More
                </button>
              </div>
            </div>
            <div id="experienceContainer">
              <div class="mb-4 p-3 border-bottom">
                <label class="form-label fw-500 mt-2 mb-2">Experience 1 <span class="badge bg-secondary ms-2">Entry</span></label>
                <textarea name="experience[]"
                          rows="4"
                          class="form-control @error('experience.0') is-invalid @enderror"
                          id="experienceField0"
                          placeholder="Add your work history...">{{ old('experience.0', old('experience')) }}</textarea>
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
              <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#educationAIModal">
                  <i class="bx bx-sparkles"></i> Generate
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addEducationField()">
                  <i class="bx bx-plus"></i> Add More
                </button>
              </div>
            </div>
            <div id="educationContainer">
              <div class="mb-4 p-3 border-bottom">
                <label class="form-label fw-500 mt-2 mb-2">Education 1 <span class="badge bg-secondary ms-2">Entry</span></label>
                <textarea name="education[]"
                          rows="3"
                          class="form-control @error('education.0') is-invalid @enderror"
                          id="educationField0"
                          placeholder="Add your education...">{{ old('education.0', old('education')) }}</textarea>
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
          Your resume will be generated and opened in a new tab!
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100" id="generateBtn">
          <i class="bx bx-file me-1"></i> Generate Resume PDF
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
    // Form submission
    document.getElementById('resumeForm').addEventListener('submit', function() {
      const btn = document.getElementById('generateBtn');
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating PDF...';
      btn.disabled = true;
    });

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
          // Add new experience field with generated content
          addExperienceField();
          const experienceContainer = document.getElementById('experienceContainer');
          const fields = experienceContainer.querySelectorAll('textarea');
          const lastField = fields[fields.length - 2]; // Get the newly added textarea (before remove button)
          lastField.value = data.content;
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

    // Add multiple experience entries
    let experienceCount = 1;
    function addExperienceField() {
      const container = document.getElementById('experienceContainer');

      // Create wrapper div for field and buttons
      const fieldWrapper = document.createElement('div');
      fieldWrapper.className = 'mb-4 p-3 border-top';
      fieldWrapper.id = 'experienceWrapper' + experienceCount;

      // Add label
      const label = document.createElement('label');
      label.className = 'form-label fw-500 mt-2 mb-2';
      label.innerHTML = 'Experience ' + (experienceCount + 1) + ' <span class="badge bg-secondary ms-2">Entry</span>';

      // Add button group for AI and Remove
      const btnGroup = document.createElement('div');
      btnGroup.className = 'd-flex gap-2 mb-3';

      const aiBtn = document.createElement('button');
      aiBtn.type = 'button';
      aiBtn.className = 'btn btn-sm btn-outline-primary';
      aiBtn.innerHTML = '<i class="bx bx-sparkles"></i> Generate with AI';
      const fieldId = 'experienceField' + experienceCount;
      aiBtn.onclick = function() {
        generateExperienceAIForField(fieldId);
      };

      // Create textarea
      const newField = document.createElement('textarea');
      newField.className = 'form-control';
      newField.name = 'experience[]';
      newField.rows = 4;
      newField.placeholder = 'Add your work history...';
      newField.id = fieldId;

      // Create remove button
      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'btn btn-sm btn-danger';
      removeBtn.innerHTML = '<i class="bx bx-trash"></i> Remove';
      removeBtn.onclick = function() {
        fieldWrapper.remove();
      };

      btnGroup.appendChild(aiBtn);
      btnGroup.appendChild(removeBtn);

      fieldWrapper.appendChild(label);
      fieldWrapper.appendChild(btnGroup);
      fieldWrapper.appendChild(newField);

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
          // Add new education field with generated content
          addEducationField();
          const educationContainer = document.getElementById('educationContainer');
          const fields = educationContainer.querySelectorAll('textarea');
          const lastField = fields[fields.length - 2]; // Get the newly added textarea (before remove button)
          lastField.value = data.content;
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

    // Add multiple education entries
    let educationCount = 1;
    function addEducationField() {
      const container = document.getElementById('educationContainer');

      // Create wrapper div for field and buttons
      const fieldWrapper = document.createElement('div');
      fieldWrapper.className = 'mb-4 p-3 border-top';
      fieldWrapper.id = 'educationWrapper' + educationCount;

      // Add label
      const label = document.createElement('label');
      label.className = 'form-label fw-500 mt-2 mb-2';
      label.innerHTML = 'Education ' + (educationCount + 1) + ' <span class="badge bg-secondary ms-2">Entry</span>';

      // Add button group for AI and Remove
      const btnGroup = document.createElement('div');
      btnGroup.className = 'd-flex gap-2 mb-3';

      const aiBtn = document.createElement('button');
      aiBtn.type = 'button';
      aiBtn.className = 'btn btn-sm btn-outline-primary';
      aiBtn.innerHTML = '<i class="bx bx-sparkles"></i> Generate with AI';
      const fieldId = 'educationField' + educationCount;
      aiBtn.onclick = function() {
        generateEducationAIForField(fieldId);
      };

      // Create textarea
      const newField = document.createElement('textarea');
      newField.className = 'form-control';
      newField.name = 'education[]';
      newField.rows = 3;
      newField.placeholder = 'Add your education...';
      newField.id = fieldId;

      // Create remove button
      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'btn btn-sm btn-danger';
      removeBtn.innerHTML = '<i class="bx bx-trash"></i> Remove';
      removeBtn.onclick = function() {
        fieldWrapper.remove();
      };

      btnGroup.appendChild(aiBtn);
      btnGroup.appendChild(removeBtn);

      fieldWrapper.appendChild(label);
      fieldWrapper.appendChild(btnGroup);
      fieldWrapper.appendChild(newField);

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

    // Generate experience for a specific field
    async function generateExperienceAIForField(fieldId) {
      const jobTitle = document.getElementById('aiJobTitle').value;
      const company = document.getElementById('aiCompany').value;
      const years = document.getElementById('aiYears').value;
      const responsibilities = document.getElementById('aiResponsibilities').value;

      if (!jobTitle || !company || !years) {
        alert('Please fill in all required fields in the modal');
        return;
      }

      // Show modal for input
      const modal = new bootstrap.Modal(document.getElementById('experienceAIModal'));
      modal.show();

      const btn = document.getElementById('experienceAIBtn');
      const originalText = btn.innerHTML;
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
          const targetField = document.getElementById(fieldId);
          if (targetField) {
            targetField.value = data.content;
            modal.hide();
          } else {
            alert('Field not found');
          }
        } else {
          alert('Error: ' + data.message);
        }
      } catch (error) {
        alert('Error generating content: ' + error.message);
      } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
      }
    }

    // Generate education for a specific field
    async function generateEducationAIForField(fieldId) {
      const degree = document.getElementById('aiDegree').value;
      const fieldOfStudy = document.getElementById('aiFieldOfStudy').value;
      const university = document.getElementById('aiUniversity').value;
      const graduationYear = document.getElementById('aiGraduationYear').value;

      if (!degree || !fieldOfStudy || !university || !graduationYear) {
        // Show modal for input
        const modal = new bootstrap.Modal(document.getElementById('educationAIModal'));
        modal.show();
        return;
      }

      const btn = document.getElementById('educationAIBtn');
      const originalText = btn.innerHTML;
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
          const targetField = document.getElementById(fieldId);
          if (targetField) {
            targetField.value = data.content;
            const modal = bootstrap.Modal.getInstance(document.getElementById('educationAIModal'));
            if (modal) modal.hide();
          } else {
            alert('Field not found');
          }
        } else {
          alert('Error: ' + data.message);
        }
      } catch (error) {
        alert('Error generating content: ' + error.message);
      } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
      }
    }
  </script>
</x-layouts.app>
