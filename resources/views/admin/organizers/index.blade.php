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
        <i class="bi bi-people me-2"></i> Manage Organizers
    </h5>
</div>

<!-- Filter Buttons -->
<div class="content-box mb-4">
    <div class="d-flex gap-2">
        <a href="/admin/organizers"
           class="btn btn-sm {{ !$filter ? 'btn-primary' : 'btn-outline-primary' }}">
           <i class="bi bi-grid me-1"></i> All
        </a>

        <a href="/admin/organizers?status=pending"
           class="btn btn-sm {{ $filter=='pending' ? 'btn-primary' : 'btn-outline-primary' }}">
           <i class="bi bi-clock me-1"></i> Requested <span class="badge bg-danger">{{ $pendingCount }}</span>
        </a>

        <a href="/admin/organizers?status=approved"
           class="btn btn-sm {{ $filter=='approved' ? 'btn-primary' : 'btn-outline-primary' }}">
           <i class="bi bi-check-circle me-1"></i> Approved
        </a>
    </div>
</div>

<!-- Organizers Table -->
<div class="content-box">
    <table class="table align-middle table-hover mb-0">
        <thead class="text-muted">
            <tr>
                <th>No.</th>
                <th>Organizer Name</th>
                <th>Request Date</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @forelse($organizers as $i => $o)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    <i class="bi bi-building me-2 text-primary"></i>
                    {{ $o->club_name }}
                </td>
                <td>
                    {{ $o->created_at ? $o->created_at->format('Y-m-d') : '-' }}
                </td>
                <td>
                    @if($o->status == 'pending')
                        <span class="badge bg-warning">Pending</span>
                    @elseif($o->status == 'approved')
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-danger">Rejected</span>
                    @endif
                </td>
                <td>
                    <a href="/admin/organizers/{{ $o->o_id }}"
                       class="btn btn-sm btn-info text-white">
                       <i class="bi bi-eye me-1"></i> View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="bi bi-info-circle me-1"></i>
                    No organizers found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
