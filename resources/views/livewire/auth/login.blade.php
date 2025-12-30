<?php

namespace App\Http\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
   public function login(): void
{
    $this->validate();
    $this->ensureIsNotRateLimited();

    // Attempt login including is_active = 1
    $credentials = [
        'email' => $this->email,
        'password' => $this->password,
        'is_active' => 1, // only allow active users
    ];

    if (! Auth::attempt($credentials, $this->remember)) {
        // Check if the user exists but inactive
        $user = \App\Models\User::where('email', $this->email)->first();
        if ($user && ! (bool)$user->is_active) {
            session()->flash('deactivated', 'Your account is deactivated. Please contact admin to activate.');
            return;
        }

        RateLimiter::hit($this->throttleKey());

        throw \Illuminate\Validation\ValidationException::withMessages([
            'email' => 'The provided credentials are incorrect.',
        ]);
    }

    RateLimiter::clear($this->throttleKey());
    Session::regenerate();

    $role = auth()->user()->role;

    $defaultRoute = $role === 'admin'
        ? route('admin.dashboard', absolute: false)
        : ($role === 'employer'
            ? route('company.dashboard', absolute: false)
            : route('user.dashboard', absolute: false));

    $this->redirectIntended(default: $defaultRoute, navigate: true);
}


    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) return;

        event(new Lockout(request()));
        $seconds = RateLimiter::availableIn($this->throttleKey());
        $minutes = ceil($seconds / 60);

        throw ValidationException::withMessages([
            'email' => "Too many login attempts. Please try again in {$minutes} minutes.",
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
};
?>

@section('title', 'Login Page')

@section('page-style')
@vite([
    'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

<div>
    <!-- Mobile Logo Header -->
    <div class="d-lg-none mb-4 pb-3 border-bottom">
        <a href="{{ url('/') }}" class="navbar-brand d-block">
            <img
                src="{{ asset('assets/img/logo.png') }}"
                alt="Logo"
                style="max-width: 110px;"
            >
        </a>
    </div>

    <x-auth-header :title="__('Welcome to :app!', ['app' => config('app.name')])" :description="__('Enter your email and password below to log in')" />

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-info mb-4">
            {{ session('status') }}
        </div>
    @endif

    @if (session()->has('deactivated'))
    <div class="alert alert-danger mb-4">
        {{ session('deactivated') }}
    </div>
@endif


    <form wire:submit="login" class="mb-6">
        <div class="mb-6">
            <label for="email" class="form-label">{{ __('Email or Username') }}</label>
            <input
                wire:model="email"
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                required
                autofocus
                autocomplete="email"
                placeholder="{{ __('Enter your email') }}"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-6 form-password-toggle">
            <div class="d-flex justify-content-between">
                <label for="password" class="form-label">{{ __('Password') }}</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate style="color: #2563EB; text-decoration: none;">
                        <span>{{ __('Forgot Password?') }}</span>
                    </a>
                @endif
            </div>
            <div class="input-group input-group-merge">
                <input
                    wire:model="password"
                    type="password"
                    class="form-control @error('password') is-invalid @enderror"
                    id="password"
                    required
                    autocomplete="current-password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                >
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-8">
            <div class="d-flex justify-content-between mt-8">
                <div class="form-check mb-0 ms-2">
                    <input wire:model="remember" type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <button type="submit" class="btn btn-primary d-grid w-100" style="background-color: #2563EB; border-color: #2563EB;">{{ __('Login') }}</button>
        </div>
    </form>

    @if (Route::has('register'))
        <p class="text-center">
            <span>{{ __('New on our platform?') }}</span>
            <a href="{{ route('register') }}" wire:navigate style="color: #2563EB; text-decoration: none;">
                <span>{{ __('Create an account') }}</span>
            </a>
        </p>
    @endif

    <style>
        /* Override primary color to blue */
        .text-primary, a.text-primary {
            color: #2563EB !important;
        }

        a.text-primary:hover,
        a[style*="color: #2563EB"]:hover {
            color: #1e40af !important;
        }

        .form-check-input:checked {
            background-color: #2563EB;
            border-color: #2563EB;
        }
    </style>





</div>
