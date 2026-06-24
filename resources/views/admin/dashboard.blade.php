@extends('admin.layout')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-warning alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-4">
        <i class="bi bi-speedometer2 me-2"></i> Admin Dashboard
    </h5>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card bg-merit">
            <h6>Total Students</h6>
            <h2 class="fw-bold">{{ $totalStudents }}</h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card bg-rank">
            <h6>Total Organizers</h6>
            <h2 class="fw-bold">{{ $totalOrganizers }}</h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card bg-event">
            <h6>Total Events</h6>
            <h2 class="fw-bold">{{ $totalEvents }}</h2>
        </div>
    </div>
</div>

<div class="content-box mb-4">
    <h6 class="fw-bold mb-3">Hostel & Semester Settings</h6>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="stat-card bg-event h-100">
                <h6>Current Hostel Quota</h6>
                <h2 class="fw-bold">{{ $eligibleStudents }}</h2>
                <p class="mb-0 mt-2 small">
                    Anda boleh mengemaskini nilai ini di halaman <a href="/admin/hostel">Hostel Eligibility</a>.
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card bg-merit h-100" style="background: linear-gradient(135deg, #4f46e5, #3b82f6);">
                <h6>Active Semester Join Code</h6>
                <h2 class="fw-bold text-white">{{ $currentSemesterCode }}</h2>
                <p class="mb-0 mt-2 small text-white-50">
                    Kongsi kod ini kepada pelajar aktif supaya mereka dapat menyertai semester semasa.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
