@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">
        <i class="bi bi-lightbulb text-primary me-2"></i> Student Event Ideas
    </h5>
</div>

<div class="content-box p-0 overflow-hidden" style="border: 1px solid #eef2f6;">
    <div class="p-4 bg-white">
        <p class="text-muted mb-4" style="font-size: 0.92rem;">
            Overview of all event ideas suggested by students across the system. 
            Ideas with <strong>5 or more reports</strong> are automatically hidden from students on the mobile app, but remain visible here for administrative review.
        </p>

        <!-- Sorting Chips -->
        <div class="d-flex flex-wrap gap-2 mb-2">
            <a href="?sort=trending" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 {{ $sort === 'trending' ? 'btn-primary' : 'btn-light border text-muted' }}">
                <i class="bi bi-fire"></i> Trending
            </a>
            <a href="?sort=new" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 {{ $sort === 'new' ? 'btn-primary' : 'btn-light border text-muted' }}">
                <i class="bi bi-stars"></i> Newest
            </a>
            <a href="?sort=top_month" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 {{ $sort === 'top_month' ? 'btn-primary' : 'btn-light border text-muted' }}">
                <i class="bi bi-calendar-event"></i> Top Month
            </a>
            <a href="?sort=top_all" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 {{ $sort === 'top_all' ? 'btn-primary' : 'btn-light border text-muted' }}">
                <i class="bi bi-trophy"></i> Top All Time
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 25%;">Idea Title</th>
                    <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 30%;">Description</th>
                    <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 15%;">Student</th>
                    <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 10%; text-align: center;">Net Score</th>
                    <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 12%; text-align: center;">Reports</th>
                    <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 10%;">Status</th>
                    <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 12%;">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ideas as $idea)
                <tr style="border-bottom: 1px solid #f3f4f6; @if($idea->reports_count >= 5) background-color: #fff5f5; @endif">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#a78bfa);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.85rem;">
                                <i class="bi bi-lightbulb-fill"></i>
                            </span>
                            <div class="fw-semibold text-dark" style="font-size:0.9rem;">{{ $idea->title }}</div>
                        </div>
                    </td>
                    <td class="text-secondary" style="font-size: 0.88rem; max-width: 220px;">
                        {{ \Illuminate\Support\Str::limit($idea->description, 120) }}
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:28px;height:28px;border-radius:50%;background:#eef2f6;display:inline-flex;align-items:center;justify-content:center;color:#64748b;font-size:0.75rem;">
                                <i class="bi bi-person"></i>
                            </span>
                            <span class="text-secondary fw-medium" style="font-size:0.85rem;">
                                {{ $idea->student ? $idea->student->name : 'Unknown' }}
                            </span>
                        </div>
                    </td>
                    <td align="center">
                        <span class="badge rounded-pill px-3 py-1.5 fw-bold
                            @if($idea->net_score > 0) bg-primary-subtle text-primary border border-primary-subtle
                            @elseif($idea->net_score < 0) bg-danger-subtle text-danger border border-danger-subtle
                            @else bg-secondary-subtle text-secondary border border-secondary-subtle
                            @endif" style="font-size: 0.78rem;">
                            {{ $idea->net_score }}
                        </span>
                    </td>
                    <td align="center">
                        @if($idea->reports_count >= 5)
                            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem;">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $idea->reports_count }} (Hidden)
                            </span>
                        @elseif($idea->reports_count > 0)
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1.5 fw-semibold" style="font-size: 0.78rem;">
                                <i class="bi bi-flag-fill me-1"></i> {{ $idea->reports_count }}
                            </span>
                        @else
                            <span class="text-muted" style="font-size:0.85rem;">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle px-2.5 py-1.5 fw-bold" style="font-size: 0.78rem;">
                            {{ ucfirst($idea->status) }}
                        </span>
                    </td>
                    <td class="text-secondary fw-medium" style="font-size: 0.85rem;">
                        {{ $idea->created_at->format('d M Y, h:i A') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-3 text-muted"></i>
                        No event ideas have been suggested yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
