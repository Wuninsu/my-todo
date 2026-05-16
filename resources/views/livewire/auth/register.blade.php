<div>

    {{-- HEADER --}}
    <div class="text-center mb-4">
        <h2 class="fw-bold mb-2">
            Create Account
        </h2>

        <p class="text-muted mb-0">
            Start organizing your tasks smarter.
        </p>

    </div>

    {{-- FORM --}}
    <form wire:submit="register">

        {{-- NAME --}}
        <div class="mb-4">

            <label class="form-label fw-semibold">

                Full Name

            </label>

            <div class="position-relative">

                <i class="bi bi-person app-input-icon"></i>

                <input
                    type="text"
                    wire:model="name"
                    class="form-control app-input ps-5 @error('name') is-invalid @enderror"
                    placeholder="Enter your full name">

            </div>

            @error('name')

                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>

            @enderror

        </div>

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

            <label class="form-label fw-semibold">

                Password

            </label>

            <div class="position-relative">

                <i class="bi bi-lock app-input-icon"></i>

                <input
                    type="password"
                    wire:model="password"
                    class="form-control app-input ps-5 @error('password') is-invalid @enderror"
                    placeholder="Create password">

            </div>

            @error('password')

                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>

            @enderror

        </div>

        {{-- CONFIRM PASSWORD --}}
        <div class="mb-4">

            <label class="form-label fw-semibold">

                Confirm Password

            </label>

            <div class="position-relative">

                <i class="bi bi-shield-lock app-input-icon"></i>

                <input
                    type="password"
                    wire:model="password_confirmation"
                    class="form-control app-input ps-5"
                    placeholder="Confirm password">

            </div>

        </div>

        {{-- TERMS --}}
        <div class="mb-4">

            <div class="form-check">

                <input
                    type="checkbox"
                    class="form-check-input"
                    id="terms">

                <label
                    class="form-check-label"
                    for="terms">

                    I agree to the
                    <a href="#"
                       class="text-decoration-none">

                        Terms

                    </a>

                    and

                    <a href="#"
                       class="text-decoration-none">

                        Privacy Policy

                    </a>

                </label>

            </div>

        </div>

        {{-- SUBMIT --}}
        <button
            type="submit"
            class="btn app-btn-primary w-100 rounded-4">

            <span wire:loading.remove>

                <i class="bi bi-person-plus"></i>

                Create Account

            </span>

            <span wire:loading>

                <span class="spinner-border spinner-border-sm me-2"></span>

                Creating account...

            </span>

        </button>

    </form>

    {{-- FOOTER --}}
    <div class="text-center mt-4">
        <p class="text-muted mb-0">
            Already have an account?
            <a href="{{ route('login') }}"class="fw-semibold text-decoration-none">
                Login
            </a>
        </p>

    </div>

</div>