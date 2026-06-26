@extends('student.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-calendar-event me-2"></i> Available Events</h5>
    <form method="GET" action="{{ url('/student/events') }}" class="d-flex gap-2">
        <input
            type="text"
            name="search"
            class="form-control form-control-sm"
            placeholder="Search events..."
            value="{{ request('search') }}"
            style="width: 220px;"
        >
        <button class="btn btn-primary btn-sm px-3">
            <i class="bi bi-search"></i>
        </button>
        @if(request('search'))
            <a href="{{ url('/student/events') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
        @endif
    </form>
</div>

<!-- EVENT CARDS -->
<div class="row g-4">

    @forelse($events as $event)
    <div class="col-md-4">

        <div class="card shadow-sm border-0 h-100" style="border-radius:15px; overflow:hidden; cursor:pointer;"
            data-bs-toggle="modal"
            data-bs-target="#eventModal"
            data-title="{{ $event->title }}"
            data-date="{{ \Carbon\Carbon::parse($event->start_time)->format('d/m/Y h:i A') }}"
            data-enddate="{{ $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('d/m/Y h:i A') : '' }}"
            data-location="{{ $event->location_name ?? 'TBA' }}"
            data-merit="{{ $event->merit_value }}"
            data-description="{{ $event->description ?? '' }}"
            data-details="{{ $event->event_details ?? '' }}"
            data-telegram="{{ $event->telegram_link ?? '' }}"
            data-whatsapp="{{ $event->whatsapp_link ?? '' }}"
            data-category="{{ $event->category ?? '' }}"
            data-banner="{{ $event->event_banner ? asset('storage/' . $event->event_banner) : '' }}"
        >
            {{-- Banner Image --}}
            @if($event->event_banner)
                <img src="{{ asset('storage/' . $event->event_banner) }}"
                    alt="{{ $event->title }}"
                    style="width:100%; height:160px; object-fit:cover;"
                    onerror="this.parentElement.querySelector('.banner-placeholder').style.display='flex'; this.style.display='none';">
                <div class="banner-placeholder" style="display:none; width:100%; height:160px; background:linear-gradient(135deg,#3f51b5,#6fb1e8); align-items:center; justify-content:center;">
                    <i class="bi bi-calendar-event text-white" style="font-size:2.5rem;"></i>
                </div>
            @else
                <div style="width:100%; height:160px; background:linear-gradient(135deg,#3f51b5,#6fb1e8); display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-calendar-event text-white" style="font-size:2.5rem;"></i>
                </div>
            @endif

            <div class="card-body d-flex flex-column">
                {{-- Category --}}
                @if($event->category)
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill mb-2" style="width:fit-content;">
                        {{ $event->category }}
                    </span>
                @endif

                {{-- Title + Merit --}}
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold mb-0 me-2">{{ $event->title }}</h6>
                    <span class="badge bg-primary flex-shrink-0">
                        {{ $event->merit_value }} pts
                    </span>
                </div>

                {{-- Date --}}
                <p class="text-muted small mb-1">
                    <i class="bi bi-calendar-date me-1"></i>
                    {{ \Carbon\Carbon::parse($event->start_time)->format('d M Y, h:i A') }}
                </p>

                {{-- Location --}}
                <p class="text-muted small mb-0">
                    <i class="bi bi-geo-alt me-1"></i>
                    {{ $event->location_name ?? 'TBA' }}
                </p>

                <div class="mt-auto pt-3">
                    <div class="btn btn-primary btn-sm w-100 rounded-3">
                        <i class="bi bi-eye me-1"></i> View Details
                    </div>
                </div>
            </div>
        </div>

    </div>
    @empty
    <div class="col-12 text-center text-muted py-5">
        <i class="bi bi-calendar-x fs-1 mb-3 d-block text-muted" style="opacity:0.4;"></i>
        No events available.
    </div>
    @endforelse

</div>

<!-- EVENT DETAIL MODAL -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-4" style="overflow:hidden;">

            {{-- Banner --}}
            <div id="modalBannerWrap" style="height:220px; overflow:hidden; background:linear-gradient(135deg,#3f51b5,#6fb1e8); display:flex; align-items:center; justify-content:center; position:relative;">
                <img id="modalBannerImg" src="" alt="" style="width:100%; height:220px; object-fit:cover; display:none;">
                <i id="modalBannerIcon" class="bi bi-calendar-event text-white" style="font-size:3rem;"></i>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="position:absolute; top:15px; right:15px;"></button>
            </div>

            <div class="modal-body p-4">

                {{-- Category --}}
                <span id="modalCategory" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill mb-3" style="display:none;"></span>

                {{-- Title --}}
                <h4 class="fw-bold mb-3" id="modalTitle"></h4>

                {{-- Info Grid --}}
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-light">
                            <div class="text-muted small mb-1"><i class="bi bi-star-fill text-warning me-1"></i> Merit Points</div>
                            <div class="fw-bold" id="modalMerit"></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-light">
                            <div class="text-muted small mb-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Location</div>
                            <div class="fw-semibold" id="modalLocation"></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-light">
                            <div class="text-muted small mb-1"><i class="bi bi-calendar-event me-1 text-primary"></i> Start Date</div>
                            <div class="fw-semibold" id="modalDate"></div>
                        </div>
                    </div>
                    <div class="col-sm-6" id="modalEndDateWrap" style="display:none;">
                        <div class="p-3 rounded-3 bg-light">
                            <div class="text-muted small mb-1"><i class="bi bi-calendar-check me-1 text-success"></i> End Date</div>
                            <div class="fw-semibold" id="modalEndDate"></div>
                        </div>
                    </div>
                </div>

                {{-- Event Details --}}
                <div id="modalDetailsSection" style="display:none;">
                    <h6 class="fw-bold mb-2">Event Details</h6>
                    <div class="p-3 rounded-3 bg-light mb-4">
                        <p id="modalDetails" class="mb-0 text-dark" style="line-height:1.7;"></p>
                    </div>
                </div>

                {{-- Description --}}
                <div id="modalDescSection" style="display:none;">
                    <h6 class="fw-bold mb-2">Description</h6>
                    <div class="p-3 rounded-3 bg-light mb-4">
                        <p id="modalDescription" class="mb-0 text-muted" style="line-height:1.7;"></p>
                    </div>
                </div>

                {{-- Social Links --}}
                <div id="modalSocialLinks" class="d-flex gap-2 flex-wrap"></div>

            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('eventModal').addEventListener('show.bs.modal', function (event) {
    let btn = event.relatedTarget;

    const title       = btn.getAttribute('data-title') || '-';
    const date        = btn.getAttribute('data-date') || '-';
    const enddate     = btn.getAttribute('data-enddate') || '';
    const location    = btn.getAttribute('data-location') || 'TBA';
    const merit       = btn.getAttribute('data-merit') || '0';
    const description = btn.getAttribute('data-description') || '';
    const details     = btn.getAttribute('data-details') || '';
    const telegram    = btn.getAttribute('data-telegram') || '';
    const whatsapp    = btn.getAttribute('data-whatsapp') || '';
    const category    = btn.getAttribute('data-category') || '';
    const banner      = btn.getAttribute('data-banner') || '';

    document.getElementById('modalTitle').textContent    = title;
    document.getElementById('modalDate').textContent     = date;
    document.getElementById('modalLocation').textContent = location;
    document.getElementById('modalMerit').textContent    = merit + ' Points';

    // End date
    const endWrap = document.getElementById('modalEndDateWrap');
    if (enddate) {
        endWrap.style.display = '';
        document.getElementById('modalEndDate').textContent = enddate;
    } else {
        endWrap.style.display = 'none';
    }

    // Category
    const catEl = document.getElementById('modalCategory');
    if (category) {
        catEl.style.display = '';
        catEl.textContent = category;
    } else {
        catEl.style.display = 'none';
    }

    // Banner
    const img  = document.getElementById('modalBannerImg');
    const icon = document.getElementById('modalBannerIcon');
    if (banner) {
        img.src = banner;
        img.style.display = '';
        icon.style.display = 'none';
        img.onerror = function() { img.style.display='none'; icon.style.display=''; };
    } else {
        img.style.display = 'none';
        icon.style.display = '';
    }

    // Event Details
    const detailsSection = document.getElementById('modalDetailsSection');
    if (details.trim()) {
        detailsSection.style.display = '';
        document.getElementById('modalDetails').textContent = details;
    } else {
        detailsSection.style.display = 'none';
    }

    // Description
    const descSection = document.getElementById('modalDescSection');
    if (description.trim()) {
        descSection.style.display = '';
        document.getElementById('modalDescription').textContent = description;
    } else {
        descSection.style.display = 'none';
    }

    // Social links
    const social = document.getElementById('modalSocialLinks');
    social.innerHTML = '';
    if (telegram) {
        social.innerHTML += `<a href="${telegram}" target="_blank" class="btn btn-sm btn-primary rounded-3 px-3">
            <i class="bi bi-telegram me-1"></i> Join Telegram
        </a>`;
    }
    if (whatsapp) {
        social.innerHTML += `<a href="${whatsapp}" target="_blank" class="btn btn-sm rounded-3 px-3 text-white" style="background:#25D366">
            <i class="bi bi-whatsapp me-1"></i> Join WhatsApp
        </a>`;
    }
});
</script>

@endsection
