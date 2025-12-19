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
            'email' => __('auth.failed'),
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

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
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
    <x-auth-header :title="__('Welcome to :app!', ['app' => config('app.name')])" :description="__('Enter your email and password below to log in')" />

    <div class="text-center mb-5">
        <img
            src="{{ asset('assets/img/illustrations/laravel-livewire-sneat.png') }}"
            alt="Dashboard preview"
            class="img-fluid rounded-4 shadow-sm"
        >
    </div>

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


    <form wire:submit="login" class="mb-5">
        <div class="mb-5">
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

        <div class="mb-5 form-password-toggle">
            <div class="d-flex justify-content-between">
                <label for="password" class="form-label">{{ __('Password') }}</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate>
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

        <div class="mb-6">
            <div class="d-flex justify-content-between mt-6">
                <div class="form-check mb-0 ms-2">
                    <input wire:model="remember" type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <button type="submit" class="btn btn-primary btn-lg d-grid w-100">{{ __('Login') }}</button>
        </div>
    </form>

    @if (Route::has('register'))
        <p class="text-center">
            <span>{{ __('New on our platform?') }}</span>
            <a href="{{ route('register') }}" wire:navigate>
                <span>{{ __('Create an account') }}</span>
            </a>
        </p>
    @endif

    <div class="card border-0 shadow-sm mt-4" style="background: #f8fafc;">
        <div class="card-body p-4">
            <div class="d-flex align-items-start mb-3">
                <div class="avatar avatar-lg bg-warning bg-opacity-25 rounded me-3" style="min-width: 50px;">
                    <i class="bx bxs-crown text-warning" style="font-size: 1.75rem;"></i>
                </div>
                <div>
                    <h6 class="mb-1 text-dark">Premium Features</h6>
                    <p class="small text-muted mb-0">Get more value when you’re ready</p>
                </div>
            </div>
            <ul class="text-muted small mb-3 ps-3">
                <li class="mb-2">Unlimited resumes</li>
                <li class="mb-2">Premium templates</li>
                <li class="mb-0">Priority support</li>
            </ul>
            <a href="{{ route('user.pricing') }}" class="btn btn-outline-primary btn-lg w-100">
                View Plans
            </a>
        </div>
    </div>




</div>
