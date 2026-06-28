@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">
        <i class="bi bi-lightbulb text-primary me-2"></i> Student Event Ideas
    </h5>
</div>

<div class="content-box">
    <p class="text-muted mb-4">
        Overview of all event ideas suggested by students across the system. 
        Ideas with <strong>5 or more reports</strong> are automatically hidden from students on the mobile app, but remain visible here for administrative review.
    </p>

    <!-- Sorting Chips -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="?sort=trending" class="btn btn-sm rounded-pill px-3 fw-bold {{ $sort === 'trending' ? 'btn-primary text-white' : 'btn-light border text-dark' }}">
            <i class="bi bi-fire me-1"></i> Trending
        </a>
        <a href="?sort=new" class="btn btn-sm rounded-pill px-3 fw-bold {{ $sort === 'new' ? 'btn-primary text-white' : 'btn-light border text-dark' }}">
            <i class="bi bi-stars me-1"></i> Newest
        </a>
        <a href="?sort=top_month" class="btn btn-sm rounded-pill px-3 fw-bold {{ $sort === 'top_month' ? 'btn-primary text-white' : 'btn-light border text-dark' }}">
            <i class="bi bi-calendar-event me-1"></i> Top Month
        </a>
        <a href="?sort=top_all" class="btn btn-sm rounded-pill px-3 fw-bold {{ $sort === 'top_all' ? 'btn-primary text-white' : 'btn-light border text-dark' }}">
            <i class="bi bi-trophy me-1"></i> Top All Time
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 25%">Idea Title</th>
                    <th style="width: 30%">Description</th>
                    <th>Student</th>
                    <th class="text-center">Net Score</th>
                    <th class="text-center">Reports</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ideas as $idea)
                <tr class="{{ $idea->reports_count >= 5 ? 'table-danger' : '' }}">
                    <td class="fw-bold text-dark">{{ $idea->title }}</td>
                    <td class="text-muted" style="font-size: 0.9rem;">
                        {{ \Illuminate\Support\Str::limit($idea->description, 100) }}
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-circle text-secondary me-2 fs-5"></i>
                            {{ $idea->student ? $idea->student->name : 'Unknown' }}
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge rounded-pill fs-6 px-3 py-2 {{ $idea->net_score > 0 ? 'bg-primary' : ($idea->net_score < 0 ? 'bg-danger' : 'bg-secondary') }}">
                            {{ $idea->net_score }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($idea->reports_count >= 5)
                            <span class="badge bg-danger">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $idea->reports_count }} (Hidden)
                            </span>
                        @elseif($idea->reports_count > 0)
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-flag-fill me-1"></i> {{ $idea->reports_count }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ ucfirst($idea->status) }}</span>
                    </td>
                    <td class="text-muted small">
                        {{ $idea->created_at->format('d M Y, h:i A') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-3"></i>
                        No event ideas have been suggested yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
