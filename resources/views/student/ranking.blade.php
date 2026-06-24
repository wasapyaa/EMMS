@extends('student.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">
        <i class="bi bi-trophy me-2"></i> Student Ranking
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

<!-- CURRENT RANK CARD -->
<div class="row mb-4">
    <div class="col-md-4">
                <div class="stat-card {{ $ranking <= 130 ? 'bg-rank' : 'bg-danger' }}">
            <h6>Current Ranking</h6>
            <h2 class="fw-bold">{{ $ranking }}/130</h2>
            <p class="mb-0 mt-2 small">
                @if($ranking <= 130)
                    You are eligible to receive hostel accommodation for the next semester.
                @else
                    You will not be eligible for hostel accommodation next semester.
                @endif
            </p>
        </div>
    </div>
</div>

<!-- RANKING TREND GRAPH -->
<div class="content-box mb-4">
    <h6 class="fw-bold mb-3">
        <i class="bi bi-graph-up-arrow me-1"></i>
        My Ranking Trend
    </h6>

    @if(!empty($rankingHistory))
        <canvas id="rankingTrendChart" height="120"></canvas>
    @else
        <p class="text-muted">No ranking history available.</p>
    @endif
</div>

<!-- LEADERBOARD -->
<div class="content-box">
    <h6 class="fw-bold mb-3">Leaderboard</h6>

    <table class="table align-middle table-hover">
        <thead class="text-muted">
            <tr>
                <th>Rank</th>
                <th>Student Name</th>
                <th>Total Merit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $s)
            <tr class="{{ $s->s_id == session('user_id') ? 'table-primary fw-bold' : '' }}">
                <td>#{{ $index + 1 }}</td>
                <td>
                    {{ $s->name }}
                    @if($s->s_id == session('user_id'))
                        <span class="badge bg-info ms-1">You</span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-success">
                        {{ $s->total_merit }} Points
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
@if(!empty($rankingHistory))
const ctx = document.getElementById('rankingTrendChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            @foreach($rankingHistory as $r)
                "{{ $r['date'] }}",
            @endforeach
        ],
        datasets: [{
            label: 'My Ranking Position',
            data: [
                @foreach($rankingHistory as $r)
                    {{ $r['rank'] }},
                @endforeach
            ],
            borderWidth: 3,
            tension: 0.4,
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                reverse: true,
                beginAtZero: false,
                ticks: {
                    precision: 0,
                    stepSize: 1
                },
                title: {
                    display: true,
                    text: 'Ranking Position'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Date'
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
@endif
</script>

@endsection
