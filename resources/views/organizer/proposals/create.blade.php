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
            <label class="form-label fw-semibold">Latitude</label>
            <input type="number" step="any" id="location_lat" name="location_lat" class="form-control" required placeholder="e.g. 3.12345">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label fw-semibold">Longitude</label>
            <input type="number" step="any" id="location_long" name="location_long" class="form-control" required placeholder="e.g. 101.12345">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label fw-semibold">Radius (Meters)</label>
            <input type="number" id="radius_meter" name="radius_meter" class="form-control" required placeholder="e.g. 100" value="100">
        </div>
    </div>

    <!-- Leaflet Map Integration -->
    <div class="mb-3">
        <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
            <span>Select Location on Map</span>
            <small class="text-primary"><i class="bi bi-info-circle me-1"></i>Click on the map to pin location</small>
        </label>
        <div id="map" style="height: 350px; border-radius: 8px; border: 1px solid #ced4da; z-index: 1;"></div>
        <small class="text-muted mt-1 d-block">
            You can click anywhere on the map to automatically retrieve the latitude and longitude. The red circle displays the attendance radius.
        </small>
    </div>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Default center: UiTM Jasin Melaka
            var defaultLat = 2.2225;
            var defaultLng = 102.4533;
            var defaultZoom = 15;

            var map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);

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
        });
    </script>
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
