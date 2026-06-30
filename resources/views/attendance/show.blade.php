<!DOCTYPE html>
<html>
<head>
    <title>Event Attendance</title>
    <link rel="icon" type="image/png" href="{{ asset('images/emms-logo.png') }}">
</head>
<body>

<h2>{{ $event->title }}</h2>

<p><strong>Date:</strong> {{ $event->start_time }}</p>
<p><strong>Location:</strong> {{ $event->location_name }}</p>

<form method="POST" action="/attendance/{{ $event->qr_code_token }}">
    @csrf
    <button type="submit">Confirm Attendance</button>
</form>

</body>
</html>
