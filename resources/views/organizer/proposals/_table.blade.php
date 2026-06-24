<table class="table align-middle table-hover mb-0">
    <thead class="text-muted">
        <tr>
            <th>Event Name</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($proposals as $p)
        <tr>
            <td>
                <i class="bi bi-circle-fill me-2
                    @if($p->status == 'approved') text-success
                    @elseif($p->status == 'pending') text-warning
                    @elseif($p->status == 'rejected') text-danger
                    @else text-secondary
                    @endif"
                    style="font-size:8px">
                </i>
                {{ $p->title }}
            </td>
            <td>
                {{ \Carbon\Carbon::parse($p->start_time)->format('Y-m-d') }}
            </td>
            <td>
                <span class="badge
                    @if($p->status == 'approved') bg-success
                    @elseif($p->status == 'pending') bg-warning text-dark
                    @elseif($p->status == 'rejected') bg-danger
                    @endif">
                    {{ ucfirst($p->status) }}
                </span>
            </td>
            <td>
                <div class="d-flex gap-2">
                    <a href="{{ url('/organizer/proposals/'.$p->e_id) }}" class="btn btn-sm btn-info text-white">
                        <i class="bi bi-eye me-1"></i> View
                    </a>
                    @if(\Carbon\Carbon::parse($p->end_time)->isPast())
                        <button class="btn btn-sm btn-warning disabled" disabled title="Event has ended">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </button>
                    @else
                        <a href="{{ url('/organizer/proposals/'.$p->e_id.'/edit') }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                    @endif
                    @if($p->status == 'approved')
                        <button class="btn btn-sm btn-danger disabled" disabled>
                            <i class="bi bi-trash me-1"></i> Delete
                        </button>
                    @else
                        <form action="{{ url('/organizer/proposals/'.$p->e_id) }}" method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this proposal?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center text-muted py-4">
                <i class="bi bi-info-circle me-1"></i>
                No proposals found.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
