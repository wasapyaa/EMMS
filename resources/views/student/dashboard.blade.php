@extends('student.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Student Dashboard</h5>
    <form method="GET" class="d-flex">
        <select name="semester" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="current" {{ $selectedSemester == 'current' ? 'selected' : '' }}>Current Semester</option>
            @foreach($semesters as $sem)
                <option value="{{ $sem }}" {{ $selectedSemester == $sem ? 'selected' : '' }}>{{ $sem }}</option>
            @endforeach
        </select>
    </form>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($selectedSemester == 'current' && !$student->current_semester_active)
<div class="d-flex justify-content-center mt-5">
    <div class="card shadow border-0 rounded-4 p-4" style="max-width: 500px; width: 100%;">
        <div class="text-center mb-4">
            <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                <i class="bi bi-shield-lock-fill fs-2"></i>
            </div>
            <h4 class="fw-bold">Join Current Semester</h4>
            <p class="text-muted small">
                To participate in events, view current merit rankings, and qualify for hostel eligibility, please enter the active Semester Join Code.
            </p>
        </div>

        <form method="POST" action="{{ url('/student/join-semester') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Semester Join Code</label>
                <input type="text" name="semester_code" class="form-control form-control-lg text-center fw-bold" placeholder="e.g. SEM-XXXXXX" required style="letter-spacing: 1px;">
                <div class="form-text text-center small mt-2">
                    <i class="bi bi-info-circle me-1"></i> Obtain the code from Admin HEP or your organizer.
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3">
                <i class="bi bi-check-circle me-1"></i> Activate Semester
            </button>
        </form>
        @if(count($semesters) > 0)
            <div class="text-center mt-3">
                <a href="{{ url('/student/dashboard?semester=' . $semesters[0]) }}" class="text-decoration-none small fw-bold text-primary">
                    <i class="bi bi-clock-history me-1"></i> View Past Semester History
                </a>
            </div>
        @endif
    </div>
</div>
@else
<!-- Stat Cards -->
<div class="row g-4 mb-4">

    <div class="col-md-4">
        <div class="stat-card bg-merit">
            <h6>Total Merit Points</h6>
            <h2 class="fw-bold">{{ $totalMerit }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card bg-rank">
            <h6>Current Ranking</h6>
            <h2 class="fw-bold">#{{ $ranking }}/{{ $totalStudents }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card {{ $ranking !== '-' && $ranking <= $eligibleStudents ? 'bg-rank' : 'bg-danger' }}">
            <h6>Hostel Quota Ranking</h6>
            <h2 class="fw-bold">#{{ $ranking }}/{{ $eligibleStudents }}</h2>
            <p class="mb-0 mt-2 small">
                @if($ranking !== '-' && $ranking <= $eligibleStudents)
                    You are eligible to receive hostel accommodation for the next semester.
                @else
                    You will not be eligible for hostel accommodation next semester.
                @endif
            </p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card bg-event">
            <h6>Events Attended</h6>
            <h2 class="fw-bold">{{ $eventsAttended }}</h2>
        </div>
    </div>

</div>

<!-- Recent Events -->
<div class="content-box">
    <h6 class="fw-bold mb-3">
        <i class="bi bi-clock-history me-1"></i> Recent Events
    </h6>

    <table class="table align-middle table-hover">
        <thead class="text-muted">
            <tr>
                <th>Event Name</th>
                <th>Date</th>
                <th>Merit</th>
                <th>Points Earned</th>
            </tr>
        </thead>

        <tbody>
            @forelse($participations as $p)
            <tr>
                <td>
                    <i class="bi bi-circle-fill text-success me-2" style="font-size:8px"></i>
                    {{ $p->event_name }}
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($p->event_date)->format('Y-m-d') }}
                </td>
                <td>
                    {{ $p->merit_value }}
                </td>
                <td>
                    <span class="badge bg-primary">
                        {{ $p->merit_value }} Points
                    </span>
                </td>
            </tr>

            @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    <i class="bi bi-info-circle me-1"></i>
                    No recent events found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

@endsection
