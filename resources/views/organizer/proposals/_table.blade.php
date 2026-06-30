<table class="table mb-0 align-middle">
    <thead style="background:#f8f9fa;">
        <tr>
            <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 5%;">#</th>
            <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 45%;">Event Name</th>
            <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 15%;">Date</th>
            <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 15%;">Status</th>
            <th style="font-size:0.8rem;text-transform:uppercase;color:#6c757d;padding:12px 16px; width: 20%; text-align: right;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($proposals as $index => $p)
        <tr style="border-bottom: 1px solid #f3f4f6;">
            <td class="text-muted small ps-3">{{ $index + 1 }}</td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    @if($p->status == 'approved')
                        <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#0f766e);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem;">
                            <i class="bi bi-calendar-check"></i>
                        </span>
                    @elseif($p->status == 'pending')
                        <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#d97706,#b45309);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem;">
                            <i class="bi bi-calendar-plus"></i>
                        </span>
                    @else
                        <span style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#dc2626,#991b1b);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem;">
                            <i class="bi bi-calendar-x"></i>
                        </span>
                    @endif
                    <div>
                        <div class="fw-semibold text-dark" style="font-size:0.9rem;">{{ $p->title }}</div>
                        <div class="text-muted small" style="font-size:0.75rem;">
                            Created: {{ $p->created_at ? $p->created_at->format('d M Y') : '—' }}
                        </div>
                    </div>
                </div>
            </td>
            <td class="text-secondary fw-medium" style="font-size: 0.88rem;">
                {{ \Carbon\Carbon::parse($p->start_time)->format('Y-m-d') }}
            </td>
            <td>
                <span class="badge px-2.5 py-1.5 fw-bold
                    @if($p->status == 'approved') bg-success-subtle text-success-emphasis border border-success-subtle
                    @elseif($p->status == 'pending') bg-warning-subtle text-warning-emphasis border border-warning-subtle
                    @elseif($p->status == 'rejected') bg-danger-subtle text-danger-emphasis border border-danger-subtle
                    @endif" style="font-size: 0.78rem;">
                    {{ ucfirst($p->status) }}
                </span>
            </td>
            <td>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ url('/organizer/proposals/'.$p->e_id) }}" 
                       class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                        <i class="bi bi-eye"></i> View
                    </a>
                    @if(\Carbon\Carbon::parse($p->end_time)->isPast())
                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1.5 disabled" disabled title="Event has ended" style="font-size: 0.78rem;">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                    @else
                        <a href="{{ url('/organizer/proposals/'.$p->e_id.'/edit') }}" 
                           class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    @endif
                    @if($p->status == 'approved')
                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1.5 disabled" disabled title="Approved events cannot be deleted" style="font-size: 0.78rem;">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    @else
                        <form action="{{ url('/organizer/proposals/'.$p->e_id) }}" method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this proposal?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center text-muted py-4">
                <i class="bi bi-info-circle me-1"></i>
                No proposals found.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
