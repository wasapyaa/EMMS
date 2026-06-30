@extends('organizer.layout')

@section('content')

{{-- SUCCESS MESSAGE --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ERROR MESSAGE --}}
@if(session('error'))
    <div class="alert alert-warning alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">
        <i class="bi bi-check-circle text-primary me-2"></i> Approved Events
    </h5>
</div>

<style>
@keyframes pulse {
    0% { opacity: 0.4; }
    50% { opacity: 1; }
    100% { opacity: 0.4; }
}
.animate-pulse {
    animation: pulse 1.5s infinite;
}
</style>

{{-- APPROVED EVENTS TABS --}}
<div class="content-box mb-4 py-3">
    <div class="d-flex gap-2" id="approvedEventsTabs" role="tablist">
        <button class="btn btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 btn-primary" 
            id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
            <i class="bi bi-grid"></i> All <span class="badge bg-white text-primary ms-1 rounded-pill" style="font-size:0.75rem;">{{ $allEvents->count() }}</span>
        </button>
        <button class="btn btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 btn-light border text-muted" 
            id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="false">
            <i class="bi bi-clock"></i> Upcoming Event <span class="badge bg-primary text-white ms-1 rounded-pill" style="font-size:0.75rem;">{{ $upcomingEvents->count() }}</span>
        </button>
        <button class="btn btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 btn-light border text-muted" 
            id="ongoing-tab" data-bs-toggle="tab" data-bs-target="#ongoing" type="button" role="tab" aria-controls="ongoing" aria-selected="false">
            <i class="bi bi-play-circle text-success"></i> Ongoing Event <span class="badge bg-success text-white ms-1 rounded-pill" style="font-size:0.75rem;">{{ $ongoingEvents->count() }}</span>
        </button>
        <button class="btn btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 btn-light border text-muted" 
            id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab" aria-controls="past" aria-selected="false">
            <i class="bi bi-archive"></i> Past Event <span class="badge bg-secondary text-white ms-1 rounded-pill" style="font-size:0.75rem;">{{ $pastEvents->count() }}</span>
        </button>
    </div>
</div>

