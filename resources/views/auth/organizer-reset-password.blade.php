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
            <h4 class="text-center fw-bold">Reset Password</h4>
            <p class="text-center text-muted mb-4">
                Please enter the OTP code sent to your email and set your new password.
            </p>

            @if (session('error'))
                <div class="alert alert-danger text-center py-2 mb-3">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger py-2 mb-3">
                    <ul class="mb-0 small ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/organizer/reset-password">
                @csrf

                {{-- EMAIL (READ-ONLY) --}}
                <div class="input-group mb-3">
                    <span class="input-group-text bg-light text-muted">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" name="email" class="form-control bg-light" value="{{ old('email', $email) }}" readonly required>
                </div>

                {{-- OTP CODE --}}
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-shield-lock"></i>
                    </span>
                    <input type="text" name="otp_code" class="form-control" placeholder="6-Digit OTP Code" maxlength="6" pattern="\d{6}" required autofocus>
                </div>

                {{-- NEW PASSWORD --}}
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password" class="form-control" placeholder="New Password" required>
                </div>

                {{-- CONFIRM NEW PASSWORD --}}
                <div class="input-group mb-4">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-lock-fill"></i>
                    </span>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm New Password" required>
                </div>

                {{-- SUBMIT BUTTON --}}
                <button type="submit" class="btn btn-primary w-100 fw-semibold mb-3">
                    Reset Password
                </button>

                {{-- BACK TO LOGIN --}}
                <div class="text-center">
                    <a href="/login" class="small text-decoration-none fw-semibold">
                        Cancel and Back to Login
                    </a>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
