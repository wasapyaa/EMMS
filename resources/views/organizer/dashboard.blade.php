@extends('organizer.layout')

@section('content')
@if(session('error'))
    <div class="alert alert-warning alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Stat Cards -->
<div class="row g-4 mb-4">

    <div class="col-md-3">
        <div class="stat-card bg-merit">
            <h6>Total Proposals</h6>
            <h2 class="fw-bold">{{ $totalProposals }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card bg-rank">
            <h6>Approved Events</h6>
            <h2 class="fw-bold">{{ $approvedEvents }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card bg-event">
            <h6>Pending Events</h6>
            <h2 class="fw-bold">{{ $pendingEvents }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card" style="background: #dc3545; color: #fff;">
            <h6>Rejected Events</h6>
            <h2 class="fw-bold">{{ $rejectedEvents }}</h2>
        </div>
    </div>

</div>

<!-- Participation Analytics Section -->
<h5 class="fw-bold mb-3 mt-5">
    <i class="bi bi-graph-up-arrow me-2 text-primary"></i> Participation Analytics
</h5>
<div class="row g-4 mb-4">
    <!-- Total Scanned Attendees Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 small text-uppercase fw-semibold mb-1">Total Student Scans</h6>
                    <h2 class="fw-bold mb-0">{{ $totalAttendees }}</h2>
                    <span class="small text-white-50">Across all approved events</span>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="bi bi-people-fill fs-3 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Turnout Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3" style="background: linear-gradient(135deg, #0d9488, #14b8a6); color: white;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 small text-uppercase fw-semibold mb-1">Average Turnout</h6>
                    <h2 class="fw-bold mb-0">{{ $averageAttendance }}</h2>
                    <span class="small text-white-50">Students per approved event</span>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="bi bi-graph-up fs-3 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Event Card -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3" style="background: linear-gradient(135deg, #7c3aed, #a78bfa); color: white;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="overflow-hidden" style="max-width: calc(100% - 66px);">
                    <h6 class="text-white-50 small text-uppercase fw-semibold mb-1">Most Popular Event</h6>
                    <h2 class="fw-bold mb-0 text-truncate" title="{{ $topEventName }}">{{ $topEventName }}</h2>
                    <span class="small text-white-50">{{ $topEventCount }} student scans</span>
                </div>
                <div class="bg-white bg-opacity-20 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; flex-shrink: 0;">
                    <i class="bi bi-trophy-fill fs-3 text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Analysis Section -->
@php
    $palette = ['#5c4ae4', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#6b7280'];
    $icons = [
        'Sport' => '🏆',
        'Education' => '📚',
        'Entertainment' => '🎭',
        'Social' => '🤝',
        'Technical' => '💻',
        'Other' => '📁'
    ];
@endphp
<div class="content-box mb-4">
    <div class="d-flex align-items-center mb-1">
        <i class="bi bi-graph-up-arrow me-2 text-primary fs-4"></i>
        <h4 class="fw-bold mb-0">Event Participation Statistics</h4>
    </div>
    <p class="text-muted small mb-4">The statistics below show the number of student participations for each event category.</p>
    
    <div class="row g-4 align-items-center">
        <!-- Left Side: Doughnut Chart Card -->
        <div class="col-md-5">
            <div class="p-4 rounded-4 text-center d-flex flex-column align-items-center justify-content-center" style="background-color: #f8f9fa; border: 1px solid #eef2f6;">
                @if($hasData)
                    <div style="position: relative; height:240px; width:240px;" class="mb-3">
                        <canvas id="categoryDoughnutChart"></canvas>
                    </div>
                    
                    <!-- Horizontal Legend -->
                    <div class="d-flex flex-wrap justify-content-center gap-3 mt-2">
                        @foreach($chartLabels as $index => $label)
                            <div class="d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                <span class="d-inline-block rounded-1" style="width:12px; height:12px; background-color: {{ $palette[$index % count($palette)] }};"></span>
                                <span class="fw-semibold text-secondary">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-pie-chart fs-1 mb-2 d-block text-muted"></i>
                        No student participation data to display.
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Right Side: Detailed Table Analysis -->
        <div class="col-md-7">
            <h5 class="fw-bold mb-3" style="color: #374151;">Detailed Analysis By Category</h5>
            
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-secondary" style="border-bottom: 2px solid #eef2f6; font-size: 0.85rem;">
                            <th class="pb-2 fw-semibold">Category</th>
                            <th class="pb-2 fw-semibold text-center">Events</th>
                            <th class="pb-2 fw-semibold text-center">Total Joined</th>
                            <th class="pb-2 fw-semibold text-end">Avg / Event</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detailedAnalysis as $index => $item)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td class="py-2.5 fw-semibold" style="font-size: 0.95rem; color: #1f2937;">
                                    <span class="me-1.5">{{ $icons[$item['category']] ?? '📁' }}</span> {{ $item['category'] }}
                                </td>
                                <td class="py-2.5 text-center text-secondary" style="font-size: 0.9rem;">
                                    {{ $item['events'] }}
                                </td>
                                <td class="py-2.5 text-center">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold" style="font-size: 0.82rem;">
                                        {{ $item['total_joined'] }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-end text-success fw-bold" style="font-size: 0.95rem;">
                                    {{ $item['avg'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No active event data found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if($hasData)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('categoryDoughnutChart').getContext('2d');
        const labels = {!! json_encode($chartLabels) !!};
        const data = {!! json_encode($chartData) !!};
        const colors = {!! json_encode($palette) !!};

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return ' ' + label + ': ' + value + ' Participations';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endif

@endsection
