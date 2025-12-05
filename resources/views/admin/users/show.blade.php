<x-layouts.app :title="$title ?? 'User Details'">
    <div class="container-fluid">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bx bx-arrow-back"></i> Back to Users
            </a>
        </div>

        <div class="row">
            <!-- User Profile Card -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <!-- Avatar -->
                        <div class="avatar-circle-lg bg-primary text-white mx-auto mb-3">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>

                        <h4 class="mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-3">
                            <i class="bx bx-envelope"></i> {{ $user->email }}
                        </p>

                        <!-- Status Badges -->
                        <div class="mb-3">
                            @if($user->is_active)
                                <span class="badge bg-success">
                                    <i class="bx bx-check-circle"></i> Active
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="bx bx-x-circle"></i> Inactive
                                </span>
                            @endif

                            @if($user->role === 'admin')
                                <span class="badge bg-danger">
                                    <i class="bx bx-shield"></i> Administrator
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bx bx-user"></i> User
                                </span>
                            @endif

                            @if($user->id === auth()->id())
                                <span class="badge bg-info">
                                    <i class="bx bx-star"></i> You
                                </span>
                            @endif
                        </div>

                        <hr>

                        <!-- Stats -->
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <i class="bx bx-file fs-3 text-primary"></i>
                                <h3 class="mb-0">{{ $user->resumes_count }}</h3>
                                <small class="text-muted">Total Resumes</small>
                            </div>
                            <div class="col-6">
                                <i class="bx bx-check-double fs-3 text-success"></i>
                                <h3 class="mb-0">{{ $resumes->where('status', 'completed')->count() }}</h3>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>

                        <hr>

                        <!-- User Info -->
                        <div class="text-start">
                            <div class="mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bx bx-calendar fs-5 text-primary me-2"></i>
                                    <div>
                                        <strong class="d-block">Joined</strong>
                                        <small class="text-muted">{{ $user->created_at->format('F d, Y') }}</small><br>
                                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>

                            @if($user->email_verified_at)
                                <div class="mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bx bx-check-shield fs-5 text-success me-2"></i>
                                        <div>
                                            <strong class="d-block">Email Verified</strong>
                                            <small class="text-muted">{{ $user->email_verified_at->format('M d, Y') }}</small>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bx bx-error fs-5 text-danger me-2"></i>
                                        <div>
                                            <strong class="d-block">Email Not Verified</strong>
                                            <small class="text-danger">Pending verification</small>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bx bx-time fs-5 text-secondary me-2"></i>
                                    <div>
                                        <strong class="d-block">Last Updated</strong>
                                        <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
                                <i class="bx bx-edit"></i> Edit User
                            </a>

                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle-status', $user->id) }}">
                                    @csrf
                                    <button type="submit" class="btn {{ $user->is_active ? 'btn-secondary' : 'btn-success' }} w-100">
                                        <i class="bx {{ $user->is_active ? 'bx-lock' : 'bx-lock-open' }}"></i>
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }} Account
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('admin.users.destroy', $user->id) }}"
                                      onsubmit="return confirm('⚠️ DELETE USER?\n\nThis will permanently delete:\n• User account\n• All resumes ({{ $user->resumes_count }})\n• All PDF files\n\nThis action CANNOT be undone!\n\nType DELETE to confirm:') && prompt('Type DELETE to confirm:') === 'DELETE'">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bx bx-trash"></i> Delete User Permanently
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-info mb-0">
                                    <i class="bx bx-info-circle"></i> <small>This is your account</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumes Section -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bx bx-file text-primary"></i> Resumes
                                <span class="badge bg-secondary rounded-pill">{{ $resumes->total() }}</span>
                            </h5>
                        </div>
                    </div>

                    <div class="card-body">
                        @if($resumes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Template</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th width="100" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($resumes as $resume)
                                            <tr>
                                                <td>
                                                    @if($resume->template)
                                                        <div class="d-flex align-items-center gap-2">
                                                            @if($resume->template->preview_image)
                                                                <img src="{{ asset($resume->template->preview_image) }}"
                                                                     alt="Template"
                                                                     class="template-thumb">
                                                            @else
                                                                <div class="template-thumb bg-light d-flex align-items-center justify-content-center">
                                                                    <i class="bx bx-file"></i>
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <strong class="d-block">{{ $resume->template->name }}</strong>
                                                                <small class="text-muted">{{ $resume->template->category }}</small>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">
                                                            <i class="bx bx-error"></i> Template Deleted
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($resume->data['name']))
                                                        <strong>{{ $resume->data['name'] }}</strong><br>
                                                        @if(isset($resume->data['email']))
                                                            <small class="text-muted">
                                                                <i class="bx bx-envelope"></i> {{ $resume->data['email'] }}
                                                            </small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">No name</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($resume->status === 'completed')
                                                        <span class="badge bg-success">
                                                            <i class="bx bx-check"></i> Completed
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning">
                                                            <i class="bx bx-time"></i> Draft
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <i class="bx bx-calendar"></i> {{ $resume->created_at->format('M d, Y') }}<br>
                                                        {{ $resume->created_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        @if($resume->generated_pdf_path)
                                                            <a href="{{ route('admin.users.download-resume', [$user->id, $resume->id]) }}"
                                                               class="btn btn-sm btn-outline-success"
                                                               data-bs-toggle="tooltip"
                                                               title="Download PDF">
                                                                <i class="bx bx-download"></i>
                                                            </a>
                                                        @endif

                                                        <form method="POST"
                                                              action="{{ route('admin.users.delete-resume', [$user->id, $resume->id]) }}"
                                                              class="d-inline"
                                                              onsubmit="return confirm('Delete this resume?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    data-bs-toggle="tooltip"
                                                                    title="Delete Resume">
                                                                <i class="bx bx-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($resumes->hasPages())
                                <div class="mt-3">
                                    {{ $resumes->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <i class="bx bx-file-blank fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted mb-0">This user hasn't created any resumes yet</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Resume Data Preview -->
                @if($resumes->count() > 0 && $resumes->first()->data)
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="bx bx-detail text-primary"></i> Latest Resume Preview
                            </h5>
                        </div>
                        <div class="card-body">
                            @php $latestData = $resumes->first()->data; @endphp

                            <div class="row g-3">
                                <!-- Personal Info -->
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">
                                        <i class="bx bx-user"></i> Personal Information
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="100"><strong>Name:</strong></td>
                                            <td>{{ $latestData['name'] ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $latestData['email'] ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $latestData['phone'] ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Location:</strong></td>
                                            <td>{{ $latestData['location'] ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Summary -->
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">
                                        <i class="bx bx-detail"></i> Professional Summary
                                    </h6>
                                    <p class="small">
                                        {{ $latestData['summary'] ?? 'No summary provided' }}
                                    </p>
                                </div>

                                <!-- Skills -->
                                @if(isset($latestData['skills']) && count($latestData['skills']) > 0)
                                    <div class="col-12">
                                        <h6 class="text-muted mb-2">
                                            <i class="bx bx-code-alt"></i> Skills
                                        </h6>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($latestData['skills'] as $skill)
                                                <span class="badge bg-secondary">{{ $skill }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .avatar-circle-lg {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 40px;
        }

        .template-thumb {
            width: 40px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }
    </style>

    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</x-layouts.app>
