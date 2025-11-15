<x-layouts.app :title="$title ?? 'Create New User'">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Back Button -->
                <div class="mb-3">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back"></i> Back to Users
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">➕ Create New User</h5>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.store') }}">
                            @csrf

                            <div class="alert alert-info">
                                <i class="bx bx-info-circle"></i>
                                <strong>Note:</strong> Users created by admin are automatically email verified and can login immediately.
                            </div>

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="John Doe"
                                       required 
                                       autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="user@example.com"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password"
                                       placeholder="Minimum 8 characters"
                                       required>
                                <small class="text-muted">Minimum 8 characters.</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation"
                                       placeholder="Re-enter password"
                                       required>
                            </div>

                            <hr>

                            <!-- Role -->
                            <div class="mb-3">
                                <label for="role" class="form-label">User Role</label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" 
                                        name="role">
                                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>
                                        Regular User
                                    </option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                                        Administrator
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <strong>Regular User:</strong> Can browse templates and create resumes.<br>
                                    <strong>Administrator:</strong> Full access to admin panel and user management.
                                </small>
                            </div>

                            <!-- Status -->
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Account
                                    </label>
                                </div>
                                <small class="text-muted">Inactive users cannot log in to the system. Usually, keep this checked.</small>
                            </div>

                            <hr>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-user-plus"></i> Create User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">💡 Quick Tips</h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Users created here are automatically verified and can login immediately</li>
                            <li>You can send login credentials to the user via email manually</li>
                            <li>Regular users can only access the user panel (browse templates, create resumes)</li>
                            <li>Admin users have full access to the admin panel including user management</li>
                            <li>You can change user role and status anytime from the edit page</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>