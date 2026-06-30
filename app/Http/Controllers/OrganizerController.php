<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Organizer;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class OrganizerController extends Controller
{
    public function dashboard()
    {
        $organizerId = session('organizer_id');

        $totalProposals = Event::where('o_id', $organizerId)->count();
        $approvedEvents = Event::where('o_id', $organizerId)
                               ->where('status', 'approved')
                               ->count();
        $pendingEvents = Event::where('o_id', $organizerId)
                              ->where('status', 'pending')
                              ->count();
        $rejectedEvents = Event::where('o_id', $organizerId)
                               ->where('status', 'rejected')
                               ->count();

        $recentEvents = Event::where('o_id', $organizerId)
                             ->orderBy('created_at', 'desc')
                             ->take(5)
                             ->get();

        return view('organizer.dashboard', compact(
            'totalProposals',
            'approvedEvents',
            'pendingEvents',
            'rejectedEvents',
            'recentEvents'
        ));
    }

    public function proposalList()
    {
        $organizerId = session('organizer_id');

        $allProposals      = Event::where('o_id', $organizerId)->orderByDesc('created_at')->get();
        $pendingProposals  = $allProposals->where('status', 'pending');
        $approvedProposals = $allProposals->where('status', 'approved');
        $rejectedProposals = $allProposals->where('status', 'rejected');

        return view('organizer.proposals.index', compact(
            'allProposals',
            'pendingProposals',
            'approvedProposals',
            'rejectedProposals'
        ));
    }


public function createProposal()
{
   
    $organizerId = session('organizer_id');

    if (!$organizerId) {
        return redirect('/login')->with('error', 'Please login first.');
    }

    $organizer = Organizer::find($organizerId);

    if (!$organizer) {
        return redirect('/login')->with('error', 'Organizer not found.');
    }

    if ($organizer->status !== 'approved') {
        return redirect('/organizer/dashboard')
            ->with('error', 'Your account is pending approval by Admin HEP.');
    }

    return view('organizer.proposals.create');
}

   public function storeProposal(Request $request)
{
    
$request->validate([
    'title'        => 'required',
    'description'  => 'required',
    'location_name'=> 'required',
    'location_lat' => 'required|numeric',
    'location_long'=> 'required|numeric',
    'radius_meter' => 'required|integer|min:1',
    'start_time'   => 'required|date',
    'end_time'     => 'required|date|after_or_equal:start_time',
    'telegram_link'=> 'nullable|url',
    'whatsapp_link'=> 'nullable|url',
    'proposal'     => 'nullable|file|mimes:pdf|max:5120',
    'category'     => 'required|string|max:50',
    'event_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    'event_details'=> 'nullable|string'
]);

$data = [
    'o_id'          => session('organizer_id'),
    'title'         => $request->title,
    'description'   => $request->description,
    'merit_value'   => 0,
    'location_name' => $request->location_name,
    'start_time'    => Carbon::parse($request->start_time),
    'end_time'      => Carbon::parse($request->end_time),
    'status'        => 'pending',
    'telegram_link' => $request->telegram_link,
    'whatsapp_link' => $request->whatsapp_link,
    'category'      => $request->category,
    'event_details' => $request->event_details,
];

$data += [
    'location_lat'  => $request->location_lat,
    'location_long' => $request->location_long,
    'radius_meter'  => $request->radius_meter,
    'qr_code_token' => \Illuminate\Support\Str::random(32),
];

    if ($request->hasFile('proposal')) {
        // simpan di storage/app/public/proposals
        $path = $request->file('proposal')->store('proposals', 'public');
        $data['proposal_path'] = $path;
    }

    if ($request->hasFile('event_banner')) {
        // simpan di storage/app/public/banners
        $path = $request->file('event_banner')->store('banners', 'public');
        $data['event_banner'] = $path;
    }

    Event::create($data);

    return redirect('/organizer/proposals')
        ->with('success', 'Event proposal submitted successfully.');
}

    public function approvedEvents()
    {
        $organizerId = session('organizer_id');
        $now = now();

        $allEvents = Event::where('o_id', $organizerId)
                       ->where('status', 'approved')
                       ->withCount('attendances')
                       ->orderByDesc('start_time')
                       ->get();

        // 1. Upcoming
        $upcomingEvents = $allEvents->filter(function ($event) use ($now) {
            return \Carbon\Carbon::parse($event->start_time)->gt($now);
        });

        // 2. Ongoing
        $ongoingEvents = $allEvents->filter(function ($event) use ($now) {
            $start = \Carbon\Carbon::parse($event->start_time);
            $end = \Carbon\Carbon::parse($event->end_time);
            return $start->lte($now) && $end->gte($now);
        });

        // 3. Past
        $pastEvents = $allEvents->filter(function ($event) use ($now) {
            return \Carbon\Carbon::parse($event->end_time)->lt($now);
        });

        return view('organizer.events.approved', compact('allEvents', 'upcomingEvents', 'ongoingEvents', 'pastEvents'));
    }

public function profile()
{
    $organizerId = session('organizer_id');

    $organizer = \App\Models\Organizer::findOrFail($organizerId);

    return view('organizer.profile', compact('organizer'));
}

public function updateProfile(Request $request)
{
    $organizerId = session('organizer_id');
    $request->validate([
        'club_name' => 'required|string|max:150',
        'pic_name'  => 'required|string|max:100',
        'email'     => 'required|email|unique:organizers,email,' . $organizerId . ',o_id',
        'phone'     => 'required|digits_between:10,11'
    ]);

    $organizer = Organizer::findOrFail($organizerId);
    $organizer->club_name = $request->club_name;
    $organizer->pic_name  = $request->pic_name;
    $organizer->email     = $request->email;
    $organizer->phone     = $request->phone;
    $organizer->save();

    return back()->with('success', 'Profile updated successfully');
}

public function updatePassword(Request $request)
{
    $request->validate([
        'password' => 'required|min:6'
    ]);

    $organizer = Organizer::findOrFail(session('organizer_id'));
    $organizer->pass_hash = Hash::make($request->password);
    $organizer->save();

    return back()->with('success', 'Password updated successfully');
}
    
public function showProposal($id)
{
    $organizerId = session('organizer_id');

    $proposal = Event::where('e_id', $id)
    ->where('o_id', $organizerId)
    ->firstOrFail();

    return view('organizer.proposals.show', compact('proposal'));
}

public function editProposal($id)
{
    $organizerId = session('organizer_id');

    $proposal = Event::where('e_id', $id)
        ->where('o_id', $organizerId)
        ->firstOrFail();

    // Block edit if event has ended
    if (Carbon::parse($proposal->end_time)->isPast()) {
        return redirect('/organizer/proposals')->with('error', 'Event sudah tamat, tidak boleh diedit.');
    }

    return view('organizer.proposals.edit', compact('proposal'));
}

public function updateProposal(Request $request, $id)
{
    $organizerId = session('organizer_id');

    $proposal = Event::where('e_id', $id)
        ->where('o_id', $organizerId)
        ->firstOrFail();

    // Block update if event has ended
    if (Carbon::parse($proposal->end_time)->isPast()) {
        return back()->with('error', 'Event has ended and can no longer be edited.');
    }

    $isApproved = $proposal->status === 'approved';

    $request->validate([
        'title'        => 'required',
        'description'  => 'required',
        'location_name'=> 'required',
        'location_lat' => $isApproved ? 'sometimes' : 'required|numeric',
        'location_long'=> $isApproved ? 'sometimes' : 'required|numeric',
        'radius_meter' => $isApproved ? 'sometimes' : 'required|integer|min:1',
        'start_time'   => $isApproved ? 'sometimes' : 'required|date',
        'end_time'     => $isApproved ? 'sometimes' : 'required|date|after_or_equal:start_time',
        'telegram_link'=> 'nullable|url',
        'whatsapp_link'=> 'nullable|url',
        'proposal'     => 'nullable|file|mimes:pdf|max:5120',
        'category'     => $isApproved ? 'sometimes' : 'required|string|max:50',
        'event_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'event_details'=> 'nullable|string'
    ]);

    // Always editable fields
    $proposal->title        = $request->title;
    $proposal->description  = $request->description;
    $proposal->location_name= $request->location_name;
    $proposal->telegram_link= $request->telegram_link;
    $proposal->whatsapp_link= $request->whatsapp_link;
    $proposal->event_details= $request->event_details;

    // Locked fields — only update if NOT approved
    if (!$isApproved) {
        $proposal->location_lat  = $request->location_lat;
        $proposal->location_long = $request->location_long;
        $proposal->radius_meter  = $request->radius_meter;
        $proposal->start_time    = \Carbon\Carbon::parse($request->start_time);
        $proposal->end_time      = \Carbon\Carbon::parse($request->end_time);
        $proposal->category      = $request->category;
    }

    // Handle replacement PDF (locked if approved)
    if (!$isApproved && $request->hasFile('proposal')) {
        if ($proposal->proposal_path) {
            Storage::disk('public')->delete($proposal->proposal_path);
        }
        $path = $request->file('proposal')->store('proposals', 'public');
        $proposal->proposal_path = $path;
    }

    // Handle replacement Banner (always allowed)
    if ($request->hasFile('event_banner')) {
        if ($proposal->event_banner) {
            Storage::disk('public')->delete($proposal->event_banner);
        }
        $path = $request->file('event_banner')->store('banners', 'public');
        $proposal->event_banner = $path;
    }

    $proposal->save();

    return redirect('/organizer/proposals')->with('success', 'Proposal updated successfully.');
}

public function showEvent($id)
{
    $organizerId = session('organizer_id');

    $event = Event::where('e_id', $id)
        ->where('o_id', $organizerId)
        ->where('status', 'approved')
        ->firstOrFail();

    $attendances = \App\Models\Attendance::with('student')
        ->where('e_id', $id)
        ->orderBy('scan_time', 'asc')
        ->get();

    return view('organizer.events.show', compact('event', 'attendances'));
}

    public function downloadQr($id)
    {
        $organizerId = session('organizer_id');

        $event = Event::where('e_id', $id)
            ->where('o_id', $organizerId)
            ->where('status', 'approved')
            ->firstOrFail();

        if (!$event->qr_code_token) {
            abort(404, 'QR code not available for this event.');
        }

        // Generate QR code as PNG image bytes
        $qrPngBytes = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->generate($event->qr_code_token);

        // Load QR image into GD
        $qrImg = imagecreatefromstring($qrPngBytes);
        if (!$qrImg) {
            abort(500, 'Failed to process QR code image.');
        }

        $qrWidth = imagesx($qrImg);
        $qrHeight = imagesy($qrImg);

        // Text wrapping and dimensions calculation
        $text = $event->title;
        $lines = explode("\n", wordwrap($text, 28, "\n"));
        $lineHeight = 18;
        $textPadding = 20 + (count($lines) * $lineHeight);

        $newWidth = $qrWidth;
        $newHeight = $qrHeight + $textPadding;

        // Create canvas
        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $black = imagecolorallocate($canvas, 0, 0, 0);

        imagefill($canvas, 0, 0, $white);
        imagecopy($canvas, $qrImg, 0, 0, 0, 0, $qrWidth, $qrHeight);

        // Draw centered wrapped text
        $fontSize = 4; // Built-in GD font size
        $fontWidth = imagefontwidth($fontSize);
        foreach ($lines as $index => $line) {
            $line = trim($line);
            $textWidth = strlen($line) * $fontWidth;
            $x = ($newWidth - $textWidth) / 2;
            $y = $qrHeight + 5 + ($index * $lineHeight);
            imagestring($canvas, $fontSize, $x, $y, $line, $black);
        }

        // Capture image bytes
        ob_start();
        imagejpeg($canvas, null, 90);
        $jpgBytes = ob_get_clean();

        // Clean up resources
        imagedestroy($qrImg);
        imagedestroy($canvas);

        $safeName = \Illuminate\Support\Str::slug($event->title) . '_qr.jpg';

        return response($jpgBytes, 200)
            ->header('Content-Type', 'image/jpeg')
            ->header('Content-Disposition', 'attachment; filename="' . $safeName . '"');
    }


public function deleteProposal($id)
{
    $organizerId = session('organizer_id');
    $proposal = Event::where('e_id', $id)->where('o_id', $organizerId)->first();

    if (!$proposal) {
        return redirect('/organizer/proposals')->with('error', 'Proposal not found.');
    }

    if ($proposal->status == 'approved') {
        return redirect('/organizer/proposals')->with('error', 'Approved proposals cannot be deleted.');
    }

    // Delete the proposal file if exists
    if ($proposal->proposal_path) {
        \Storage::disk('public')->delete($proposal->proposal_path);
    }

    $proposal->delete();

    return redirect('/organizer/proposals')->with('success', 'Proposal deleted successfully.');
}
    public function ideas(Request $request)
    {
        $organizerId = session('organizer_id');
        if (!$organizerId) return redirect('/login');

        $sort = $request->query('sort', 'trending');
        $cutoffDays = null;
        
        if ($sort === 'trending') {
            $cutoffDays = 7;
        } elseif ($sort === 'top_month') {
            $cutoffDays = 30;
        }

        $ideasQuery = \App\Models\Idea::with('student:s_id,name')
            ->addSelect([
                '*',
                'net_score' => \Illuminate\Support\Facades\DB::table('idea_votes')
                    ->selectRaw("COALESCE(SUM(CASE WHEN type = 'like' THEN 1 WHEN type = 'dislike' THEN -1 ELSE 0 END), 0)")
                    ->whereColumn('idea_votes.idea_id', 'ideas.id'),
                'reports_count' => \Illuminate\Support\Facades\DB::table('idea_reports')
                    ->selectRaw('count(*)')
                    ->whereColumn('idea_reports.idea_id', 'ideas.id')
            ]);

        if ($sort === 'new') {
            $ideasQuery->orderByDesc('created_at');
        } elseif ($cutoffDays !== null) {
            $cutoff = now()->subDays($cutoffDays);
            $ideasQuery->addSelect([
                'window_net_score' => \Illuminate\Support\Facades\DB::table('idea_votes')
                    ->selectRaw("COALESCE(SUM(CASE WHEN type = 'like' THEN 1 WHEN type = 'dislike' THEN -1 ELSE 0 END), 0)")
                    ->whereColumn('idea_votes.idea_id', 'ideas.id')
                    ->where('idea_votes.created_at', '>=', $cutoff)
            ])->orderByDesc('window_net_score')->orderByDesc('created_at');
        } else {
            // top_all
            $ideasQuery->orderByDesc('net_score')->orderByDesc('created_at');
        }

        $ideas = $ideasQuery->get();

        return view('organizer.ideas.index', compact('ideas', 'sort'));
    }
}
