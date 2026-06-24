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
            <h4 class="text-center fw-bold">Forgot Password</h4>
            <p class="text-center text-muted mb-4">
                Enter your organizer email to receive a password reset OTP code.
            </p>

            @if (session('status'))
                <div class="alert alert-success text-center py-2 mb-3">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger text-center py-2 mb-3">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="/organizer/forgot-password">
                @csrf

                {{-- EMAIL --}}
                <div class="input-group mb-4">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                </div>

                {{-- SUBMIT BUTTON --}}
                <button type="submit" class="btn btn-primary w-100 fw-semibold mb-3">
                    Send OTP Code
                </button>

                {{-- BACK TO LOGIN --}}
                <div class="text-center">
                    <a href="/login" class="small text-decoration-none fw-semibold">
                        <i class="bi bi-arrow-left"></i> Back to Login
                    </a>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
