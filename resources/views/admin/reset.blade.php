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
                    <i class="bi bi-calendar me-1"></i> Enter New Academic Session
                </label>
                <input type="text" class="form-control" name="semester_name" placeholder="e.g. 2024/2025 Semester 1" required>
                <div class="form-text">The current merit points will be saved under this semester name before resetting.</div>
            </div>

            {{-- Button --}}
            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to reset all merit points? This action cannot be undone.')">
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

@endsection
