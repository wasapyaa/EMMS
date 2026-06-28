@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">
        <i class="bi bi-building text-primary me-2"></i> Organizer Detail
    </h5>
    <a href="/admin/organizers" class="btn btn-light border shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to List
    </a>
</div>

<div class="content-box">
    <div class="d-flex align-items-center mb-4 border-bottom pb-3">
        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle me-3">
            <i class="bi bi-people fs-3"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1">{{ $organizer->club_name }}</h5>
            <div class="text-muted">
                <i class="bi bi-person me-1"></i> PIC: {{ $organizer->pic_name }}
            </div>
        </div>
        <div class="ms-auto text-end">
            <div class="text-muted small text-uppercase fw-bold mb-1">Status</div>
            @if($organizer->status == 'approved')
                <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm">
                    <i class="bi bi-check-circle me-1"></i> Approved
                </span>
            @elseif($organizer->status == 'rejected')
                <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">
                    <i class="bi bi-x-circle me-1"></i> Rejected
                </span>
            @else
                <span class="badge bg-warning text-dark rounded-pill px-3 py-2 shadow-sm">
                    <i class="bi bi-hourglass-split me-1"></i> Pending
                </span>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="text-muted small fw-bold text-uppercase">Club / Organization Name</label>
            <p class="fs-6 fw-semibold text-dark">{{ $organizer->club_name }}</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="text-muted small fw-bold text-uppercase">Person in Charge (PIC)</label>
            <p class="fs-6 fw-semibold text-dark">{{ $organizer->pic_name }}</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="text-muted small fw-bold text-uppercase">Email Address</label>
            <p class="fs-6 fw-semibold text-dark">
                <a href="mailto:{{ $organizer->email }}" class="text-decoration-none">
                    {{ $organizer->email }}
                </a>
            </p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="text-muted small fw-bold text-uppercase">Phone Number</label>
            <p class="fs-6 fw-semibold text-dark">
                <a href="tel:{{ $organizer->phone }}" class="text-decoration-none text-dark">
                    {{ $organizer->phone }}
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
