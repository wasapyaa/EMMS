<!DOCTYPE html>
<html>
<head>
    <title>Student Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card shadow-sm">
                <div class="card-body">

                    <h4 class="text-center mb-4">Student Sign Up</h4>

                    {{-- Error --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="/student/register">
                        @csrf

                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label>UiTM Email</label>
                            <div class="input-group">
                                <input type="email" id="email" name="email" class="form-control"
                                       placeholder="2021123456@student.uitm.edu.my"
                                       value="{{ old('email') }}" required>
                                <button class="btn btn-outline-secondary" type="button" id="btn-send-otp">Send OTP</button>
                            </div>
                            <small class="d-block mt-1 text-muted" id="otp-message"></small>
                        </div>

                        <div class="mb-3">
                            <label>OTP Code</label>
                            <input type="text" name="otp_code" class="form-control"
                                   placeholder="Enter 6-digit OTP" required>
                        </div>

                        <div class="mb-3">
                            <label>Mobile Number</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone') }}" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control" required>
                        </div>

                        <button class="btn btn-primary w-100">
                            Register
                        </button>
                    </form>

                    <script>
                    document.getElementById('btn-send-otp').addEventListener('click', function() {
                        const email = document.getElementById('email').value;
                        const name = document.querySelector('input[name="name"]').value;
                        const btn = this;
                        const msg = document.getElementById('otp-message');

                        if (!email) {
                            alert('Please enter your email address first.');
                            return;
                        }

                        btn.disabled = true;
                        btn.textContent = 'Sending...';
                        msg.textContent = '';
                        msg.className = 'd-block mt-1 text-muted';

                        fetch('{{ route("student.register.send-otp") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ email: email, name: name })
                        })
                        .then(response => response.json().then(data => ({ status: response.status, body: data })))
                        .then(res => {
                            if (res.status === 200 && res.body.status === 'success') {
                                msg.textContent = 'OTP sent successfully! Please check your email.';
                                msg.className = 'd-block mt-1 text-success';
                                
                                // Countdown 60 seconds
                                let seconds = 60;
                                const timer = setInterval(() => {
                                    seconds--;
                                    if (seconds <= 0) {
                                        clearInterval(timer);
                                        btn.disabled = false;
                                        btn.textContent = 'Send OTP';
                                    } else {
                                        btn.textContent = `Resend in ${seconds}s`;
                                    }
                                }, 1000);
                            } else {
                                btn.disabled = false;
                                btn.textContent = 'Send OTP';
                                msg.textContent = res.body.message || 'Failed to send OTP. Please check your email or try again.';
                                msg.className = 'd-block mt-1 text-danger';
                            }
                        })
                        .catch(err => {
                            btn.disabled = false;
                            btn.textContent = 'Send OTP';
                            msg.textContent = 'An error occurred. Please try again.';
                            msg.className = 'd-block mt-1 text-danger';
                        });
                    });
                    </script>

                    <p class="text-center mt-3">
                        Already have an account? <a href="/login">Login</a>
                    </p>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
