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
    <table class="table mb-0">
        <thead style="background:#f8f9fa;">
            <tr>
                <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">#</th>
                <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">Student</th>
                <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">Matric No.</th>
                <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">Total Merit</th>
                <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $i => $s)
            <tr style="border-bottom:1px solid #f5f5f5;">
                <td class="text-muted small ps-3">{{ $i + 1 }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#1e2a4a,#3f5bb5);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem;">
                            {{ strtoupper(substr($s->name ?? 'S', 0, 1)) }}
                        </span>
                        <div>
                            <div class="fw-semibold" style="font-size:0.9rem;">{{ $s->name ?? 'Unknown' }}</div>
                            <div class="text-muted" style="font-size:0.78rem;">{{ $s->email ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-light text-dark border">{{ $s->num_matrics ?? '—' }}</span>
                </td>
                <td>
                    @if($s->total_merit > 0)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 fw-semibold" style="font-size: 0.78rem;">
                            {{ $s->total_merit }} Points
                        </span>
                    @else
                        <span class="badge bg-light text-muted border px-2.5 py-1.5 fw-normal" style="font-size: 0.78rem;">
                            0 Points
                        </span>
                    @endif
                </td>
                <td>
                    <a href="/admin/merit/{{ $s->s_id }}"
                       class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem; border-color: #3b82f6; color: #3b82f6;">
                       <i class="bi bi-eye"></i> View Profile
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-5">
                    <i class="bi bi-info-circle fs-4 mb-2 d-block text-muted"></i>
                    No students found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
