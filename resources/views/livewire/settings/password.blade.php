<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

@section('title', 'Update password')

<section>
    @include('partials.settings-heading')

    <x-settings.layout :subheading="__('Ensure your account is using a long, random password to stay secure')">
        <form wire:submit="updatePassword" class="w-50">
            <div class="mb-3">
                <label for="current_password" class="form-label">{{ __('Current password') }}</label>
                <input type="password" id="current_password" wire:model="current_password" class="form-control" required autocomplete="current-password" />
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">{{ __('New password') }}</label>
                <input type="password" id="password" wire:model="password" class="form-control" required autocomplete="new-password" />
                <small class="text-muted d-block mt-1">Use at least 8 characters with a mix of letters, numbers, and symbols.</small>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                <input type="password" id="password_confirmation" wire:model="password_confirmation" class="form-control" required autocomplete="new-password" />
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary">{{ __('Update Password') }}</button>
                <x-action-message class="mb-0" on="password-updated">
                    <span class="text-success d-flex align-items-center">
                        <i class="bx bx-check-circle me-1"></i>
                        {{ __('Password updated successfully!') }}
                    </span>
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
