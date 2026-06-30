<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;

// 🔥 TAMBAH INI
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\MeritLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        // ambil student login
        $studentId = session('user_id');
        $student = Student::findOrFail($studentId);
        $selectedSemester = $request->input('semester', 'current');
        $eligibleStudents = Setting::where('key', 'hostel_eligible_students')->value('value');
        $eligibleStudents = $eligibleStudents !== null ? intval($eligibleStudents) : 130;

        $semesters = DB::table('semester_merits')
            ->where('s_id', $studentId)
            ->select('semester_name')
            ->distinct()
            ->orderByDesc('semester_name')
            ->pluck('semester_name');

        if ($selectedSemester !== 'current') {
            $totalStudents = DB::table('semester_merits')
                ->where('semester_name', $selectedSemester)
                ->where('total_merit', '>', 0)
                ->count();
        } else {
            $totalStudents = Student::where('current_semester_active', true)->count();
        }

        if ($selectedSemester !== 'current') {
            // 1️⃣ TOTAL MERIT (Past Semester)
            $totalMerit = DB::table('semester_merits')
                ->where('s_id', $studentId)
                ->where('semester_name', $selectedSemester)
                ->value('total_merit') ?? 0;

            // 2️⃣ EVENTS ATTENDED (Past Semester)
            $eventsAttended = DB::table('semester_attendances')
                ->where('s_id', $studentId)
                ->where('semester_name', $selectedSemester)
                ->count();

            // 3️⃣ RANKING (Past Semester)
            $rankingList = DB::table('semester_merits')
                ->where('semester_name', $selectedSemester)
                ->orderByDesc('total_merit')
                ->pluck('s_id');
            
            $ranking = $rankingList->search($studentId);
            $ranking = $ranking !== false ? $ranking + 1 : '-';

            $participations = DB::table('semester_attendances')
                ->where('s_id', $studentId)
                ->where('semester_name', $selectedSemester)
                ->orderByDesc('event_date')
                ->limit(5)
                ->get();
        } else {
            // 1️⃣ TOTAL MERIT (SUM dari merit_logs for current semester)
            $totalMerit = DB::table('merit_logs')
                ->where('s_id', $studentId)
                ->where('semester_name', 'current')
                ->sum('points_added');

            // 2️⃣ EVENTS ATTENDED (current semester)
            $eventsAttended = Attendance::where('s_id', $studentId)
                ->where('semester_name', 'current')
                ->count();

            // 3️⃣ RANKING (simple & konsisten - current semester)
            $rankingList = DB::table('students')
                ->where('students.current_semester_active', true)
                ->leftJoin('merit_logs', function($join) {
                    $join->on('merit_logs.s_id', '=', 'students.s_id')
                         ->where('merit_logs.semester_name', '=', 'current');
                })
                ->select(
                    'students.s_id',
                    DB::raw('COALESCE(SUM(merit_logs.points_added),0) as total_merit')
                )
                ->groupBy('students.s_id')
                ->orderByDesc('total_merit')
                ->pluck('students.s_id');

            $ranking = $rankingList->search($studentId);
            $ranking = $ranking !== false ? $ranking + 1 : '-';

            $participations = DB::table('attendances')
                ->join('events', 'attendances.e_id', '=', 'events.e_id')
                ->where('attendances.s_id', $studentId)
                ->where('attendances.semester_name', 'current')
                ->orderByDesc('events.start_time')
                ->limit(5)
                ->select(
                    'events.title as event_name',
                    'events.start_time as event_date',
                    'events.merit_value'
                )
                ->get();
        }

        return view('student.dashboard', compact(
            'student',
            'totalMerit',
            'eventsAttended',
            'ranking',
            'participations',
            'semesters',
            'selectedSemester',
            'eligibleStudents',
            'totalStudents'
        ));
    }

  

    public function events(Request $request)
{
    $query = Event::where('status', 'approved')->where('semester_name', 'current');

    // 🔍 REAL SEARCH
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%')
              ->orWhere('location_name', 'like', '%' . $request->search . '%');
        });
    }

    $events = $query->orderBy('start_time', 'desc')->get();

    return view('student.events', compact('events'));
}

    public function participation(Request $request)
    {
        $studentId = session('user_id');
        $selectedSemester = $request->input('semester', 'current');

        $semesters = DB::table('semester_merits')
            ->where('s_id', $studentId)
            ->select('semester_name')
            ->distinct()
            ->orderByDesc('semester_name')
            ->pluck('semester_name');

        if ($selectedSemester !== 'current') {
            $participations = DB::table('semester_attendances')
                ->where('s_id', $studentId)
                ->where('semester_name', $selectedSemester)
                ->select(
                    'event_name',
                    DB::raw('NULL as location_name'),
                    'event_date',
                    'merit_value'
                )
                ->orderByDesc('event_date')
                ->get();
        } else {
            $participations = DB::table('attendances')
                ->join('events', 'attendances.e_id', '=', 'events.e_id')
                ->where('attendances.s_id', $studentId)
                ->where('attendances.semester_name', 'current')
                ->select(
                    'events.title as event_name',
                    'events.location_name',
                    'events.start_time as event_date',
                    'events.merit_value'
                )
                ->orderByDesc('events.start_time')
                ->get();
        }

        return view('student.participation', compact('participations', 'semesters', 'selectedSemester'));
    }

    public function ranking(Request $request)
    {
        $studentId = session('user_id');
        $selectedSemester = $request->input('semester', 'current');

        $semesters = DB::table('semester_merits')
            ->where('s_id', $studentId)
            ->select('semester_name')
            ->distinct()
            ->orderByDesc('semester_name')
            ->pluck('semester_name');

        if ($selectedSemester !== 'current') {
            $students = DB::table('semester_merits')
                ->join('students', 'students.s_id', '=', 'semester_merits.s_id')
                ->where('semester_merits.semester_name', $selectedSemester)
                ->select(
                    'students.s_id',
                    'students.name',
                    'semester_merits.total_merit'
                )
                ->orderByDesc('semester_merits.total_merit')
                ->get();
            
            $ranking = $students->pluck('s_id')->search($studentId);
            $ranking = $ranking !== false ? $ranking + 1 : '-';
            
            $rankingHistory = []; // Graf trend tidak tersedia untuk semester lepas
        } else {
            /* =====================================================
               1️⃣ SIMPAN RANKING SNAPSHOT (SEKALI SEHARI)
               ===================================================== */
            Student::saveDailyRankingSnapshot();

            /* =====================================================
               2️⃣ LEADERBOARD SEMASA
               ===================================================== */
            $students = DB::table('students')
                ->where('students.current_semester_active', true)
                ->leftJoin('merit_logs', function($join) {
                    $join->on('merit_logs.s_id', '=', 'students.s_id')
                         ->where('merit_logs.semester_name', '=', 'current');
                })
                ->select(
                    'students.s_id',
                    'students.name',
                    DB::raw('COALESCE(SUM(merit_logs.points_added), 0) as total_merit')
                )
                ->groupBy('students.s_id', 'students.name')
                ->orderByDesc('total_merit')
                ->get();

            // ranking semasa student login
            $ranking = $students->pluck('s_id')->search($studentId);
            $ranking = $ranking !== false ? $ranking + 1 : '-';

            /* =====================================================
               3️⃣ DATA GRAF (RANKING TREND STUDENT LOGIN)
               ===================================================== */
            $rankingHistory = DB::table('ranking_snapshots')
                ->where('s_id', $studentId)
                ->orderBy('snapshot_date')
                ->get()
                ->map(function ($row) {
                    return [
                        'date' => $row->snapshot_date,
                        'rank' => $row->rank
                    ];
                })
                ->toArray();
        }

        /* =====================================================
           4️⃣ RETURN VIEW
           ===================================================== */
        return view('student.ranking', compact(
            'students',
            'ranking',
            'rankingHistory',
            'semesters',
            'selectedSemester'
        ));
    }



