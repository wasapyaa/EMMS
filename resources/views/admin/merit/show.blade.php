@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">
        <i class="bi bi-person-lines-fill text-primary me-2"></i> Student Merit Detail
    </h5>
    <a href="/admin/merit?semester={{ $selectedSemester }}" class="btn btn-light border shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to List
    </a>
</div>

<div class="content-box mb-4">
    <div class="d-flex align-items-center mb-3">
        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle me-3">
            <i class="bi bi-person fs-3"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1">{{ $student->name }}</h5>
            <div class="text-muted">
                <i class="bi bi-card-heading me-1"></i> {{ $student->num_matrics }}
            </div>
        </div>
        <div class="ms-auto text-end">
            <div class="text-muted small text-uppercase fw-bold mb-1">Total Merit</div>
            <span class="badge rounded-pill bg-success fs-5 px-3 py-2 shadow-sm">
                {{ $logs->sum('points_added') }}
            </span>
        </div>
    </div>
</div>

<div class="content-box">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h6 class="fw-bold mb-0">Merit History Log</h6>
        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2.5 py-1.5 fw-semibold" style="font-size: 0.78rem;">
            {{ $selectedSemester === 'current' ? 'Current Semester' : $selectedSemester }}
        </span>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 5%">#</th>
                    <th>Event Name</th>
                    <th class="text-center">Merit Added</th>
                    <th class="text-end">Date / Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $i => $log)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td class="fw-semibold text-dark">{{ $log->event_title }}</td>
                    <td class="text-center">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                            +{{ $log->points_added }}
                        </span>
                    </td>
                    <td class="text-end text-muted small">
                        {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-3"></i>
                        No merit records found for this student.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
