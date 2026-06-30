@extends('organizer.layout')

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
    .status-badge {
        font-size: 0.9rem;
        padding: 6px 14px;
        border-radius: 50px;
    }
    .attendance-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .attendance-card .card-header {
        background: linear-gradient(135deg, #1a6b3c, #28a745);
        padding: 18px 24px;
    }
    .attendance-table thead th {
        background: #f8f9fa;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6c757d;
        border-bottom: 2px solid #e9ecef;
        padding: 12px 16px;
    }
    .attendance-table tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f5f5f5;
    }
    .attendance-table tbody tr:hover {
        background: #f8f9fa;
    }
    .student-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e2a4a, #3f5bb5);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .count-badge {
        background: rgba(255,255,255,0.2);
        color: #fff;
        border-radius: 50px;
        padding: 3px 12px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .event-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 600;
    }
    .dot-pulse {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        animation: pulse-anim 1.5s infinite;
    }
    @keyframes pulse-anim {
        0%, 100% { opacity: 0.4; transform: scale(0.9); }
        50% { opacity: 1; transform: scale(1.1); }
    }
    .empty-state {
        padding: 50px 20px;
        text-align: center;
        color: #adb5bd;
    }
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 12px;
        display: block;
    }
</style>

{{-- BREADCRUMB --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="/organizer/events/approved" class="text-decoration-none">
                <i class="bi bi-check-circle me-1"></i>Approved Events
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($event->title, 40) }}</li>
    </ol>
</nav>

@php
    $now = now();
    $start = \Carbon\Carbon::parse($event->start_time);
    $end   = \Carbon\Carbon::parse($event->end_time);
    if ($start->gt($now)) {
        $statusLabel = 'Upcoming';
        $statusClass = 'bg-primary';
        $dotClass    = '';
    } elseif ($end->lt($now)) {
        $statusLabel = 'Ended';
        $statusClass = 'bg-secondary';
        $dotClass    = '';
    } else {
        $statusLabel = 'Ongoing';
        $statusClass = 'bg-success';
        $dotClass    = 'dot-pulse';
    }
@endphp

{{-- ============================
     EVENT DETAIL CARD
     ============================ --}}
<div class="card detail-card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0 text-white">
            <i class="bi bi-calendar-event me-2"></i> Event Details
        </h5>
        <span class="event-status-pill {{ $statusClass }} text-white">
            @if($dotClass)
                <span class="dot-pulse bg-white {{ $dotClass }}"></span>
            @endif
            {{ $statusLabel }}
        </span>
    </div>

    <div class="card-body p-4">
        @if($event->event_banner)
            <div class="mb-4 text-center">
                <img src="{{ asset('storage/'.$event->event_banner) }}" alt="Event Banner" class="img-fluid rounded-4 shadow-sm" style="max-height: 350px; width: 100%; object-fit: cover;">
            </div>
        @endif

        <div class="row g-4">

            {{-- LEFT COLUMN: Info --}}
            <div class="col-md-8">

                <div class="mb-4">
                    <p class="info-label"><i class="bi bi-tag me-1"></i> Title</p>
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
                        <p class="info-value">{{ $start->format('l, F j, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <p class="info-label"><i class="bi bi-clock-history me-1"></i> End Time</p>
                        <p class="info-value">{{ $end->format('l, F j, Y \a\t g:i A') }}</p>
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

            {{-- RIGHT COLUMN: Status/Merit/PDF/QR --}}
            <div class="col-md-4">

                {{-- Merit --}}
                <div class="mb-4 p-3 rounded-3" style="background:#f8f9fa">
                    <p class="info-label mb-2"><i class="bi bi-star-fill text-warning me-1"></i> Merit Points</p>
                    <span class="fs-3 fw-bold text-primary">{{ $event->merit_value }}</span>
                    <span class="text-muted ms-1">pts</span>
                </div>

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

                {{-- QR Code --}}
                @if($event->qr_path)
                <div class="mb-4">
                    <p class="info-label"><i class="bi bi-qr-code me-1"></i> QR Code</p>
                    <div class="mb-2">
                        <img src="{{ url('/organizer/events/'.$event->e_id.'/download-qr') }}" alt="QR Code" class="img-fluid border rounded p-2 bg-white" style="max-width: 150px;">
                    </div>
                    <a href="{{ url('/organizer/events/'.$event->e_id.'/download-qr') }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-download me-1"></i> Download QR Code
                    </a>
                </div>
                @endif

                {{-- Radius --}}
                <div class="mb-2">
                    <p class="info-label"><i class="bi bi-broadcast-pin me-1"></i> Scan Radius</p>
                    <p class="info-value">{{ $event->radius_meter }} meter</p>
                </div>

            </div>

        </div>
    </div>

    <div class="card-footer bg-white text-end py-3 px-4">
        <a href="/organizer/events/approved" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Events
        </a>
    </div>
</div>

{{-- ============================
     ATTENDANCE LIST CARD
     ============================ --}}
<div class="card attendance-card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0 text-white">
            <i class="bi bi-people-fill me-2"></i> Student Attendance
        </h5>
        <span class="count-badge">
            {{ $attendances->count() }} student{{ $attendances->count() == 1 ? '' : 's' }} scanned
        </span>
    </div>

    <div class="card-body p-0">
        @if($attendances->count() > 0)
        <div class="table-responsive">
            <table class="table attendance-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Matric No.</th>
                        <th>Scan Time</th>
                        <th>Distance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($attendances as $i => $att)
                    <tr>
                        <td class="text-muted small">{{ $i + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="student-avatar">
                                    {{ strtoupper(substr($att->student->name ?? 'S', 0, 1)) }}
                                </span>
                                <div>
                                    <div class="fw-semibold" style="font-size:0.9rem">
                                        {{ $att->student->name ?? 'Unknown' }}
                                    </div>
                                    <div class="text-muted" style="font-size:0.78rem">
                                        {{ $att->student->email ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $att->student->num_matrics ?? '—' }}
                            </span>
                        </td>
                        <td>
                            <span class="small">
                                {{ \Carbon\Carbon::parse($att->scan_time)->format('d M Y') }}
                            </span>
                            <br>
                            <span class="text-muted" style="font-size:0.78rem">
                                {{ \Carbon\Carbon::parse($att->scan_time)->format('g:i A') }}
                            </span>
                        </td>
                        <td>
                            @if($att->distance !== null)
                                <span class="small">{{ number_format($att->distance, 1) }} m</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($att->status == 'present')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-check-circle-fill me-1"></i>Present
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                    <i class="bi bi-clock-fill me-1"></i>{{ ucfirst($att->status ?? 'N/A') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Summary footer --}}
        <div class="px-4 py-3 bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Total <strong>{{ $attendances->count() }}</strong> student(s) have scanned the QR code for this event.
            </small>
            <div>
                <span class="badge bg-success me-1">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $attendances->where('status', 'present')->count() }} Present
                </span>
                @if($attendances->whereNotIn('status', ['present'])->count() > 0)
                <span class="badge bg-warning text-dark">
                    {{ $attendances->whereNotIn('status', ['present'])->count() }} Other
                </span>
                @endif
            </div>
        </div>

        @else
        <div class="empty-state">
            <i class="bi bi-person-x text-muted"></i>
            <h6 class="text-muted">No students have scanned yet</h6>
            <p class="text-muted small mb-0">
                Students who scan the QR code during the event will appear here.
            </p>
        </div>
        @endif
    </div>
</div>

@endsection
