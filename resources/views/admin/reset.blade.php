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
        <i class="bi bi-arrow-clockwise me-2"></i> System Reset - New Semester
    </h5>
</div>

<div class="d-flex justify-content-center">
    <div class="content-box" style="max-width:520px; width:100%;">
        <form action="{{ url('/admin/reset') }}" method="POST">
            @csrf
            {{-- Academic Session --}}
            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="bi bi-calendar me-1"></i> Enter Current Academic Session
                </label>
                <input type="text" class="form-control" name="semester_name" placeholder="e.g. 2024/2025 Semester 1" required>
                <div class="form-text">The current merit points will be saved under this semester name before resetting.</div>
            </div>

            {{-- Button --}}
            <button type="submit" class="btn btn-danger w-100">
                <i class="bi bi-exclamation-triangle me-1"></i> RESET ALL MERIT POINTS
            </button>

            {{-- Warning --}}
            <div class="alert alert-danger mt-3 mb-0">
                <i class="bi bi-exclamation-triangle me-1"></i>
                <strong>Attention:</strong>
                This action will permanently reset all student merit
                points to 0 for the new semester and save their current merit to the database.
            </div>
        </form>
    </div>
</div>

@if($lastArchivedSemester)
<div class="d-flex justify-content-center mt-4">
    <div class="content-box border-warning-subtle" style="max-width:520px; width:100%; border: 1px solid #ffe69c; background-color: #fffdf5; border-radius: 12px; padding: 20px;">
        <h6 class="fw-bold text-warning-emphasis mb-2">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Undo Last Reset
        </h6>
        <p class="text-muted mb-3" style="font-size: 0.88rem;">
            You can restore the merit points, active statuses, and attendance logs of the previous semester (<strong>{{ $lastArchivedSemester }}</strong>). This will merge them back as the current active semester.
        </p>
        <form action="{{ url('/admin/reset/undo') }}" method="POST" onsubmit="return confirm('Are you sure you want to undo the last reset? This will restore all student merits and overwrite any new activities in the current semester.')">
            @csrf
            <button type="submit" class="btn btn-warning w-100 fw-semibold text-dark">
                <i class="bi bi-arrow-counterclockwise me-1"></i> UNDO RESET FOR "{{ $lastArchivedSemester }}"
            </button>
        </form>
    </div>
</div>
@endif

@endsection
