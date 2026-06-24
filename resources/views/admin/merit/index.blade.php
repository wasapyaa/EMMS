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
    <h5 class="fw-bold mb-0">
        <i class="bi bi-star me-2"></i> Student Merit List
    </h5>
    
    <a href="/admin/merit/export?semester={{ request('semester', 'current') }}" class="btn btn-success">
        <i class="bi bi-file-earmark-excel me-2"></i> Export to Excel
    </a>
</div>

<!-- Search Form -->
<div class="content-box mb-4">
    <form method="GET" class="d-flex gap-2">
        <select name="semester" class="form-select" style="max-width: 250px;">
            <option value="current" {{ $selectedSemester == 'current' ? 'selected' : '' }}>Current Semester</option>
            @foreach($semesters as $sem)
                <option value="{{ $sem }}" {{ $selectedSemester == $sem ? 'selected' : '' }}>{{ $sem }}</option>
            @endforeach
        </select>
        <input type="text"
               name="search"
               value="{{ $search }}"
               class="form-control"
               placeholder="Search by name or matric number">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-search me-1"></i> Search
        </button>
    </form>
</div>

<!-- Merit Table -->
<div class="content-box">
    <table class="table align-middle table-hover mb-0">
        <thead class="text-muted">
            <tr>
                <th>No.</th>
                <th>Student Name</th>
                <th>Matric No</th>
                <th>Total Merit</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $i => $s)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    <i class="bi bi-person-circle me-2 text-primary"></i>
                    {{ $s->name }}
                </td>
                <td>
                    <code>{{ $s->num_matrics }}</code>
                </td>
                <td>
                    <span class="badge bg-primary fs-6">
                        {{ $s->total_merit }} Points
                    </span>
                </td>
                <td>
                    <a href="/admin/merit/{{ $s->s_id }}"
                       class="btn btn-sm btn-info">
                       <i class="bi bi-eye me-1"></i> View Student
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="bi bi-info-circle me-1"></i>
                    No students found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
