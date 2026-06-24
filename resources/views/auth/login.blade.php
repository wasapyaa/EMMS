@extends('layouts.auth')

@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-light">

    <div class="card shadow border-0" style="width: 420px; border-radius: 16px;">
        <div class="card-body p-4">

            {{-- LOGO --}}
            <div class="text-center mb-3">
                <img src="{{ asset('images/UiTM-Logo.png') }}" alt="UiTM" height="50">
            </div>

            {{-- TITLE --}}
            <h4 class="text-center fw-bold">Welcome Back</h4>
            <p class="text-center text-muted mb-4">
                Please enter your credentials to access your portal
            </p>

            @if (session('success'))
                <div class="alert alert-success text-center py-2 mb-3 small">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger text-center py-2 mb-3 small">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="/login">
                @csrf

                {{-- ROLE SWITCH --}}
                <div class="btn-group w-100 mb-3" role="group">
                    <input type="radio" class="btn-check" name="role" id="organizer" value="organizer" checked>
                    <label class="btn btn-outline-primary" for="organizer">Organizer</label>

                    <input type="radio" class="btn-check" name="role" id="admin" value="admin">
                    <label class="btn btn-outline-primary" for="admin">Admin HEP</label>
                </div>

                {{-- EMAIL --}}
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                </div>

                {{-- PASSWORD --}}
                <div class="input-group mb-2">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>

                {{-- REMEMBER + FORGOT --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember">
                        <label class="form-check-label text-muted" for="remember">
                            Remember Me
                        </label>
                    </div>
                    <a href="#" id="forgotPasswordLink" class="small text-decoration-none">Forgot Password?</a>
                </div>

                {{-- LOGIN BUTTON --}}
                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                    Login
                </button>

                {{-- SIGN UP --}}
                <div class="text-center mt-3">
                    <small class="text-muted">
                        New Organizer?
                        <a href="#" id="signupLink" class="text-decoration-none fw-semibold">
                            Request account here
                        </a>
                    </small>
                </div>

                @error('login')
                    <div class="text-danger text-center mt-2">
                        {{ $message }}
                    </div>
                @enderror
            </form>

        </div>
    </div>

</div>

{{-- SIGNUP & FORGOT PASSWORD REDIRECT --}}
<script>
document.getElementById('signupLink').addEventListener('click', function (e) {
    e.preventDefault();

    if (document.getElementById('organizer').checked) {
        window.location.href = '/organizer/register';
    } 
    else {
        alert('Admin account is managed by the system administrator.');
    }
});

document.getElementById('forgotPasswordLink').addEventListener('click', function (e) {
    e.preventDefault();

    if (document.getElementById('organizer').checked) {
        window.location.href = '/organizer/forgot-password';
    } 
    else {
        alert('Admin password reset is managed by the system administrator.');
    }
});
</script>
@endsection
