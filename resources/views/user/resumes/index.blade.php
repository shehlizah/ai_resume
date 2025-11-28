<x-layouts.app :title="'My Resumes'">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">📄 My Resumes</h5>
          <a href="{{ route('user.resumes.choose') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Create New Resume
          </a>
        </div>
        
        {{-- Warning banner --}}
@if(!$hasActivePackage)
    <div class="alert alert-warning">
        <strong>No Active Package</strong> - 
        <a href="{{ route('packages') }}">Subscribe</a> to create and download resumes.
    </div>
@endif

{{-- Create button --}}
@if($hasActivePackage)
    <a href="{{ route('user.resumes.create') }}" class="btn btn-primary">
        Create Resume
    </a>
@else
    <button class="btn btn-secondary" disabled>
        <i class="fas fa-lock"></i> Create Resume (Subscribe First)
    </button>
@endif

{{-- Download button --}}
@if($hasActivePackage)
    <a href="{{ route('user.resumes.download', $resume->id) }}" class="btn btn-sm btn-primary">
        Download
    </a>
@else
    <button class="btn btn-sm btn-secondary" disabled>
        <i class="fas fa-lock"></i> Locked
    </button>
@endif

        <div class="card-body">
          @if($resumes->isEmpty())
            <div class="text-center py-5">
              <i class="bx bx-file" style="font-size: 64px; color: #ddd;"></i>
              <h5 class="mt-3 text-muted">No resumes yet</h5>
              <p class="text-muted">Create your first professional resume now!</p>
              <a href="{{ route('user.resumes.choose') }}" class="btn btn-primary mt-2">
                <i class="bx bx-plus me-1"></i> Choose Template
              </a>
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Resume Details</th>
                    <th>Template</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($resumes as $resume)
                    @php
                      $resumeData = is_array($resume->data) ? $resume->data : json_decode($resume->data, true);
                    @endphp
                    <tr>
                      <td>
                        <div class="d-flex align-items-center">
                          <i class="bx bx-file-blank me-2" style="font-size: 24px; color: #667eea;"></i>
                          <div>
                            <div class="fw-semibold">{{ $resumeData['name'] ?? 'Resume' }}</div>
                            <small class="text-muted">{{ $resumeData['title'] ?? '' }}</small>
                          </div>
                        </div>
                      </td>
                     <td>
                      <span class="badge {{ $resume->template ? 'bg-label-primary' : 'bg-label-warning' }}">
                        {{ $resume->template?->name ?? 'Template Deleted' }}
                      </span>
                    </td>
                      <td>
                        @if($resume->status === 'completed')
                          <span class="badge bg-label-success">
                            <i class="bx bx-check-circle"></i> Completed
                          </span>
                        @elseif($resume->status === 'pending')
                          <span class="badge bg-label-warning">
                            <i class="bx bx-time"></i> Pending
                          </span>
                        @else
                          <span class="badge bg-label-secondary">
                            {{ ucfirst($resume->status) }}
                          </span>
                        @endif
                      </td>
                      <td>
                        <small class="text-muted">
                          {{ $resume->created_at->format('M d, Y') }}<br>
                          <span class="text-muted">{{ $resume->created_at->format('h:i A') }}</span>
                        </small>
                      </td>
                      <td>
                        <div class="dropdown">
                          <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('user.resumes.view', $resume->id) }}" 
                               class="dropdown-item"
                               target="_blank">
                              <i class="bx bx-show me-2"></i> View PDF
                            </a>
                            <a href="{{ route('user.resumes.download', $resume->id) }}" 
                               class="dropdown-item">
                              <i class="bx bx-download me-2"></i> Download PDF
                            </a>
                            <a href="{{ route('user.resumes.fill', $resume->template_id) }}" 
                               class="dropdown-item">
                              <i class="bx bx-copy me-2"></i> Create Similar
                            </a>
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('user.resumes.destroy', $resume->id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this resume?')">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="dropdown-item text-danger">
                                <i class="bx bx-trash me-2"></i> Delete
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
    </div>
  </div>
</x-layouts.app>