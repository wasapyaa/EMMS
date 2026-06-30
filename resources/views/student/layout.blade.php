<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="icon" type="image/png" href="{{ asset('images/emms-logo.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7fb;
        }

        .sidebar {
            width: 230px;
            min-height: 100vh;
            background: linear-gradient(180deg, #1e2a4a, #243b6b);
            color: #fff;
            position: fixed;

            /* IMPORTANT */
            display: flex;
            flex-direction: column;
        }

        .sidebar a {
            color: #dbe3ff;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .sidebar a:hover {
            background: rgba(255,255,255,0.15);
        }

        .main-content {
            margin-left: 250px;
            padding: 25px;
        }

        .topbar {
            background: #fff;
            border-radius: 12px;
            padding: 15px 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,.05);
        }

        .stat-card {
            border-radius: 15px;
            color: #fff;
            padding: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,.08);
        }

        .bg-merit { background: #3f51b5; }
        .bg-rank  { background: #6fb1e8; }
        .bg-event { background: #bfc7cf; color:#000; }

        .content-box {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,.05);
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar p-3">

    <h5 class="mb-4">Student</h5>

    <!-- MENU (akan push logout ke bawah) -->
    <div class="flex-grow-1">
        <a href="/student/dashboard">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        <a href="/student/events">
            <i class="bi bi-calendar-event me-2"></i> Event List
        </a>
        <a href="/student/participation">
            <i class="bi bi-calendar-check me-2"></i> Event Participation
        </a>
        <a href="/student/ranking">
            <i class="bi bi-trophy me-2"></i> Ranking & Merit
        </a>
        <a href="/student/profile">
            <i class="bi bi-person me-2"></i> Profile
        </a>
    </div>

    <!-- LOGOUT (BOTTOM FIXED) -->
    <form action="{{ route('student.logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-light w-100">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
        </button>
    </form>

</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- TOP BAR -->
    <div class="topbar d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">Student Portal</h5>
        <div class="d-flex align-items-center">
            @if(auth('student')->check())
                @if(auth('student')->user()->current_semester_active)
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill me-3 px-2 py-1 small fw-bold">
                        <i class="bi bi-check-circle-fill me-1"></i> Active
                    </span>
                @else
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill me-2 px-2 py-1 small fw-bold">
                        <i class="bi bi-exclamation-circle-fill me-1"></i> Inactive
                    </span>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill me-3 px-2 py-0" data-bs-toggle="modal" data-bs-target="#joinSemesterModal" style="font-size: 0.75rem; line-height: 1.5;">
                        Join Semester
                    </button>
                @endif
            @endif
            <div>
                <i class="bi bi-person-circle me-2"></i>
                {{ auth('student')->user()->name ?? 'Student' }}
            </div>
        </div>
    </div>

    @yield('content')

</div>

<!-- JOIN SEMESTER MODAL -->
<div class="modal fade" id="joinSemesterModal" tabindex="-1" aria-labelledby="joinSemesterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="joinSemesterModalLabel">Join Current Semester</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ url('/student/join-semester') }}">
                @csrf
                <div class="modal-body py-4">
                    <p class="text-muted small mb-4">
                        Please enter the active Semester Join Code to activate your account for the current semester and unlock full access.
                    </p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Semester Join Code</label>
                        <input type="text" name="semester_code" class="form-control form-control-lg text-center fw-bold" placeholder="e.g. SEM-XXXXXX" required style="letter-spacing: 1px;">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Activate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
