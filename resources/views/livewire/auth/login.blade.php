<div>

    {{-- HEADER --}}
    <div class="text-center mb-4">

        <h2 class="fw-bold mb-2">
            Welcome Back
        </h2>

        <p class="text-muted mb-0">
            Login to continue managing your tasks.
        </p>

    </div>

    {{-- FORM --}}
    <form wire:submit="login">

        {{-- EMAIL --}}
        <div class="mb-4">

            <label class="form-label fw-semibold">

                Email Address

            </label>

            <div class="position-relative">

                <i class="bi bi-envelope app-input-icon"></i>

                <input
                    type="email"
                    wire:model="email"
                    class="form-control app-input ps-5 @error('email') is-invalid @enderror"
                    placeholder="Enter your email">

            </div>

            @error('email')

                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>

            @enderror

        </div>

        {{-- PASSWORD --}}
        <div class="mb-4">

            <div class="d-flex align-items-center justify-content-between mb-2">

                <label class="form-label fw-semibold mb-0">

                    Password

                </label>

                <a href="#"
                   class="small text-decoration-none">

                    Forgot Password?

                </a>

            </div>

            <div class="position-relative">

                <i class="bi bi-lock app-input-icon"></i>

                <input
                    type="password"
                    wire:model="password"
                    class="form-control app-input ps-5 @error('password') is-invalid @enderror"
                    placeholder="Enter your password">

            </div>

            @error('password')

                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>

            @enderror

        </div>

        {{-- REMEMBER --}}
        <div class="mb-4">

            <div class="form-check">

                <input type="checkbox" wire:model="remember" class="form-check-input" id="remember">
                <label
                    class="form-check-label"
                    for="remember">

                    Remember me

                </label>

            </div>

        </div>

        {{-- SUBMIT --}}
        <button type="submit" class="btn app-btn-primary w-100 rounded-4">

            <span wire:loading.remove>
                <i class="bi bi-box-arrow-in-right"></i>
                Login
            </span>

            <span wire:loading>
                <span class="spinner-border spinner-border-sm me-2"></span>
                Signing in...
            </span>

        </button>

    </form>

    {{-- FOOTER --}}
    <div class="text-center mt-4">
        <p class="text-muted mb-0">
            Don’t have an account?
            <a href="{{route('register')}}" class="fw-semibold text-decoration-none">
                Create account
            </a>

        </p>

    </div>

</div>