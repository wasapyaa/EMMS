<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\Organizer;
use App\Models\Event;
use App\Models\Admin;
use App\Models\MeritLog;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;



class AdminController extends Controller
{
    public function dashboard()
{
    $totalStudents  = Student::count();
    $totalOrganizers = Organizer::count();
    $totalEvents    = Event::count();
    $eligibleStudents = Setting::where('key', 'hostel_eligible_students')->value('value');
    $eligibleStudents = $eligibleStudents !== null ? intval($eligibleStudents) : 130;

    $currentSemesterCode = Setting::where('key', 'current_semester_code')->value('value') ?? 'No Active Code';

    return view('admin.dashboard', compact(
        'totalStudents',
        'totalOrganizers',
        'totalEvents',
        'eligibleStudents',
        'currentSemesterCode'
    ));
}

    public function updateHostelEligibility(Request $request)
    {
        $request->validate([
            'eligible_students' => 'required|integer|min:1',
        ]);

        Setting::updateOrCreate(
            ['key' => 'hostel_eligible_students'],
            ['value' => $request->eligible_students]
        );

        return redirect('/admin/hostel')->with('success', 'Jumlah pelajar layak ke kolej semester hadapan telah dikemaskini.');
    }

    public function hostel(Request $request)
    {
        $eligibleStudents = Setting::where('key', 'hostel_eligible_students')->value('value');
        $eligibleStudents = $eligibleStudents !== null ? intval($eligibleStudents) : 130;

        return view('admin.hostel', compact('eligibleStudents'));
    }

    public function viewMerit(Request $request)
    {
        $search = $request->search;
        $selectedSemester = $request->input('semester', 'current');

        $semesters = DB::table('semester_merits')
            ->select('semester_name')
            ->distinct()
            ->orderByDesc('semester_name')
            ->pluck('semester_name');

        if ($selectedSemester !== 'current') {
            $students = DB::table('students')
                ->leftJoin('semester_merits', function($join) use ($selectedSemester) {
                    $join->on('semester_merits.s_id', '=', 'students.s_id')
                         ->where('semester_merits.semester_name', '=', $selectedSemester);
                })
                ->select(
                    'students.s_id',
                    'students.name',
                    'students.num_matrics',
                    DB::raw('COALESCE(SUM(semester_merits.total_merit), 0) as total_merit')
                )
                ->when($search, function ($q) use ($search) {
                    $q->where(function($query) use ($search) {
                        $query->where('students.name', 'like', "%$search%")
                              ->orWhere('students.num_matrics', 'like', "%$search%");
                    });
                })
                ->groupBy('students.s_id', 'students.name', 'students.num_matrics')
                ->orderByDesc('total_merit')
                ->get();
        } else {
            $students = DB::table('students')
                ->leftJoin('merit_logs', function($join) {
                    $join->on('merit_logs.s_id', '=', 'students.s_id')
                         ->where('merit_logs.semester_name', '=', 'current');
                })
                ->select(
                    'students.s_id',
                    'students.name',
                    'students.num_matrics',
                    DB::raw('COALESCE(SUM(merit_logs.points_added), 0) as total_merit')
                )
                ->when($search, function ($q) use ($search) {
                    $q->where(function($query) use ($search) {
                        $query->where('students.name', 'like', "%$search%")
                              ->orWhere('students.num_matrics', 'like', "%$search%");
                    });
                })
                ->groupBy('students.s_id', 'students.name', 'students.num_matrics')
                ->orderByDesc('total_merit')
                ->get();
        }

        return view('admin.merit.index', compact('students', 'search', 'semesters', 'selectedSemester'));
    }

