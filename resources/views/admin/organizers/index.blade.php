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
        <i class="bi bi-people me-2"></i> Manage Organizers
    </h5>
</div>

<!-- Filter Buttons -->
<div class="content-box mb-4">
    <div class="d-flex gap-2">
        <a href="/admin/organizers"
           class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 {{ !$filter ? 'btn-primary' : 'btn-light border text-muted' }}">
           <i class="bi bi-grid"></i> All
        </a>

        <a href="/admin/organizers?status=pending"
           class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 {{ $filter=='pending' ? 'btn-primary' : 'btn-light border text-muted' }}">
           <i class="bi bi-clock"></i> Requested 
           @if($pendingCount > 0)
               <span class="badge bg-danger ms-1" style="font-size: 0.7rem; padding: 0.25em 0.5em;">{{ $pendingCount }}</span>
           @endif
        </a>

        <a href="/admin/organizers?status=approved"
           class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 {{ $filter=='approved' ? 'btn-primary' : 'btn-light border text-muted' }}">
           <i class="bi bi-check-circle"></i> Approved
        </a>
    </div>
</div>

<!-- Organizers Table -->
<div class="content-box">
    <table class="table mb-0">
        <thead style="background:#f8f9fa;">
            <tr>
                <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">#</th>
                <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">Organizer Name</th>
                <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">Request Date</th>
                <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">Status</th>
                <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($organizers as $i => $o)
            <tr style="border-bottom:1px solid #f5f5f5;">
                <td class="text-muted small ps-3">{{ $i + 1 }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#6f42c1,#a951d8);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem;">
                            <i class="bi bi-building"></i>
                        </span>
                        <div>
                            <div class="fw-semibold" style="font-size:0.9rem;">{{ $o->club_name }}</div>
                            <div class="text-muted" style="font-size:0.78rem;">Organizer ID: #{{ $o->o_id }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="small">{{ $o->created_at ? $o->created_at->format('d M Y') : '—' }}</span><br>
                    <span class="text-muted" style="font-size:0.78rem;">{{ $o->created_at ? $o->created_at->format('g:i A') : '' }}</span>
                </td>
                <td>
                    @if($o->status == 'pending')
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5 fw-semibold" style="font-size: 0.78rem;">
                            <i class="bi bi-clock-fill me-1"></i>Pending
                        </span>
                    @elseif($o->status == 'approved')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 fw-semibold" style="font-size: 0.78rem;">
                            <i class="bi bi-check-circle-fill me-1"></i>Approved
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 fw-semibold" style="font-size: 0.78rem;">
                            <i class="bi bi-x-circle-fill me-1"></i>Rejected
                        </span>
                    @endif
                </td>
                <td>
                    <a href="/admin/organizers/{{ $o->o_id }}"
                       class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem; border-color: #3b82f6; color: #3b82f6;">
                       <i class="bi bi-eye"></i> View Profile
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-5">
                    <i class="bi bi-info-circle fs-4 mb-2 d-block text-muted"></i>
                    No organizers found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
