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
<ul class="nav nav-tabs mb-4" id="proposalTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all"
            type="button" role="tab" aria-controls="all" aria-selected="true">
            All <span class="badge bg-secondary ms-1">{{ $allProposals->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
            type="button" role="tab" aria-controls="pending" aria-selected="false">
            Pending <span class="badge bg-warning text-dark ms-1">{{ $pendingProposals->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved"
            type="button" role="tab" aria-controls="approved" aria-selected="false">
            Approved <span class="badge bg-success ms-1">{{ $approvedProposals->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected"
            type="button" role="tab" aria-controls="rejected" aria-selected="false">
            Rejected <span class="badge bg-danger ms-1">{{ $rejectedProposals->count() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content" id="proposalTabsContent">

    {{-- ALL TAB --}}
    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
        <div class="content-box">
            @include('organizer.proposals._table', ['proposals' => $allProposals])
        </div>
    </div>

    {{-- PENDING TAB --}}
    <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
        <div class="content-box">
            @include('organizer.proposals._table', ['proposals' => $pendingProposals])
        </div>
    </div>

    {{-- APPROVED TAB --}}
    <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
        <div class="content-box">
            @include('organizer.proposals._table', ['proposals' => $approvedProposals])
        </div>
    </div>

    {{-- REJECTED TAB --}}
    <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
        <div class="content-box">
            @include('organizer.proposals._table', ['proposals' => $rejectedProposals])
        </div>
    </div>

</div>

@endsection