    public function exportMerit(Request $request)
    {
        $selectedSemester = $request->input('semester', 'current');

        if ($selectedSemester !== 'current') {
            $students = DB::table('students')
                ->leftJoin('semester_merits', function($join) use ($selectedSemester) {
                    $join->on('semester_merits.s_id', '=', 'students.s_id')
                         ->where('semester_merits.semester_name', '=', $selectedSemester);
                })
                ->select(
                    'students.name',
                    'students.num_matrics',
                    DB::raw('COALESCE(SUM(semester_merits.total_merit), 0) as total_merit')
                )
                ->groupBy('students.s_id', 'students.name', 'students.num_matrics')
                ->orderByDesc('total_merit')
                ->get();
            $csvFileName = 'student-merit-list-'. Str::slug($selectedSemester) .'-' . date('Y-m-d') . '.csv';
        } else {
            $students = DB::table('students')
                ->leftJoin('merit_logs', function($join) {
                    $join->on('merit_logs.s_id', '=', 'students.s_id')
                         ->where('merit_logs.semester_name', '=', 'current');
                })
                ->select(
                    'students.name',
                    'students.num_matrics',
                    DB::raw('COALESCE(SUM(merit_logs.points_added), 0) as total_merit')
                )
                ->groupBy('students.s_id', 'students.name', 'students.num_matrics')
                ->orderByDesc('total_merit')
                ->get();
            $csvFileName = 'student-merit-list-current-' . date('Y-m-d') . '.csv';
        }

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$csvFileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() use($students) {
        $file = fopen('php://output', 'w');
        fputcsv($file, ['No', 'Student Name', 'Matric No', 'Total Merit']);

        foreach ($students as $index => $row) {
            fputcsv($file, [
                $index + 1,
                $row->name,
                $row->num_matrics,
                $row->total_merit
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

public function viewStudentMerit($id)
{
    $student = Student::findOrFail($id);

    $logs = DB::table('merit_logs')
    ->join('events', 'events.e_id', '=', 'merit_logs.e_id')
    ->where('merit_logs.s_id', $id)
    ->where('merit_logs.semester_name', 'current')
    ->select(
        'events.title as event_title',
        'merit_logs.points_added',
        'merit_logs.created_at'
    )
    ->orderBy('merit_logs.created_at', 'asc')
    ->get();

$totalMerit = $logs->sum('points_added');

    return view('admin.merit.show', compact(
        'student',
        'logs',
        'totalMerit'
    ));
}




public function manageOrganizers(Request $request)
{
    $filter = $request->get('status'); // pending / approved

    $organizers = Organizer::when($filter, function ($q) use ($filter) {
            $q->where('status', $filter);
        })
        ->orderByDesc('created_at')
        ->get();

    $pendingCount = Organizer::where('status', 'pending')->count();

    return view('admin.organizers.index', compact(
        'organizers',
        'filter',
        'pendingCount'
    ));
}



public function approveOrganizer($id)
{
    $org = Organizer::findOrFail($id);
    $org->status = 'approved';
    $org->save();

    return back()->with('success', 'Organizer approved');
}


public function rejectOrganizer($id)
{
    $org = Organizer::findOrFail($id);
    $org->status = 'rejected';
    $org->save();

    return back()->with('success', 'Organizer rejected');
}

public function viewOrganizer($id)
{
    $organizer = Organizer::findOrFail($id);
    return view('admin.organizers.show', compact('organizer'));
}








    public function manageEvents(Request $request)
{
    $filter = $request->get('status'); // pending / approved

    $events = Event::when($filter, function ($q) use ($filter) {
            $q->where('status', $filter);
        })
        ->orderByDesc('created_at')
        ->get();

    $pendingCount = Event::where('status', 'pending')->count();

    return view('admin.events.index', compact(
        'events',
        'filter',
        'pendingCount'
    ));
}


public function approveEvent(Request $request, $id)
{
    if (!session('admin_id')) {
        abort(403, 'Unauthorized');
    }

    $request->validate([
        'merit_value' => 'required|integer|min:1|max:100'
    ]);

    $event = Event::findOrFail($id);

    // elak approve dua kali
    if ($event->status === 'approved') {
        return back()->with('info', 'Event already approved');
    }

    // 1. Set merit value
    $event->merit_value = $request->merit_value;

    // 2. Approve
    $event->status = 'approved';

    // 3. Generate token
    $event->qr_code_token = Str::uuid();

    // 4. QR link (sementara)
    $qrLink = $event->qr_code_token;

    try {
        // 5. Generate QR
        $qrImage = QrCode::format('svg')
        ->size(300)
        ->generate($qrLink);

        // 6. Simpan QR
        $path = 'qrcode/event_'.$event->e_id.'.svg';
        Storage::disk('public')->put($path, $qrImage);

        // 7. Simpan path
        $event->qr_path = $path;
    } catch (\Throwable $e) {
        // If QR fails, still approve but log error
        \Log::error('QR Code generation failed for event ' . $event->e_id . ': ' . $e->getMessage());
        // Still save without QR
    }

    $event->save();

    return back()->with('success', 'Event approved with ' . $request->merit_value . ' merit points & QR generated');
}



public function rejectEvent($id)
{
    if (!session('admin_id')) {
        abort(403, 'Unauthorized');
    }

    $event = Event::findOrFail($id);
    $event->status = 'rejected';
    $event->save();

    return back()->with('success', 'Event rejected');
}


public function viewEvent($id)
{
    if (!session('admin_id')) {
        abort(403, 'Unauthorized');
    }

    $event = Event::findOrFail($id);

    $attendances = \App\Models\Attendance::with('student')
        ->where('e_id', $id)
        ->orderBy('scan_time', 'asc')
        ->get();

    return view('admin.events.show', compact('event', 'attendances'));
}


public function editEvent($id)
{
    if (!session('admin_id')) {
        abort(403, 'Unauthorized');
    }

    $event = Event::findOrFail($id);
    return view('admin.events.edit', compact('event'));
}


public function updateEvent(Request $request, $id)
{
    if (!session('admin_id')) {
        abort(403, 'Unauthorized');
    }

    $request->validate([
        'title'         => 'required|string|max:255',
        'description'   => 'nullable|string',
        'location_name' => 'required|string|max:255',
        'location_lat'  => 'required|numeric',
        'location_long' => 'required|numeric',
        'radius_meter'  => 'required|integer|min:1',
        'start_time'    => 'required|date',
        'end_time'      => 'required|date|after_or_equal:start_time',
        'merit_value'   => 'nullable|integer|min:0|max:100',
        'category'      => 'nullable|string|max:50',
        'telegram_link' => 'nullable|url',
        'whatsapp_link' => 'nullable|url',
        'event_details' => 'nullable|string',
        'event_banner'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    $event = Event::findOrFail($id);

    $event->title         = $request->title;
    $event->description   = $request->description;
    $event->location_name = $request->location_name;
    $event->location_lat  = $request->location_lat;
    $event->location_long = $request->location_long;
    $event->radius_meter  = $request->radius_meter;
    $event->start_time    = \Carbon\Carbon::parse($request->start_time);
    $event->end_time      = \Carbon\Carbon::parse($request->end_time);
    $event->category      = $request->category;
    $event->event_details = $request->event_details;
    $event->telegram_link = $request->telegram_link;
    $event->whatsapp_link = $request->whatsapp_link;

    if ($request->filled('merit_value')) {
        $event->merit_value = $request->merit_value;
    }

    if ($request->hasFile('event_banner')) {
        if ($event->event_banner) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($event->event_banner);
        }
        $path = $request->file('event_banner')->store('banners', 'public');
        $event->event_banner = $path;
    }

    $event->save();

    return redirect('/admin/events/' . $id)->with('success', 'Event updated successfully by Admin HEP.');
}








    public function reset()
    {
        return view('admin.reset');
    }

    public function processReset(Request $request)
    {
        $request->validate([
            'semester_name' => 'required|string|max:255',
        ]);

        $semesterName = $request->semester_name;

        // 1. Get current merits for all students (current semester only)
        $students = DB::table('students')
            ->leftJoin('merit_logs', function($join) {
                $join->on('merit_logs.s_id', '=', 'students.s_id')
                     ->where('merit_logs.semester_name', '=', 'current');
            })
            ->select(
                'students.s_id',
                DB::raw('COALESCE(SUM(merit_logs.points_added), 0) as total_merit')
            )
            ->groupBy('students.s_id')
            ->get();

        // 2. Save to semester_merits table
        $insertData = [];
        $now = now();
        foreach ($students as $student) {
            $insertData[] = [
                's_id' => $student->s_id,
                'semester_name' => $semesterName,
                'total_merit' => $student->total_merit,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert in chunks to be safe with DB limits
        foreach (array_chunk($insertData, 500) as $chunk) {
            DB::table('semester_merits')->insert($chunk);
        }

        // 3. Clear existing merits (Update 'current' to the reset semester name instead of truncating)
        DB::table('merit_logs')
            ->where('semester_name', 'current')
            ->update(['semester_name' => $semesterName]);

        // Generate a new random semester code
        $semesterCode = 'SEM-' . strtoupper(Str::random(6));
        Setting::updateOrCreate(
            ['key' => 'current_semester_code'],
            ['value' => $semesterCode]
        );

        // Deactivate all students for the new semester and reset merit
        DB::table('students')->update([
            'total_merit' => 0,
            'current_semester_active' => false
        ]);
        
        // 4. Archive attendances to semester_attendances (current semester only)
        $attendances = DB::table('attendances')
            ->join('events', 'attendances.e_id', '=', 'events.e_id')
            ->where('attendances.semester_name', 'current')
            ->select(
                'attendances.s_id',
                'events.title as event_name',
                'events.start_time as event_date',
                'events.merit_value'
            )
            ->get();
            
        $attInsertData = [];
        foreach ($attendances as $att) {
            $attInsertData[] = [
                's_id' => $att->s_id,
                'semester_name' => $semesterName,
                'event_name' => $att->event_name,
                'event_date' => $att->event_date,
                'merit_value' => $att->merit_value,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($attInsertData, 500) as $chunk) {
            DB::table('semester_attendances')->insert($chunk);
        }

        // 5. Clear current attendances (Update 'current' to the reset semester name instead of truncating)
        DB::table('attendances')
            ->where('semester_name', 'current')
            ->update(['semester_name' => $semesterName]);
        
        // Also clear ranking snapshots if applicable
        if (Schema::hasTable('ranking_snapshots')) {
            DB::table('ranking_snapshots')->truncate();
        }

        return redirect('/admin/dashboard')->with('success', 'All student merits have been successfully reset and saved. New semester join code is: ' . $semesterCode);
    }


    public function profile()
{
    $adminId = session('admin_id');
    if (!$adminId) {
        return redirect('/login')->with('error', 'Please login as Admin HEP.');
    }

    $admin = Admin::findOrFail($adminId);

    return view('admin.profile', compact('admin'));
}

    public function updateProfile(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'nullable|min:6'
    ]);

    $admin = Admin::findOrFail(session('admin_id'));

    // update email
    $admin->email = $request->email;

    // update password (ikut column sebenar)
    if ($request->filled('password')) {
        $admin->pass_hash = Hash::make($request->password);
    }

    $admin->save();

    return back()->with('success', 'Profile updated successfully');
}



public function showSendReminder()
    {
        return view('admin.send-reminder');
    }

    public function sendReminderToAll()
{
    $students = Student::orderByDesc('total_merit')->get();
    
    $eligibleStudents = Setting::where('key', 'hostel_eligible_students')->value('value');
    $eligibleStudents = $eligibleStudents !== null ? intval($eligibleStudents) : 130;

    // Calculate dynamic brackets based on the eligible limit
    $bracket1Max = max(1, intval(round($eligibleStudents * 0.23))); // equivalent to top ~30 of 130
    $bracket2Max = max($bracket1Max + 1, intval(round($eligibleStudents * 0.77))); // equivalent to top ~100 of 130
    $bracket3Max = $eligibleStudents; // up to the limit

    foreach ($students as $index => $student) {

        $rank = $index + 1;
        $messageBody = "";

        if ($rank >= 1 && $rank <= $bracket1Max) {

            $messageBody = "Congratulations {$student->name}!

You are currently ranked #{$rank} based on your merit points.

Excellent performance! Keep up the great work and maintain your position to secure hostel accommodation for the upcoming semester.";

        } elseif ($rank > $bracket1Max && $rank <= $bracket2Max) {

            $messageBody = "Dear {$student->name},

Your current merit ranking is #{$rank}.

You are in a competitive position. Continue participating in events to improve your merit points and strengthen your chances of obtaining hostel accommodation.";

        } elseif ($rank > $bracket2Max && $rank <= $bracket3Max) {

            $messageBody = "Dear {$student->name},

Your current merit ranking is #{$rank}.

Please be cautious. Only the top {$eligibleStudents} students are eligible for hostel accommodation.
You are advised to actively participate in more events to maintain or improve your ranking.";

        } else {

            $messageBody = "Dear {$student->name},

Your current merit ranking is #{$rank}.

Unfortunately, your ranking is currently outside the hostel eligibility range (top {$eligibleStudents} students).
You are encouraged to participate in more events to earn additional merit points and improve your chances of securing hostel accommodation next semester.";
        }

        // SEND EMAIL
        Mail::raw(
            $messageBody . "\n\nRegards,\nStudent Affairs Department",
            function ($message) use ($student) {
                $message->to($student->email)
                        ->subject('Merit Ranking & Hostel Accommodation Reminder');
            }
        );
    }

    return back()->with('success', 'Merit reminder emails sent successfully.');
}


}