public function profile()
{
    $student = Student::findOrFail(session('user_id'));
    return view('student.profile', compact('student'));
}

public function updateProfile(Request $request)
{
    $request->validate([
        'name'  => 'required|string|max:100',
        'phone' => 'required|digits_between:10,11'
    ]);

    $student = Student::findOrFail(session('user_id'));
    $student->name  = $request->name;
    $student->phone = $request->phone;
    $student->save();

    return back()->with('success', 'Profile updated successfully');
}



public function updatePassword(Request $request)
{
    $request->validate([
        'password' => 'required|min:6'
    ]);

    $student = Student::findOrFail(session('user_id'));
    $student->pass_hash = Hash::make($request->password);
    $student->save();

    return back()->with('success', 'Password updated successfully');
}

public function joinSemester(Request $request)
{
    $request->validate([
        'semester_code' => 'required|string'
    ]);

    $activeCode = Setting::where('key', 'current_semester_code')->value('value');

    if (!$activeCode || strtoupper($request->semester_code) !== strtoupper($activeCode)) {
        return back()->with('error', 'Invalid Semester Join Code. Please contact Admin HEP for the correct code.');
    }

    $student = Student::findOrFail(session('user_id'));
    $student->current_semester_active = true;
    $student->save();

    return redirect('/student/dashboard')->with('success', 'Successfully joined the new semester!');
}

}

