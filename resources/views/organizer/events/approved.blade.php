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
    <h5 class="fw-bold mb-4">
        <i class="bi bi-check-circle me-2"></i> Approved Events
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
<ul class="nav nav-tabs mb-4" id="approvedEventsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
            All <span class="badge bg-secondary ms-1">{{ $allEvents->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="false">
            Upcoming Event <span class="badge bg-primary ms-1">{{ $upcomingEvents->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="ongoing-tab" data-bs-toggle="tab" data-bs-target="#ongoing" type="button" role="tab" aria-controls="ongoing" aria-selected="false">
            Ongoing Event <span class="badge bg-success ms-1">{{ $ongoingEvents->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab" aria-controls="past" aria-selected="false">
            Past Event <span class="badge bg-secondary ms-1">{{ $pastEvents->count() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content" id="approvedEventsTabsContent">
    {{-- All Tab --}}
    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
        <div class="content-box">
            <table class="table align-middle table-hover mb-0">
                <thead class="text-muted">
                    <tr>
                        <th>Event Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Merit Points</th>
                        <th>Attendance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($allEvents as $e)
                    <tr>
                        <td>
                            <i class="bi bi-circle-fill me-2
                                @if(\Carbon\Carbon::parse($e->start_time)->gt(now())) text-primary
                                @elseif(\Carbon\Carbon::parse($e->end_time)->lt(now())) text-secondary
                                @else text-success animate-pulse
                                @endif"
                                style="font-size:8px"></i>
                            {{ $e->title }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($e->start_time)->format('Y-m-d H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($e->end_time)->format('Y-m-d H:i') }}</td>
                        <td><span class="badge bg-primary">{{ $e->merit_value }} Points</span></td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-people-fill me-1"></i>{{ $e->attendances_count }} scanned
                            </span>
                        </td>
                        <td>
                            <a href="/organizer/events/{{ $e->e_id }}"
                               class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye me-1"></i> View Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-info-circle me-1"></i> No approved events found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Upcoming Tab --}}
    <div class="tab-pane fade" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
        <div class="content-box">
            <table class="table align-middle table-hover mb-0">
                <thead class="text-muted">
                    <tr>
                        <th>Event Name</th>
                        <th>Start Date</th>
                        <th>Merit Points</th>
                        <th>Attendance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($upcomingEvents as $e)
                    <tr>
                        <td>
                            <i class="bi bi-circle-fill text-primary me-2" style="font-size:8px"></i>
                            {{ $e->title }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($e->start_time)->format('Y-m-d H:i') }}
                        </td>
                        <td>
                            <span class="badge bg-primary">
                                {{ $e->merit_value }} Points
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-people-fill me-1"></i>{{ $e->attendances_count }} scanned
                            </span>
                        </td>
                        <td>
                            <a href="/organizer/events/{{ $e->e_id }}"
                               class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye me-1"></i> View Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-info-circle me-1"></i>
                            No upcoming events found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Semasa (Ongoing) Tab --}}
    <div class="tab-pane fade" id="ongoing" role="tabpanel" aria-labelledby="ongoing-tab">
        <div class="content-box">
            <table class="table align-middle table-hover mb-0">
                <thead class="text-muted">
                    <tr>
                        <th>Event Name</th>
                        <th>Start / End Date</th>
                        <th>Merit Points</th>
                        <th>Attendance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($ongoingEvents as $e)
                    <tr>
                        <td>
                            <i class="bi bi-circle-fill text-success me-2 animate-pulse" style="font-size:8px"></i>
                            {{ $e->title }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($e->start_time)->format('Y-m-d H:i') }} - {{ \Carbon\Carbon::parse($e->end_time)->format('H:i') }}
                        </td>
                        <td>
                            <span class="badge bg-primary">
                                {{ $e->merit_value }} Points
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-people-fill me-1"></i>{{ $e->attendances_count }} scanned
                            </span>
                        </td>
                        <td>
                            <a href="/organizer/events/{{ $e->e_id }}"
                               class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye me-1"></i> View Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-info-circle me-1"></i>
                            No ongoing events found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Selepas (Past) Tab --}}
    <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
        <div class="content-box">
            <table class="table align-middle table-hover mb-0">
                <thead class="text-muted">
                    <tr>
                        <th>Event Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Merit Points</th>
                        <th>Attendance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pastEvents as $e)
                    <tr>
                        <td>
                            <i class="bi bi-circle-fill text-secondary me-2" style="font-size:8px"></i>
                            {{ $e->title }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($e->start_time)->format('Y-m-d H:i') }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($e->end_time)->format('Y-m-d H:i') }}
                        </td>
                        <td>
                            <span class="badge bg-primary">
                                {{ $e->merit_value }} Points
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-people-fill me-1"></i>{{ $e->attendances_count }} scanned
                            </span>
                        </td>
                        <td>
                            <a href="/organizer/events/{{ $e->e_id }}"
                               class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye me-1"></i> View Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-info-circle me-1"></i>
                            No past events found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
