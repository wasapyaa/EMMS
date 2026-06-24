@extends('organizer.layout')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@section('content')
<div class="container mt-4">

    
    <h5 class="fw-bold mb-4">
        <i class="bi bi-plus-circle me-2"></i> Submit Event Proposal
    </h5>

    <form method="POST" action="/organizer/proposals" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label>Event Title</label>
        <input type="text" name="title" class="form-control">
    </div>

    <div class="mb-3">
        <label>Brief Description (Simple summary for list)</label>
        <textarea name="description" class="form-control" rows="2"></textarea>
    </div>

    <div class="mb-3">
        <label>Event Banner (Image)</label>
        <input type="file" name="event_banner" accept="image/*" class="form-control">
        <small class="text-muted">Upload a banner image for the event (optional).</small>
    </div>

    <div class="mb-3">
        <label>Event Details (Long description/tentative/etc.)</label>
        <textarea name="event_details" class="form-control" rows="5" placeholder="Tuliskan tentative program, syarat-syarat, atau maklumat terperinci di sini..."></textarea>
    </div>

    <div class="mb-3">
        <label>Category</label>
        <select name="category" class="form-control" required>
            <option value="">Select Category</option>
            <option value="Sport">Sport</option>
            <option value="Education">Education</option>
            <option value="Entertainment">Entertainment</option>
            <option value="Social">Social</option>
            <option value="Technical">Technical</option>
            <option value="Other">Other</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Telegram Group Link</label>
        <input type="url" name="telegram_link" class="form-control" placeholder="https://t.me/yourgroup">
        <small class="text-muted">Letakkan link group Telegram jika ada (optional).</small>
    </div>

    <div class="mb-3">
        <label>WhatsApp Group Link</label>
        <input type="url" name="whatsapp_link" class="form-control" placeholder="https://chat.whatsapp.com/yourlink">
        <small class="text-muted">Letakkan link group WhatsApp jika ada (optional).</small>
    </div>

    <!-- removed merit_value input -->

    <div class="mb-3">
        <label>Location Name</label>
        <input type="text" name="location_name" class="form-control" required>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label>Latitude</label>
            <input type="number" step="any" name="location_lat" class="form-control" required placeholder="e.g. 3.12345">
        </div>
        <div class="col-md-4 mb-3">
            <label>Longitude</label>
            <input type="number" step="any" name="location_long" class="form-control" required placeholder="e.g. 101.12345">
        </div>
        <div class="col-md-4 mb-3">
            <label>Radiusg(Meters)</label>
            <input type="number" name="radius_meter" class="form-control" required placeholder="e.g. 100">
        </div>
        <a href="https://www.google.com/maps" target="_blank">
            Open Google Maps
        </a>
    </div>
<br>
    <div class="mb-3">
        <label>Start Date & Time</label>
        <input type="datetime-local" name="start_time" class="form-control">
    </div>
    <div class="mb-3">
    <label>End Date & Time</label>
    <input type="datetime-local" name="end_time" class="form-control">
    </div>

    <div class="mb-3">
        <label>Upload Proposal (PDF)</label>
        <input type="file" name="proposal" accept="application/pdf" class="form-control">
    </div>

    <button class="btn btn-primary">Submit Proposal</button>
</form>

</div>
@endsection
