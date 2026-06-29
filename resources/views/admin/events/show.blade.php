@extends('admin.layout')

@section('content')

<style>
    .detail-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .detail-card .card-header {
        background: linear-gradient(135deg, #1e2a4a, #243b6b);
        padding: 18px 24px;
    }
    .info-label {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #6c757d;
        margin-bottom: 4px;
    }
    .info-value {
        font-size: 1rem;
        color: #212529;
        padding-bottom: 10px;
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 0;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 600;
    }
    .reject-reason-box {
        background: #fff5f5;
        border-left: 4px solid #dc3545;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 0.9rem;
        color: #721c24;
    }
</style>

<div class="container-fluid py-3">
    {{-- BREADCRUMB --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/admin/events" class="text-decoration-none">
                    <i class="bi bi-calendar-event me-1"></i>List Proposal
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($event->title, 40) }}</li>
        </ol>
    </nav>

    @php
        $statusLabel = ucfirst($event->status);
        if ($event->status == 'approved') {
            $statusClass = 'bg-success text-white';
            $statusIcon  = 'bi-check-circle-fill';
        } elseif ($event->status == 'rejected') {
            $statusClass = 'bg-danger text-white';
            $statusIcon  = 'bi-x-circle-fill';
        } else {
            $statusClass = 'bg-warning text-dark';
            $statusIcon  = 'bi-clock-fill';
            $statusLabel = 'Pending Review';
        }
    @endphp

    {{-- EVENT DETAIL CARD --}}
    <div class="card detail-card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0 text-white">
                <i class="bi bi-file-earmark-text me-2"></i> Event Proposal Details
            </h5>
            <span class="status-pill {{ $statusClass }}">
                <i class="bi {{ $statusIcon }}"></i> {{ $statusLabel }}
            </span>
        </div>

        <div class="card-body p-4">
            @if($event->event_banner)
                <div class="mb-4 text-center">
                    <img src="{{ asset('storage/'.$event->event_banner) }}" alt="Event Banner" class="img-fluid rounded-4 shadow-sm" style="max-height: 350px; width: 100%; object-fit: cover;">
                </div>
            @endif

            <div class="row g-4">

                {{-- LEFT COLUMN --}}
                <div class="col-md-8">

                    <div class="mb-4">
                        <p class="info-label"><i class="bi bi-tag me-1"></i> Event Name</p>
                        <p class="info-value">{{ $event->title }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="info-label"><i class="bi bi-file-text me-1"></i> Brief Description</p>
                        <p class="info-value">{{ $event->description ?: '—' }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="info-label"><i class="bi bi-card-text me-1"></i> Event Details (Tentative / Info)</p>
                        <div class="p-3 bg-light rounded-3 border" style="white-space: pre-line; font-size: 0.95rem; line-height: 1.6; color: #333;">
                            {{ $event->event_details ?: 'No detailed information provided.' }}
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6 mb-2">
                            <p class="info-label"><i class="bi bi-tags me-1"></i> Category</p>
                            <p class="info-value">{{ $event->category ?: '—' }}</p>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <p class="info-label"><i class="bi bi-geo-alt me-1"></i> Location</p>
                            <p class="info-value">{{ $event->location_name ?: '—' }}</p>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <p class="info-label"><i class="bi bi-clock me-1"></i> Start Time</p>
                            <p class="info-value">{{ $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('l, F j, Y \a\t g:i A') : '—' }}</p>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <p class="info-label"><i class="bi bi-clock-history me-1"></i> End Time</p>
                            <p class="info-value">{{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('l, F j, Y \a\t g:i A') : '—' }}</p>
                        </div>
                    </div>

                    @if($event->telegram_link || $event->whatsapp_link)
                    <div class="mt-2">
                        <p class="info-label"><i class="bi bi-share me-1"></i> Social Links</p>
                        <div class="d-flex gap-2 flex-wrap">
                            @if($event->telegram_link)
                                <a href="{{ $event->telegram_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-telegram me-1"></i> Telegram
                                </a>
                            @endif
                            @if($event->whatsapp_link)
                                <a href="{{ $event->whatsapp_link }}" target="_blank" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-whatsapp me-1"></i> WhatsApp
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                </div>

                {{-- RIGHT COLUMN --}}
                <div class="col-md-4">

                    {{-- Merit Point --}}
                    @if($event->status == 'approved')
                    <div class="mb-4 p-3 rounded-3" style="background:#f0fdf4">
                        <p class="info-label mb-2"><i class="bi bi-star-fill text-warning me-1"></i> Merit Points</p>
                        @if($event->merit_value)
                            <span class="fs-3 fw-bold text-success">{{ $event->merit_value }}</span>
                            <span class="text-muted ms-1">pts</span>
                        @else
                            <span class="text-muted small">Not assigned yet</span>
                        @endif
                    </div>
                    @endif

                    {{-- Proposal PDF --}}
                    <div class="mb-4">
                        <p class="info-label"><i class="bi bi-file-earmark-pdf text-danger me-1"></i> Proposal PDF</p>
                        @if($event->proposal_path)
                            <a href="{{ asset('storage/'.$event->proposal_path) }}"
                               target="_blank"
                               class="btn btn-outline-success btn-sm">
                                <i class="bi bi-eye me-1"></i> View PDF
                            </a>
                        @else
                            <span class="text-muted small"><i class="bi bi-dash-circle me-1"></i> No file uploaded</span>
                        @endif
                    </div>

                    {{-- QR Code (only if approved) --}}
                    @if($event->status == 'approved' && $event->qr_code_token)
                    <div class="mb-4">
                        <p class="info-label"><i class="bi bi-qr-code me-1"></i> QR Code</p>
                        <a href="/admin/events/{{ $event->e_id }}/qr"
                           class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-download me-1"></i> Download QR Code
                        </a>
                    </div>
                    @endif

                    {{-- Submitted Date --}}
                    <div class="mb-2">
                        <p class="info-label"><i class="bi bi-calendar-plus me-1"></i> Submitted</p>
                        <p class="info-value">{{ \Carbon\Carbon::parse($event->created_at)->format('d M Y, g:i A') }}</p>
                    </div>

                </div>
            </div>

            {{-- Actions section --}}
            @if($event->status == 'pending')
            <div class="mt-4 p-3 bg-light border rounded-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-1 fw-bold text-dark"><i class="bi bi-shield-check me-1"></i> Actions Required</h6>
                    <p class="text-muted small mb-0">Evaluate this event proposal to approve or reject.</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="bi bi-check-circle me-1"></i> Approve
                    </button>
                    <form action="/admin/events/{{ $event->e_id }}/reject" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle me-1"></i> Reject
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </div>

        <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3 px-4">
            <a href="/admin/events" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Events
            </a>
            <a href="/admin/events/{{ $event->e_id }}/edit" class="btn btn-warning fw-semibold">
                <i class="bi bi-pencil-square me-1"></i> Emergency Edit
            </a>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveModalLabel">
                    <i class="bi bi-check-circle me-1"></i> Approve Event
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/admin/events/{{ $event->e_id }}/approve" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="merit_value" class="form-label fw-bold">
                            <i class="bi bi-star text-warning"></i> Merit Points
                        </label>
                        <input type="number" class="form-control" id="merit_value" name="merit_value"
                               min="1" max="100" required placeholder="Enter merit points (1-100)">
                        <div class="form-text">Points awarded to students who attend this event.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Approve Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ATTENDANCE SECTION --}}
<div class="card detail-card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1a6b3c, #28a745);">
        <h5 class="mb-0 text-white">
            <i class="bi bi-people-fill me-2"></i> Student Attendance
        </h5>
        <span style="background:rgba(255,255,255,0.2);color:#fff;border-radius:50px;padding:3px 12px;font-size:0.85rem;">
            {{ $attendances->count() }} student{{ $attendances->count() == 1 ? '' : 's' }} scanned
        </span>
    </div>
    <div class="card-body p-0">
        @if($attendances->count() > 0)
        <div class="table-responsive">
            <table class="table mb-0">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">#</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">Student</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">Matric No.</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">Scan Time</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">Distance</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($attendances as $i => $att)
                    <tr style="border-bottom:1px solid #f5f5f5;">
                        <td class="text-muted small ps-3">{{ $i + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#1e2a4a,#3f5bb5);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem;">
                                    {{ strtoupper(substr($att->student->name ?? 'S', 0, 1)) }}
                                </span>
                                <div>
                                    <div class="fw-semibold" style="font-size:0.9rem;">{{ $att->student->name ?? 'Unknown' }}</div>
                                    <div class="text-muted" style="font-size:0.78rem;">{{ $att->student->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $att->student->num_matrics ?? '—' }}</span></td>
                        <td>
                            <span class="small">{{ \Carbon\Carbon::parse($att->scan_time)->format('d M Y') }}</span><br>
                            <span class="text-muted" style="font-size:0.78rem;">{{ \Carbon\Carbon::parse($att->scan_time)->format('g:i A') }}</span>
                        </td>
                        <td>
                            @if($att->distance !== null)
                                <span class="small">{{ number_format($att->distance, 1) }} m</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-check-circle-fill me-1"></i>{{ ucfirst($att->status ?? 'success') }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 bg-light border-top">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Total <strong>{{ $attendances->count() }}</strong> student(s) scanned for this event.
            </small>
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-person-x fs-1 d-block mb-2"></i>
            <h6>No students have scanned yet</h6>
            <p class="small mb-0">Students who scan the QR code during the event will appear here.</p>
        </div>
        @endif
    </div>
</div>

@endsection
