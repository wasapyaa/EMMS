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
    <h5 class="fw-bold mb-0">Hostel Eligibility</h5>
</div>

<div class="content-box mb-4">
    <h6 class="fw-bold mb-3">Set the number of students eligible for hostel next semester</h6>
    <form action="/admin/hostel-eligibility" method="POST">
        @csrf
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Number of students eligible for hostel next semester</label>
                <input type="number" name="eligible_students" class="form-control" min="1" value="{{ $eligibleStudents }}" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Save</button>
            </div>
            <div class="col-md-4">
                <p class="mb-0 text-muted">This value is used to determine the number of students eligible for hostel placement on the student dashboard.</p>
            </div>
        </div>
    </form>
</div>

@endsection
