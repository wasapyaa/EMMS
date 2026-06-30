@extends('organizer.layout')

@section('content')
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
    <h5 class="fw-bold mb-0">
        <i class="bi bi-file-earmark-text me-2"></i> Event Proposals
    </h5>
    <a href="/organizer/proposals/create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Submit New Proposal
    </a>
</div>

{{-- TABS --}}
<div class="content-box mb-4 py-3">
    <div class="nav d-flex gap-2" id="proposalTabs" role="tablist">
        <button class="btn btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 btn-primary active" 
            id="all-tab" data-bs-toggle="tab" data-bs-target="#all"
            type="button" role="tab" aria-controls="all" aria-selected="true">
            <i class="bi bi-grid"></i> All <span class="badge bg-white text-primary ms-1 rounded-pill" style="font-size:0.75rem;">{{ $allProposals->count() }}</span>
        </button>
        <button class="btn btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 btn-light border text-muted" 
            id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
            type="button" role="tab" aria-controls="pending" aria-selected="false">
            <i class="bi bi-clock"></i> Pending <span class="badge bg-warning text-dark ms-1 rounded-pill" style="font-size:0.75rem;">{{ $pendingProposals->count() }}</span>
        </button>
        <button class="btn btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 btn-light border text-muted" 
            id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved"
            type="button" role="tab" aria-controls="approved" aria-selected="false">
            <i class="bi bi-check-circle"></i> Approved <span class="badge bg-success text-white ms-1 rounded-pill" style="font-size:0.75rem;">{{ $approvedProposals->count() }}</span>
        </button>
        <button class="btn btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 btn-light border text-muted" 
            id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected"
            type="button" role="tab" aria-controls="rejected" aria-selected="false">
            <i class="bi bi-x-circle"></i> Rejected <span class="badge bg-danger text-white ms-1 rounded-pill" style="font-size:0.75rem;">{{ $rejectedProposals->count() }}</span>
        </button>
    </div>
</div>

<div class="tab-content" id="proposalTabsContent">

    {{-- ALL TAB --}}
    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
        <div class="content-box p-0 overflow-hidden" style="border: 1px solid #eef2f6;">
            @include('organizer.proposals._table', ['proposals' => $allProposals])
        </div>
    </div>

    {{-- PENDING TAB --}}
    <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
        <div class="content-box p-0 overflow-hidden" style="border: 1px solid #eef2f6;">
            @include('organizer.proposals._table', ['proposals' => $pendingProposals])
        </div>
    </div>

    {{-- APPROVED TAB --}}
    <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
        <div class="content-box p-0 overflow-hidden" style="border: 1px solid #eef2f6;">
            @include('organizer.proposals._table', ['proposals' => $approvedProposals])
        </div>
    </div>

    {{-- REJECTED TAB --}}
    <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
        <div class="content-box p-0 overflow-hidden" style="border: 1px solid #eef2f6;">
            @include('organizer.proposals._table', ['proposals' => $rejectedProposals])
        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tabs = document.querySelectorAll('#proposalTabs button');
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
                        } else if (t.id === 'pending-tab') {
                            badge.className = 'badge bg-warning text-dark ms-1 rounded-pill';
                        } else if (t.id === 'approved-tab') {
                            badge.className = 'badge bg-success text-white ms-1 rounded-pill';
                        } else if (t.id === 'rejected-tab') {
                            badge.className = 'badge bg-danger text-white ms-1 rounded-pill';
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
