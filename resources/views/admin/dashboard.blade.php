@extends('admin.layout')

@section('content')
<style>
    .stat-card.clickable-card {
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card.clickable-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-warning alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-4">
        <i class="bi bi-speedometer2 me-2"></i> Admin Dashboard
    </h5>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card bg-merit">
            <h6>Active Students (Current Sem)</h6>
            <h2 class="fw-bold">{{ $totalStudents }}</h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card bg-rank">
            <h6>Total Organizers</h6>
            <h2 class="fw-bold">{{ $totalOrganizers }}</h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card bg-event clickable-card" data-bs-toggle="modal" data-bs-target="#eventStatsModal">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Total Events</h6>
                    <h2 class="fw-bold mb-0">{{ $totalEvents }}</h2>
                </div>
                <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-2 py-1 small">
                    <i class="bi bi-info-circle-fill me-1"></i>Stats
                </span>
            </div>
        </div>
    </div>
</div>

<div class="content-box mb-4">
    <h6 class="fw-bold mb-3">Hostel & Semester Settings</h6>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="stat-card bg-event h-100">
                <h6>Current Hostel Quota</h6>
                <h2 class="fw-bold">{{ $eligibleStudents }}</h2>
                <p class="mb-0 mt-2 small">
                    You can update this value on the <a href="/admin/hostel">Hostel Eligibility</a> page.
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card bg-merit h-100" style="background: linear-gradient(135deg, #4f46e5, #3b82f6);">
                <h6>Active Semester Join Code</h6>
                <h2 class="fw-bold text-white">{{ $currentSemesterCode }}</h2>
                <p class="mb-0 mt-2 small text-white-50">
                    Share this code with active students so they can join the current semester.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Event Statistics Modal -->
<div class="modal fade" id="eventStatsModal" tabindex="-1" aria-labelledby="eventStatsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="eventStatsModalLabel">
                    <i class="bi bi-bar-chart-line-fill text-primary me-2"></i> Event Participation Statistics
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="text-muted small mb-4">
                    The statistics below show the number of student participations for each event category.
                </p>

                @if($categoryStats->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-info-circle fs-3 mb-2 d-block"></i>
                        <p class="mb-0">No student participation data available yet.</p>
                    </div>
                @else
                    <div class="row align-items-center">
                        <!-- Chart Column -->
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="p-3 bg-light rounded-4 d-flex justify-content-center align-items-center" style="min-height: 280px;">
                                <canvas id="categoryStatsChart"></canvas>
                            </div>
                        </div>

                        <!-- Detailed Table Column -->
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3 text-secondary">Detailed Analysis By Category</h6>
                            <div class="table-responsive" style="max-height: 280px; overflow-y: auto; padding-right: 5px;">
                                <table class="table table-sm align-middle table-hover border-0">
                                    <thead>
                                        <tr class="text-muted small border-bottom" style="font-size: 11px;">
                                            <th class="border-0 pb-2">Category</th>
                                            <th class="border-0 pb-2 text-center">Events</th>
                                            <th class="border-0 pb-2 text-center">Total Joined</th>
                                            <th class="border-0 pb-2 text-center">Avg / Event</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($categoryStats as $index => $stat)
                                            <tr style="border-bottom: 1px solid #f1f3f5;">
                                                <td class="py-2 border-0 fw-semibold text-dark small">
                                                    @if($index == 0 && $stat->total_participants > 0)
                                                        🏆 
                                                    @endif
                                                    {{ $stat->category }}
                                                </td>
                                                <td class="py-2 border-0 text-center text-muted small">
                                                    {{ $stat->total_events }}
                                                </td>
                                                <td class="py-2 border-0 text-center">
                                                    <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2 fw-semibold" style="font-size: 11px;">
                                                        {{ $stat->total_participants }}
                                                    </span>
                                                </td>
                                                <td class="py-2 border-0 text-center fw-bold text-success small">
                                                    {{ $stat->average_participants }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@if(!$categoryStats->isEmpty())
<!-- Include Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('categoryStatsChart').getContext('2d');
    
    const labels = {!! json_encode($categoryStats->pluck('category')) !!};
    const data = {!! json_encode($categoryStats->pluck('total_participants')) !!};
    const colors = ['#4f46e5', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, labels.length),
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: {
                            size: 11,
                            weight: '500'
                        },
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ` ${context.label}: ${context.raw} students`;
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });
});
</script>
@endif

@endsection
