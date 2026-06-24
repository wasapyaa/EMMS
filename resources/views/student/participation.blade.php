@extends('student.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">
        <i class="bi bi-calendar-check me-2"></i> Event Participation
    </h5>
    
    <form method="GET" class="d-flex">
        <select name="semester" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="current" {{ $selectedSemester == 'current' ? 'selected' : '' }}>Current Semester</option>
            @foreach($semesters as $sem)
                <option value="{{ $sem }}" {{ $selectedSemester == $sem ? 'selected' : '' }}>{{ $sem }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="content-box">

    <table class="table align-middle table-hover">
        <thead class="text-muted">
            <tr>
                <th>Event Name</th>
                <th>Location</th>
                <th>Date</th>
                <th>Merit</th>
            </tr>
        </thead>
        <tbody>

            @forelse($participations as $p)
            <tr>
                <td>
                    <i class="bi bi-circle-fill text-success me-2" style="font-size:8px"></i>
                    {{ $p->event_name }}
                </td>
                <td>{{ $p->location_name }}</td>
                <td>{{ \Carbon\Carbon::parse($p->event_date)->format('Y-m-d') }}</td>
                <td>
                    <span class="badge bg-primary">
                        {{ $p->merit_value }} Points
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    <i class="bi bi-info-circle me-1"></i>
                    No participation record found.
                </td>
            </tr>
            @endforelse

        </tbody>
    </table>

</div>

@endsection
