@extends('admin.layout')

@section('content')

<style>
    .edit-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .edit-card .card-header {
        background: linear-gradient(135deg, #7c3aed, #5b21b6);
        padding: 18px 24px;
    }
    .section-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #6c757d;
        margin-bottom: 6px;
    }
    .emergency-banner {
        background: linear-gradient(135deg, #fff7ed, #fef3c7);
        border-left: 4px solid #f59e0b;
        border-radius: 10px;
        padding: 14px 18px;
    }
</style>

<div class="container-fluid py-3">

    {{-- BREADCRUMB --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/admin/events" class="text-decoration-none">
                    <i class="bi bi-calendar-event me-1"></i>Manage Events
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="/admin/events/{{ $event->e_id }}" class="text-decoration-none">{{ Str::limit($event->title, 30) }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Emergency Edit</li>
        </ol>
    </nav>

    {{-- EMERGENCY WARNING BANNER --}}
    <div class="emergency-banner mb-4">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
            <div>
                <p class="fw-bold mb-0" style="color:#92400e;">Emergency Edit — Admin Override</p>
                <p class="mb-0 small" style="color:#b45309;">Changes made here will override the organizer's submission. Use only when necessary.</p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3">
            <i class="bi bi-exclamation-circle me-1"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="/admin/events/{{ $event->e_id }}/edit" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card edit-card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square text-white fs-5"></i>
                <h5 class="mb-0 text-white">Edit Event Details</h5>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">

                    {{-- LEFT COLUMN --}}
                    <div class="col-md-8">

                        {{-- Event Name --}}
                        <div class="mb-3">
                            <label class="section-label"><i class="bi bi-tag me-1"></i>Event Name</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $event->title) }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Brief Description --}}
                        <div class="mb-3">
                            <label class="section-label"><i class="bi bi-file-text me-1"></i>Brief Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                      rows="3">{{ old('description', $event->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Event Details --}}
                        <div class="mb-3">
                            <label class="section-label"><i class="bi bi-card-text me-1"></i>Event Details (Tentative / Info)</label>
                            <textarea name="event_details" class="form-control @error('event_details') is-invalid @enderror"
                                      rows="5" placeholder="Enter full event details, schedule, etc.">{{ old('event_details', $event->event_details) }}</textarea>
                            @error('event_details') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Category --}}
                        <div class="mb-3">
                            <label class="section-label"><i class="bi bi-tags me-1"></i>Category</label>
                            <input type="text" name="category" class="form-control @error('category') is-invalid @enderror"
                                   value="{{ old('category', $event->category) }}">
                            @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Date & Time --}}
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="section-label"><i class="bi bi-clock me-1"></i>Start Time</label>
                                <input type="datetime-local" name="start_time"
                                       class="form-control @error('start_time') is-invalid @enderror"
                                       value="{{ old('start_time', \Carbon\Carbon::parse($event->start_time)->format('Y-m-d\TH:i')) }}" required>
                                @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="section-label"><i class="bi bi-clock-history me-1"></i>End Time</label>
                                <input type="datetime-local" name="end_time"
                                       class="form-control @error('end_time') is-invalid @enderror"
                                       value="{{ old('end_time', \Carbon\Carbon::parse($event->end_time)->format('Y-m-d\TH:i')) }}" required>
                                @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Social Links --}}
                        <div class="row g-3 mt-1">
                            <div class="col-sm-6">
                                <label class="section-label"><i class="bi bi-telegram me-1"></i>Telegram Link</label>
                                <input type="url" name="telegram_link" class="form-control @error('telegram_link') is-invalid @enderror"
                                       value="{{ old('telegram_link', $event->telegram_link) }}" placeholder="https://t.me/...">
                                @error('telegram_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="section-label"><i class="bi bi-whatsapp me-1"></i>WhatsApp Link</label>
                                <input type="url" name="whatsapp_link" class="form-control @error('whatsapp_link') is-invalid @enderror"
                                       value="{{ old('whatsapp_link', $event->whatsapp_link) }}" placeholder="https://wa.me/...">
                                @error('whatsapp_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT COLUMN --}}
                    <div class="col-md-4">

                        {{-- Merit Value --}}
                        <div class="mb-4 p-3 rounded-3" style="background:#f0fdf4;">
                            <label class="section-label"><i class="bi bi-star-fill text-warning me-1"></i>Merit Points</label>
                            <input type="number" name="merit_value"
                                   class="form-control @error('merit_value') is-invalid @enderror"
                                   min="0" max="100"
                                   value="{{ old('merit_value', $event->merit_value) }}"
                                   placeholder="e.g. 5">
                            @error('merit_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Points awarded to attending students.</div>
                        </div>

                        {{-- Location --}}
                        <div class="mb-3 p-3 rounded-3 border">
                            <p class="section-label mb-2"><i class="bi bi-geo-alt me-1"></i>Location</p>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Venue Name</label>
                                <input type="text" name="location_name" class="form-control @error('location_name') is-invalid @enderror"
                                       value="{{ old('location_name', $event->location_name) }}" required>
                                @error('location_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small fw-semibold">Latitude</label>
                                    <input type="number" step="any" name="location_lat"
                                           class="form-control @error('location_lat') is-invalid @enderror"
                                           value="{{ old('location_lat', $event->location_lat) }}" required>
                                    @error('location_lat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-semibold">Longitude</label>
                                    <input type="number" step="any" name="location_long"
                                           class="form-control @error('location_long') is-invalid @enderror"
                                           value="{{ old('location_long', $event->location_long) }}" required>
                                    @error('location_long') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="form-label small fw-semibold">Scan Radius (meters)</label>
                                <input type="number" name="radius_meter"
                                       class="form-control @error('radius_meter') is-invalid @enderror"
                                       min="1" value="{{ old('radius_meter', $event->radius_meter) }}" required>
                                @error('radius_meter') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Event Banner --}}
                        <div class="mb-3">
                            <label class="section-label"><i class="bi bi-image me-1"></i>Event Banner</label>
                            @if($event->event_banner)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/'.$event->event_banner) }}"
                                         alt="Current Banner" class="img-fluid rounded-3 shadow-sm" style="max-height:150px; width:100%; object-fit:cover;">
                                    <p class="text-muted small mt-1">Current banner — upload new to replace.</p>
                                </div>
                            @endif
                            <input type="file" name="event_banner" class="form-control @error('event_banner') is-invalid @enderror"
                                   accept="image/*">
                            @error('event_banner') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>
            </div>

            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3 px-4">
                <a href="/admin/events/{{ $event->e_id }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-warning fw-semibold px-4">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
            </div>
        </div>

    </form>
</div>

@endsection