<div class="tab-content" id="approvedEventsTabsContent">
    {{-- All Tab --}}
    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
        <div class="content-box p-0 overflow-hidden" style="border: 1px solid #eef2f6;">
            <table class="table align-middle mb-0">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 5%;">#</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 30%;">Event Name</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 18%;">Start Date</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 18%;">End Date</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 12%; text-align: center;">Merit Points</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 12%; text-align: center;">Attendance</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 10%; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($allEvents as $index => $e)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td class="text-muted small ps-3">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if(\Carbon\Carbon::parse($e->start_time)->gt(now()))
                                    <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#1d4ed8);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem;">
                                        <i class="bi bi-calendar-plus"></i>
                                    </span>
                                @elseif(\Carbon\Carbon::parse($e->end_time)->lt(now()))
                                    <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#6b7280,#374151);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem;">
                                        <i class="bi bi-calendar-minus"></i>
                                    </span>
                                @else
                                    <span class="animate-pulse" style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#10b981,#047857);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem;">
                                        <i class="bi bi-play-circle"></i>
                                    </span>
                                @endif
                                <div>
                                    <div class="fw-semibold text-dark" style="font-size:0.9rem;">{{ $e->title }}</div>
                                    <div class="text-muted small" style="font-size:0.75rem;">
                                        Category: {{ ucfirst($e->category) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-secondary fw-medium" style="font-size: 0.88rem;">{{ \Carbon\Carbon::parse($e->start_time)->format('Y-m-d H:i') }}</td>
                        <td class="text-secondary fw-medium" style="font-size: 0.88rem;">{{ \Carbon\Carbon::parse($e->end_time)->format('Y-m-d H:i') }}</td>
                        <td align="center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.78rem;">
                                {{ $e->merit_value }} Points
                            </span>
                        </td>
                        <td align="center">
                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2.5 py-1.5 fw-semibold" style="font-size: 0.78rem;">
                                <i class="bi bi-people-fill me-1"></i>{{ $e->attendances_count }} scanned
                            </span>
                        </td>
                        <td align="right">
                            <a href="/organizer/events/{{ $e->e_id }}"
                               class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-info-circle fs-4 mb-2 d-block text-muted"></i> No approved events found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Upcoming Tab --}}
    <div class="tab-pane fade" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
        <div class="content-box p-0 overflow-hidden" style="border: 1px solid #eef2f6;">
            <table class="table align-middle mb-0">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 5%;">#</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 35%;">Event Name</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 22%;">Start Date</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 14%; text-align: center;">Merit Points</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 14%; text-align: center;">Attendance</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 10%; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($upcomingEvents as $index => $e)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td class="text-muted small ps-3">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#1d4ed8);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem;">
                                    <i class="bi bi-calendar-plus"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold text-dark" style="font-size:0.9rem;">{{ $e->title }}</div>
                                    <div class="text-muted small" style="font-size:0.75rem;">
                                        Category: {{ ucfirst($e->category) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-secondary fw-medium" style="font-size: 0.88rem;">{{ \Carbon\Carbon::parse($e->start_time)->format('Y-m-d H:i') }}</td>
                        <td align="center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.78rem;">
                                {{ $e->merit_value }} Points
                            </span>
                        </td>
                        <td align="center">
                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2.5 py-1.5 fw-semibold" style="font-size: 0.78rem;">
                                <i class="bi bi-people-fill me-1"></i>{{ $e->attendances_count }} scanned
                            </span>
                        </td>
                        <td align="right">
                            <a href="/organizer/events/{{ $e->e_id }}"
                               class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-info-circle fs-4 mb-2 d-block text-muted"></i> No upcoming events found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Semasa (Ongoing) Tab --}}
    <div class="tab-pane fade" id="ongoing" role="tabpanel" aria-labelledby="ongoing-tab">
        <div class="content-box p-0 overflow-hidden" style="border: 1px solid #eef2f6;">
            <table class="table align-middle mb-0">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 5%;">#</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 35%;">Event Name</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 22%;">Start / End Date</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 14%; text-align: center;">Merit Points</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 14%; text-align: center;">Attendance</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 10%; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($ongoingEvents as $index => $e)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td class="text-muted small ps-3">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="animate-pulse" style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#10b981,#047857);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem;">
                                    <i class="bi bi-play-circle"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold text-dark" style="font-size:0.9rem;">{{ $e->title }}</div>
                                    <div class="text-muted small" style="font-size:0.75rem;">
                                        Category: {{ ucfirst($e->category) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-secondary fw-medium" style="font-size: 0.88rem;">
                            {{ \Carbon\Carbon::parse($e->start_time)->format('Y-m-d H:i') }} - {{ \Carbon\Carbon::parse($e->end_time)->format('H:i') }}
                        </td>
                        <td align="center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.78rem;">
                                {{ $e->merit_value }} Points
                            </span>
                        </td>
                        <td align="center">
                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2.5 py-1.5 fw-semibold" style="font-size: 0.78rem;">
                                <i class="bi bi-people-fill me-1"></i>{{ $e->attendances_count }} scanned
                            </span>
                        </td>
                        <td align="right">
                            <a href="/organizer/events/{{ $e->e_id }}"
                               class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-info-circle fs-4 mb-2 d-block text-muted"></i> No ongoing events found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Selepas (Past) Tab --}}
    <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
        <div class="content-box p-0 overflow-hidden" style="border: 1px solid #eef2f6;">
            <table class="table align-middle mb-0">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 5%;">#</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 30%;">Event Name</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 18%;">Start Date</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 18%;">End Date</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 12%; text-align: center;">Merit Points</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 12%; text-align: center;">Attendance</th>
                        <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 10%; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pastEvents as $index => $e)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td class="text-muted small ps-3">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#6b7280,#374151);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem;">
                                    <i class="bi bi-calendar-minus"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold text-dark" style="font-size:0.9rem;">{{ $e->title }}</div>
                                    <div class="text-muted small" style="font-size:0.75rem;">
                                        Category: {{ ucfirst($e->category) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-secondary fw-medium" style="font-size: 0.88rem;">{{ \Carbon\Carbon::parse($e->start_time)->format('Y-m-d H:i') }}</td>
                        <td class="text-secondary fw-medium" style="font-size: 0.88rem;">{{ \Carbon\Carbon::parse($e->end_time)->format('Y-m-d H:i') }}</td>
                        <td align="center">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.78rem;">
                                {{ $e->merit_value }} Points
                            </span>
                        </td>
                        <td align="center">
                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-2.5 py-1.5 fw-semibold" style="font-size: 0.78rem;">
                                <i class="bi bi-people-fill me-1"></i>{{ $e->attendances_count }} scanned
                            </span>
                        </td>
                        <td align="right">
                            <a href="/organizer/events/{{ $e->e_id }}"
                               class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-info-circle fs-4 mb-2 d-block text-muted"></i> No past events found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tabs = document.querySelectorAll('#approvedEventsTabs button');
        tabs.forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(event) {
                // Reset all tabs to inactive
                tabs.forEach(t => {
                    t.classList.remove('btn-primary');
                    t.classList.add('btn-light', 'border', 'text-muted');
                    
                    const badge = t.querySelector('.badge');
                    if (badge) {
                        if (t.id === 'all-tab') {
                            badge.className = 'badge bg-secondary ms-1 rounded-pill';
                        } else if (t.id === 'upcoming-tab') {
                            badge.className = 'badge bg-primary ms-1 rounded-pill';
                        } else if (t.id === 'ongoing-tab') {
                            badge.className = 'badge bg-success ms-1 rounded-pill';
                        } else if (t.id === 'past-tab') {
                            badge.className = 'badge bg-secondary ms-1 rounded-pill';
                        }
                    }
                });
                
                // Set active tab to btn-primary
                event.target.classList.remove('btn-light', 'border', 'text-muted');
                event.target.classList.add('btn-primary');
                
                const activeBadge = event.target.querySelector('.badge');
                if (activeBadge) {
                    activeBadge.className = 'badge bg-white text-primary ms-1 rounded-pill';
                }
            });
        });
    });
</script>

@endsection
