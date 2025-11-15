<x-layouts.app :title="$title ?? 'Edit User'">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Back Button -->
                <div class="mb-3">
                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back"></i> Back to User Details
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">✏️ Edit User: {{ $user->name }}</h5>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                            @csrf
                            @method('PUT')

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required>
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
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password"
                                       placeholder="Leave blank to keep current password">
                                <small class="text-muted">Minimum 8 characters. Leave blank to keep current password.</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation"
                                       placeholder="Confirm new password">
                            </div>

                            <hr>

                            <!-- Role -->
                            <div class="mb-3">
                                <label for="role" class="form-label">User Role</label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" 
                                        name="role">
                                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>
                                        Regular User
                                    </option>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                                        Administrator
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Admins have full access to the admin panel.</small>
                            </div>

                            <!-- Status -->
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                           {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Account
                                    </label>
                                </div>
                                <small class="text-muted">Inactive users cannot log in to the system.</small>
                                @if($user->id === auth()->id())
                                    <br><small class="text-warning">You cannot deactivate your own account.</small>
                                @endif
                            </div>

                            <hr>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save"></i> Update User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Danger Zone -->
                @if($user->id !== auth()->id())
                    <div class="card border-danger mt-3">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">⚠️ Danger Zone</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-3">
                                <strong>Delete this user permanently</strong><br>
                                <small class="text-muted">Once deleted, all user data and resumes will be permanently removed. This action cannot be undone.</small>
                            </p>
                            <form method="POST" 
                                  action="{{ route('admin.users.destroy', $user->id) }}"
                                  onsubmit="return confirm('Are you absolutely sure you want to delete this user? This will delete:\n\n• User account\n• All their resumes\n• All their data\n\nThis action cannot be undone!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bx bx-trash"></i> Delete User Permanently
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>