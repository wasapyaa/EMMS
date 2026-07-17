@extends('organizer.layout')

@section('content')
<div class="container mt-4">
    <h4>Edit Proposal</h4>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($proposal->status == 'approved')
        <div class="alert alert-warning d-flex align-items-center gap-2 rounded-3 mb-4" style="border-left: 4px solid #f59e0b;">
            <i class="bi bi-lock-fill text-warning fs-5"></i>
            <div>
                <strong>Some fields are locked after approval.</strong>
                Fields marked with <i class="bi bi-lock-fill text-warning"></i> cannot be changed.
                Contact Admin HEP for changes to locked fields.
            </div>
        </div>
    @endif

    <form method="POST" action="{{ url('/organizer/proposals/'.$proposal->e_id.'/update') }}" enctype="multipart/form-data">
        @csrf

        {{-- Title --}}
        <div class="mb-3">
            <label>Event Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $proposal->title) }}" required>
        </div>

        {{-- Brief Description --}}
        <div class="mb-3">
            <label>Brief Description (Simple summary for list)</label>
            <textarea name="description" class="form-control" rows="2" required>{{ old('description', $proposal->description) }}</textarea>
        </div>

        {{-- Event Banner --}}
        <div class="mb-3">
            <label>Event Banner (Image)</label>
            <input type="file" name="event_banner" accept="image/*" class="form-control">
            <small class="text-muted">Upload a banner image to replace the current one (optional).</small>
            @if($proposal->event_banner)
                <div class="mt-2">
                    <p class="mb-1 text-muted small">Current Banner:</p>
                    <img src="{{ asset('storage/'.$proposal->event_banner) }}" alt="Event Banner" style="max-height: 150px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1)">
                </div>
            @endif
        </div>

        {{-- Event Details --}}
        <div class="mb-3">
            <label>Event Details (Long description / tentative / etc.)</label>
            <textarea name="event_details" class="form-control" rows="5" placeholder="Enter program schedule, requirements, or detailed info here...">{{ old('event_details', $proposal->event_details) }}</textarea>
        </div>

        {{-- Category — LOCKED if approved --}}
        <div class="mb-3">
            <label>
                Category
                @if($proposal->status == 'approved')
                    <i class="bi bi-lock-fill text-warning ms-1" title="Locked after approval"></i>
                @endif
            </label>
            @if($proposal->status == 'approved')
                <input type="text" class="form-control bg-light text-muted" value="{{ $proposal->category }}" disabled>
                <small class="text-muted">Category cannot be changed after approval.</small>
            @else
                <select name="category" class="form-control" required>
                    <option value="">Select Category</option>
                    <option value="Sport" {{ old('category', $proposal->category) == 'Sport' ? 'selected' : '' }}>Sport</option>
                    <option value="Education" {{ old('category', $proposal->category) == 'Education' ? 'selected' : '' }}>Education</option>
                    <option value="Entertainment" {{ old('category', $proposal->category) == 'Entertainment' ? 'selected' : '' }}>Entertainment</option>
                    <option value="Social" {{ old('category', $proposal->category) == 'Social' ? 'selected' : '' }}>Social</option>
                    <option value="Technical" {{ old('category', $proposal->category) == 'Technical' ? 'selected' : '' }}>Technical</option>
                    <option value="Other" {{ old('category', $proposal->category) == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            @endif
        </div>

        {{-- Social Links --}}
        <div class="mb-3">
            <label>Telegram Group Link</label>
            <input type="url" name="telegram_link" class="form-control" placeholder="https://t.me/yourgroup" value="{{ old('telegram_link', $proposal->telegram_link) }}">
            <small class="text-muted">Optional.</small>
        </div>

        <div class="mb-3">
            <label>WhatsApp Group Link</label>
            <input type="url" name="whatsapp_link" class="form-control" placeholder="https://chat.whatsapp.com/yourlink" value="{{ old('whatsapp_link', $proposal->whatsapp_link) }}">
            <small class="text-muted">Optional.</small>
        </div>

        {{-- Location Name (always editable) --}}
        <div class="mb-3">
            <label>Location Name</label>
            <input type="text" name="location_name" class="form-control" value="{{ old('location_name', $proposal->location_name) }}" required>
        </div>

        {{-- GPS Coordinates & Radius — LOCKED if approved --}}
        <div class="row">
            <div class="col-md-4 mb-3">
                <label>
                    Latitude
                    @if($proposal->status == 'approved')
                        <i class="bi bi-lock-fill text-warning ms-1" title="Locked after approval"></i>
                    @endif
                </label>
                @if($proposal->status == 'approved')
                    <input type="text" id="location_lat" class="form-control bg-light text-muted" value="{{ $proposal->location_lat }}" disabled>
                @else
                    <input type="number" step="any" id="location_lat" name="location_lat" class="form-control" value="{{ old('location_lat', $proposal->location_lat) }}" required>
                @endif
            </div>
            <div class="col-md-4 mb-3">
                <label>
                    Longitude
                    @if($proposal->status == 'approved')
                        <i class="bi bi-lock-fill text-warning ms-1" title="Locked after approval"></i>
                    @endif
                </label>
                @if($proposal->status == 'approved')
                    <input type="text" id="location_long" class="form-control bg-light text-muted" value="{{ $proposal->location_long }}" disabled>
                @else
                    <input type="number" step="any" id="location_long" name="location_long" class="form-control" value="{{ old('location_long', $proposal->location_long) }}" required>
                @endif
            </div>
            <div class="col-md-4 mb-3">
                <label>
                    Radius (Meters)
                    @if($proposal->status == 'approved')
                        <i class="bi bi-lock-fill text-warning ms-1" title="Locked after approval"></i>
                    @endif
                </label>
                @if($proposal->status == 'approved')
                    <input type="text" id="radius_meter" class="form-control bg-light text-muted" value="{{ $proposal->radius_meter }}" disabled>
                    <small class="text-muted">Location settings cannot be changed after approval.</small>
                @else
                    <input type="number" id="radius_meter" name="radius_meter" class="form-control" value="{{ old('radius_meter', $proposal->radius_meter) }}" required>
                @endif
            </div>
        </div>

        <!-- Leaflet Map Integration -->
        <div class="mb-3">
            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                <span>Location on Map</span>
                @if($proposal->status != 'approved')
                    <small class="text-primary"><i class="bi bi-info-circle me-1"></i>Click on the map to pin location</small>
                @else
                    <small class="text-muted"><i class="bi bi-lock-fill me-1"></i>Map is read-only after approval</small>
                @endif
            </label>
            <div id="map" style="height: 350px; border-radius: 8px; border: 1px solid #ced4da; z-index: 1;"></div>
            <small class="text-muted mt-1 d-block">
                @if($proposal->status != 'approved')
                    You can click anywhere on the map to automatically retrieve the latitude and longitude. The red circle displays the attendance radius.
                @else
                    This is the approved attendance location.
                @endif
            </small>
        </div>

        <!-- Leaflet CSS & JS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var initialLat = parseFloat("{{ $proposal->location_lat }}") || 2.2225;
                var initialLng = parseFloat("{{ $proposal->location_long }}") || 102.4533;
                var initialRadius = parseInt("{{ $proposal->radius_meter }}") || 100;
                var isApproved = "{{ $proposal->status == 'approved' }}" === "1";

                var map = L.map('map').setView([initialLat, initialLng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                var marker;
                var circle;

                function updateMap(lat, lng, radius) {
                    if (marker) map.removeLayer(marker);
                    if (circle) map.removeLayer(circle);

                    marker = L.marker([lat, lng]).addTo(map);
                    circle = L.circle([lat, lng], {
                        color: '#dc3545',
                        fillColor: '#dc3545',
                        fillOpacity: 0.15,
                        radius: radius
                    }).addTo(map);

                    map.setView([lat, lng]);
                }

                // Initialize marker
                updateMap(initialLat, initialLng, initialRadius);

                if (!isApproved) {
                    // Click Map event
                    map.on('click', function(e) {
                        var lat = e.latlng.lat.toFixed(6);
                        var lng = e.latlng.lng.toFixed(6);
                        var radius = parseInt(document.getElementById('radius_meter').value) || 100;

                        document.getElementById('location_lat').value = lat;
                        document.getElementById('location_long').value = lng;

                        updateMap(lat, lng, radius);
                    });

                    // Input fields change event
                    function handleInputChange() {
                        var lat = parseFloat(document.getElementById('location_lat').value);
                        var lng = parseFloat(document.getElementById('location_long').value);
                        var radius = parseInt(document.getElementById('radius_meter').value) || 100;

                        if (!isNaN(lat) && !isNaN(lng)) {
                            updateMap(lat, lng, radius);
                        }
                    }

                    document.getElementById('location_lat').addEventListener('input', handleInputChange);
                    document.getElementById('location_long').addEventListener('input', handleInputChange);
                    document.getElementById('radius_meter').addEventListener('input', handleInputChange);
                }
            });
        </script>

        {{-- Date & Time — LOCKED if approved --}}
        <div class="mb-3">
            <label>
                Start Time
                @if($proposal->status == 'approved')
                    <i class="bi bi-lock-fill text-warning ms-1" title="Locked after approval"></i>
                @endif
            </label>
            @if($proposal->status == 'approved')
                <input type="text" class="form-control bg-light text-muted"
                       value="{{ \Carbon\Carbon::parse($proposal->start_time)->format('d M Y, g:i A') }}" disabled>
            @else
                <input type="datetime-local" name="start_time" class="form-control"
                       value="{{ \Carbon\Carbon::parse($proposal->start_time)->format('Y-m-d\TH:i') }}" required>
            @endif
        </div>

        <div class="mb-3">
            <label>
                End Time
                @if($proposal->status == 'approved')
                    <i class="bi bi-lock-fill text-warning ms-1" title="Locked after approval"></i>
                @endif
            </label>
            @if($proposal->status == 'approved')
                <input type="text" class="form-control bg-light text-muted"
                       value="{{ \Carbon\Carbon::parse($proposal->end_time)->format('d M Y, g:i A') }}" disabled>
                <small class="text-muted">Date & time cannot be changed after approval. Contact Admin HEP if changes are needed.</small>
            @else
                <input type="datetime-local" name="end_time" class="form-control"
                       value="{{ \Carbon\Carbon::parse($proposal->end_time)->format('Y-m-d\TH:i') }}" required>
            @endif
        </div>

        {{-- Proposal PDF — LOCKED if approved --}}
        <div class="mb-3">
            <label>
                Replace Proposal (PDF)
                @if($proposal->status == 'approved')
                    <i class="bi bi-lock-fill text-warning ms-1" title="Locked after approval"></i>
                @endif
            </label>
            @if($proposal->status == 'approved')
                <input type="file" class="form-control bg-light" disabled>
                <small class="text-muted">Proposal PDF cannot be replaced after approval.</small>
            @else
                <input type="file" name="proposal" accept="application/pdf" class="form-control">
            @endif
            @if($proposal->proposal_path)
                <p class="mt-2">Current file: <a href="{{ asset('storage/'.$proposal->proposal_path) }}" target="_blank">View</a></p>
            @endif
        </div>

        <button class="btn btn-primary">Save Changes</button>
        <a href="/organizer/proposals" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection