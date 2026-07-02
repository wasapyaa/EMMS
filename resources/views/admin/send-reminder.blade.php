@extends('admin.layout')

@section('content')
<div class="content-box">
    <h5 class="fw-bold mb-3">
        <i class="bi bi-envelope-paper me-2"></i> Send Merit & Hostel Reminder
    </h5>

    <p class="text-muted">
        This will send an email to <strong>all active students in the current semester</strong> about their current
        merit ranking and a reminder to compete for hostel accommodation next semester.
    </p>

    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-1"></i>
        Please confirm before sending. This action sends real emails.
    </div>

    <form method="POST" action="/admin/send-reminder">
        @csrf
        <button class="btn btn-primary">
            <i class="bi bi-send me-1"></i> Send Email to All Active Students
        </button>
    </form>

    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
</div>
@endsection
