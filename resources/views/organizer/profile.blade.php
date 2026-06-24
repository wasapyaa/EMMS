@extends('organizer.layout')

@section('content')
<div class="container mt-4">
    <div class="row">

        <!-- LEFT PROFILE CARD -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">

                    <!-- Avatar -->
                    <div class="rounded-circle bg-primary text-white d-flex
                                justify-content-center align-items-center mx-auto"
                         style="width:80px;height:80px;font-size:32px;">
                        {{ strtoupper(substr($organizer->club_name, 0, 1)) }}
                    </div>

                    <h5 class="mt-3">{{ $organizer->club_name }}</h5>
                    <p class="text-muted mb-1">PIC: {{ $organizer->pic_name }}</p>
                    <p class="text-muted mb-1">{{ $organizer->email }}</p>
                    <p class="text-muted">{{ $organizer->phone }}</p>

                    <span class="badge
                        {{ $organizer->status === 'approved' ? 'bg-success' : 'bg-warning' }}">
                        {{ ucfirst($organizer->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- RIGHT CONTENT -->
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-body">

                    <h4 class="mb-4">Update Organizer Profile</h4>

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Error Message --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- UPDATE PROFILE -->
                    <form method="POST" action="{{ url('/organizer/profile/update') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Club Name</label>
                            <input type="text"
                                   name="club_name"
                                   class="form-control"
                                   value="{{ old('club_name', $organizer->club_name) }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Person In Charge (PIC)</label>
                            <input type="text"
                                   name="pic_name"
                                   class="form-control"
                                   value="{{ old('pic_name', $organizer->pic_name) }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="{{ old('email', $organizer->email) }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text"
                                   name="phone"
                                   class="form-control"
                                   value="{{ old('phone', $organizer->phone) }}"
                                   required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update Profile
                        </button>
                    </form>

                    <hr class="my-4">

                    <!-- UPDATE PASSWORD -->
                    <form method="POST" action="{{ url('/organizer/profile/password') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   placeholder="Enter new password"
                                   required>
                        </div>

                        <button type="submit" class="btn btn-secondary">
                            Update Password
                        </button>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
