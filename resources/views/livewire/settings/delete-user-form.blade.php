<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

@section('title', 'Delete account')

<section>
    <hr class="my-5 w-50" />
    <div class="mb-4 p-4 border border-danger rounded bg-light">
        <h5 class="mb-2 text-danger d-flex align-items-center">
            <i class="bx bx-error-circle me-2"></i>
            {{ __('Danger Zone') }}
        </h5>
        <p class="text-muted mb-3">{{ __('Once you delete your account, there is no going back. All your data will be permanently removed.') }}</p>
        
        <!-- Button to open the modal -->
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
            <i class="bx bx-trash me-1"></i>
            {{ __('Delete Account') }}
        </button>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">
                        <i class="bx bx-error-circle me-2"></i>
                        {{ __('Confirm Account Deletion') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger mb-3">
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    <p>{{ __('All of your resumes, cover letters, interview practice sessions, and account data will be permanently deleted. Please enter your password to confirm.') }}</p>

                    <form wire:submit="deleteUser" class="space-y-3">
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input type="password" id="password" wire:model="password" class="form-control" required />
                        </div>

                        <div class="d-flex justify-content-between gap-2 mt-4">
                            <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="modal">
                                <i class="bx bx-x me-1"></i>
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="btn btn-danger flex-fill">
                                <i class="bx bx-trash me-1"></i>
                                {{ __('Yes, Delete My Account') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
