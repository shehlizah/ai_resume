<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $terms = false;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(route('user.dashboard', absolute: false), navigate: true);
    }
}; ?>

@section('title', 'Register Page')

@section('page-style')
@vite([
    'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

<div>
    <h4 class="mb-2" style="font-size: clamp(1.5rem, 5vw, 1.75rem);">Welcome to JOBSEASE! 👋</h4>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-info mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="register" class="mb-6">
        <div class="mb-4 mb-sm-6">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input
                wire:model="name"
                type="text"
                class="form-control @error('name') is-invalid @enderror"
                id="name"
                required
                autofocus
                autocomplete="name"
                placeholder="{{ __('Enter your name') }}"
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4 mb-sm-6">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input
                wire:model="email"
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                required
                autocomplete="email"
                placeholder="{{ __('Enter your email') }}"
            >
            <div class="form-text text-muted">We’ll never share your email.</div>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4 mb-sm-6 form-password-toggle">
            <label class="form-label" for="password">{{ __('Password') }}</label>
            <div class="input-group input-group-merge">
                <input
                    wire:model="password"
                    type="password"
                    class="form-control @error('password') is-invalid @enderror"
                    id="password"
                    required
                    autocomplete="new-password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                >
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-text text-muted">Minimum 8 characters.</div>
        </div>

        <div class="mb-4 mb-sm-6 form-password-toggle">
            <label class="form-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
            <div class="input-group input-group-merge">
                <input
                    wire:model="password_confirmation"
                    type="password"
                    class="form-control @error('password_confirmation') is-invalid @enderror"
                    id="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                >
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-4 mb-sm-8">
            <div class="form-check mb-0 ms-2">
                <input wire:model="terms" type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" id="terms">
                <label class="form-check-label" for="terms">
                    {{ __('I agree to') }}
                    <a href="javascript:void(0);" class="fw-semibold text-primary text-decoration-underline">{{ __('privacy policy & terms') }}</a>
                </label>
                @error('terms')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg d-grid w-100 mb-4 mb-sm-6 shadow-sm" style="font-size: clamp(0.95rem, 2.5vw, 1rem);">
            {{ __('Create Free Account') }}
        </button>
    </form>

    <!-- Why Join Section - Below Form (compact) -->
    <div class="why-join p-2 p-sm-3 bg-light border rounded-3 mb-3 lh-sm">
        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold mb-1 d-inline-block" style="font-size: clamp(0.78rem, 1.8vw, 0.82rem);">Why Join</span>
        <h6 class="mb-1 text-dark fw-semibold" style="font-size: clamp(0.9rem, 2vw, 1rem);">Get job-ready faster with AI-powered CV, interview practice, and job matching</h6>
        <p class="text-muted mb-1" style="font-size: clamp(0.8rem, 1.8vw, 0.9rem);">Create your CV, practice interviews, and find matching jobs — all in one place.</p>
        <ul class="text-muted small mb-0 ps-3" style="font-size: clamp(0.78rem, 1.8vw, 0.88rem);">
            <li class="mb-0">Create a professional CV in minutes</li>
            <li class="mb-0">Practice interviews with AI feedback</li>
            <li class="mb-0">Find jobs matching your skills & location</li>
        </ul>
    </div>

    <p class="text-center mb-2 mb-sm-3" style="font-size: clamp(0.85rem, 2vw, 0.95rem);">
        <span>{{ __('Are you an employer?') }}</span>
        <a href="{{ route('register.employer') }}" wire:navigate>
            <span>{{ __('Register to post jobs') }}</span>
        </a>
    </p>

    <p class="text-center" style="font-size: clamp(0.85rem, 2vw, 0.95rem);">
        <span>{{ __('Already have an account?') }}</span>
        <a href="{{ route('login') }}" wire:navigate>
            <span>{{ __('Sign in instead') }}</span>
        </a>
    </p>

  <style>
        .why-join { line-height: 1.25; }
        .why-join ul { margin-bottom: 0; }
        .why-join .badge { margin-bottom: .25rem; }
        @media (max-width: 575.98px) {
            .why-join { padding: 0.75rem; }
        }
    @media (max-width: 575.98px) {
      .form-label {
        font-size: 0.95rem;
      }
      .form-control, .input-group-text {
        font-size: 0.95rem;
        padding: 0.5rem 0.75rem;
      }
      .form-text {
        font-size: 0.8rem;
      }
      .btn {
        border-radius: 0.375rem;
      }
      h4 {
        font-size: 1.25rem;
      }
    }
  </style>
</div>
