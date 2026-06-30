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

{{-- BREADCRUMB --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="/organizer/proposals" class="text-decoration-none">
                <i class="bi bi-file-earmark-text me-1"></i>List Proposal
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($proposal->title, 40) }}</li>
    </ol>
</nav>

@php
    $statusLabel = ucfirst($proposal->status);
    if ($proposal->status == 'approved') {
        $statusClass = 'bg-success';
        $statusIcon  = 'bi-check-circle-fill';
    } elseif ($proposal->status == 'rejected') {
        $statusClass = 'bg-danger';
        $statusIcon  = 'bi-x-circle-fill';
    } else {
        $statusClass = 'bg-warning text-dark';
        $statusIcon  = 'bi-clock-fill';
        $statusLabel = 'Pending Review';
    }
@endphp

{{-- PROPOSAL DETAIL CARD --}}
<div class="card detail-card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0 text-white">
            <i class="bi bi-file-earmark-text me-2"></i> Proposal Details
        </h5>
        <span class="status-pill {{ $statusClass }}">
            <i class="bi {{ $statusIcon }}"></i> {{ $statusLabel }}
        </span>
    </div>

    <div class="card-body p-4">
        @if($proposal->event_banner)
            <div class="mb-4 text-center">
                <img src="{{ asset('storage/'.$proposal->event_banner) }}" alt="Event Banner" class="img-fluid rounded-4 shadow-sm" style="max-height: 350px; width: 100%; object-fit: cover;">
            </div>
        @endif

        <div class="row g-4">

            {{-- LEFT COLUMN --}}
            <div class="col-md-8">

                <div class="mb-4">
                    <p class="info-label"><i class="bi bi-tag me-1"></i> Title</p>
                    <p class="info-value">{{ $proposal->title }}</p>
                </div>

                <div class="mb-4">
                    <p class="info-label"><i class="bi bi-file-text me-1"></i> Brief Description</p>
                    <p class="info-value">{{ $proposal->description ?: '—' }}</p>
                </div>

                <div class="mb-4">
                    <p class="info-label"><i class="bi bi-card-text me-1"></i> Event Details (Tentative / Info)</p>
                    <div class="p-3 bg-light rounded-3 border" style="white-space: pre-line; font-size: 0.95rem; line-height: 1.6; color: #333;">
                        {{ $proposal->event_details ?: 'No detailed information provided.' }}
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6 mb-2">
                        <p class="info-label"><i class="bi bi-tags me-1"></i> Category</p>
                        <p class="info-value">{{ $proposal->category ?: '—' }}</p>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <p class="info-label"><i class="bi bi-geo-alt me-1"></i> Location</p>
                        <p class="info-value">{{ $proposal->location_name ?: '—' }}</p>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <p class="info-label"><i class="bi bi-clock me-1"></i> Start Time</p>
                        <p class="info-value">{{ $proposal->start_time ? \Carbon\Carbon::parse($proposal->start_time)->format('l, F j, Y \a\t g:i A') : '—' }}</p>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <p class="info-label"><i class="bi bi-clock-history me-1"></i> End Time</p>
                        <p class="info-value">{{ $proposal->end_time ? \Carbon\Carbon::parse($proposal->end_time)->format('l, F j, Y \a\t g:i A') : '—' }}</p>
                    </div>
                </div>

                @if($proposal->telegram_link || $proposal->whatsapp_link)
                <div class="mt-2">
                    <p class="info-label"><i class="bi bi-share me-1"></i> Social Links</p>
                    <div class="d-flex gap-2 flex-wrap">
                        @if($proposal->telegram_link)
                            <a href="{{ $proposal->telegram_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-telegram me-1"></i> Telegram
                            </a>
                        @endif
                        @if($proposal->whatsapp_link)
                            <a href="{{ $proposal->whatsapp_link }}" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-whatsapp me-1"></i> WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
                @endif

            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-md-4">

                {{-- Status detail --}}
                @if($proposal->status == 'approved')
                <div class="mb-4 p-3 rounded-3" style="background:#f0fdf4">
                    <p class="info-label mb-2"><i class="bi bi-star-fill text-warning me-1"></i> Merit Points</p>
                    @if($proposal->merit_value)
                        <span class="fs-3 fw-bold text-success">{{ $proposal->merit_value }}</span>
                        <span class="text-muted ms-1">pts</span>
                    @else
                        <span class="text-muted small">Not assigned yet</span>
                    @endif
                </div>
                @endif

                {{-- Proposal PDF --}}
                <div class="mb-4">
                    <p class="info-label"><i class="bi bi-file-earmark-pdf text-danger me-1"></i> Proposal PDF</p>
                    @if($proposal->proposal_path)
                        <a href="{{ asset('storage/'.$proposal->proposal_path) }}"
                           target="_blank"
                           class="btn btn-outline-success btn-sm">
                            <i class="bi bi-eye me-1"></i> View PDF
                        </a>
                    @else
                        <span class="text-muted small"><i class="bi bi-dash-circle me-1"></i> No file uploaded</span>
                    @endif
                </div>

                {{-- QR Code (only if approved) --}}
                @if($proposal->status == 'approved' && $proposal->qr_path)
                <div class="mb-4">
                    <p class="info-label"><i class="bi bi-qr-code me-1"></i> QR Code</p>
                    <div class="mb-2">
                        <img src="{{ url('/organizer/events/'.$proposal->e_id.'/download-qr') }}" alt="QR Code" class="img-fluid border rounded p-2 bg-white" style="max-width: 150px;">
                    </div>
                    <a href="{{ url('/organizer/events/'.$proposal->e_id.'/download-qr') }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-download me-1"></i> Download QR Code
                    </a>
                </div>
                @endif

                {{-- Submitted Date --}}
                <div class="mb-2">
                    <p class="info-label"><i class="bi bi-calendar-plus me-1"></i> Submitted</p>
                    <p class="info-value">{{ \Carbon\Carbon::parse($proposal->created_at)->format('d M Y, g:i A') }}</p>
                </div>

            </div>
        </div>

        {{-- Reject reason (if any) --}}
        @if($proposal->status == 'rejected' && $proposal->reject_reason)
        <div class="reject-reason-box mt-3">
            <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Reason for Rejection:</strong>
            <p class="mb-0 mt-1">{{ $proposal->reject_reason }}</p>
        </div>
        @endif

    </div>

    <div class="card-footer bg-white text-end py-3 px-4">
        @if($proposal->status == 'pending')
            <a href="/organizer/proposals/{{ $proposal->e_id }}/edit" class="btn btn-outline-warning me-2">
                <i class="bi bi-pencil me-1"></i> Edit Proposal
            </a>
        @endif
        <a href="/organizer/proposals" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Proposals
        </a>
    </div>
</div>

@endsection
