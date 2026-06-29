<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\MeritLog;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AttendanceController extends Controller
{
    public function scan(Request $request)
    {
        // 1️⃣ VALIDATION
        $request->validate([
            's_id'      => 'required|integer',
            'token'     => 'required|string',
            'device_id' => 'required|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Check if student has joined the current semester
        $student = Student::find($request->s_id);
        if (!$student || !$student->current_semester_active) {
            return response()->json([
                'status' => 'fail',
                'code'   => 'semester_not_joined',
                'message'=> 'You have not joined the current semester. Please join the semester using the active join code first.'
            ], 403);
        }

        // 2️⃣ FIND EVENT BY TOKEN
        $event = Event::where('qr_code_token', $request->token)->first();

        if (!$event) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid QR code.'
            ], 404);
        }
        // 🚫 CHECK EVENT TIME (ONLY ALLOW SCAN BETWEEN START & END TIME)
        $now = now();
        $startTime = \Carbon\Carbon::parse($event->start_time);
        $endTime = \Carbon\Carbon::parse($event->end_time);

        if ($now->lt($startTime)) {
            return response()->json([
                'status' => 'fail',
                'code'   => 'event_not_started',
                'message'=> 'Event has not started yet. Scan is only allowed during the event.'
            ], 403);
        }

        if ($now->gt($endTime)) {
            return response()->json([
                'status' => 'fail',
                'code'   => 'event_ended',
                'message'=> 'Event has already ended. Attendance scan is closed.'
            ], 403);
        }


        // 🚫 CHECK STUDENT DAH ATTEND EVENT INI
        $alreadyAttend = Attendance::where('s_id', $request->s_id)
            ->where('e_id', $event->e_id)
            ->exists();

        if ($alreadyAttend) {
            return response()->json([
                'status' => 'fail',
                'code'   => 'already_attended',
                'message'=> 'You have already recorded attendance for this event.'
            ], 403);
        }


        // 3️⃣ CHECK DUPLICATE DEVICE
        $exists = Attendance::where('e_id', $event->e_id)
            ->where('device_id', $request->device_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'fail',
                'code'   => 'device_duplicate',
                'message'=> 'This device has already scanned for this event.'
            ], 403);
        }



        // 4️⃣ CALCULATE DISTANCE
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $event->location_lat,
            $event->location_long
        );

        // 5️⃣ GEOFENCE CHECK
        if ($distance > $event->radius_meter) {
            return response()->json([
                'status'   => 'fail',
                'code'     => 'outside_area',
                'message'  => 'You are outside the allowed attendance area.',
                'distance' => round($distance, 2)
            ], 403);
        }

        // 6️⃣ SAVE ATTENDANCE
        Attendance::create([
            's_id'      => $request->s_id,
            'e_id'      => $event->e_id,
            'device_id' => $request->device_id,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'distance'  => round($distance, 2),
            'status'    => 'success',
            'scan_time' => now(),
        ]);

        // 7️⃣ MERIT
        MeritLog::create([
            's_id' => $request->s_id,
            'e_id' => $event->e_id,
            'points_added' => $event->merit_value,
        ]);

        Student::where('s_id', $request->s_id)
            ->increment('total_merit', $event->merit_value);

        return response()->json([
            'status'  => 'success',
            'code'    => 'success',
            'message' => 'Attendance recorded successfully.',
            'event' => $event->title,
            'distance'=> round($distance, 2)
        ]);
    }

    public function events(Request $request)
    {
        $semester = $request->query('semester', 'current');
        $semesters = DB::table('semester_merits')
            ->select('semester_name')
            ->orderByDesc('semester_name')
            ->pluck('semester_name')
            ->toArray();

        $semesters = array_values(array_unique(array_merge(['current'], $semesters)));

        if ($semester === 'current') {
            $query = Event::where('status', 'approved');

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('location_name', 'like', '%' . $request->search . '%');
                });
            }

            $events = $query->orderByDesc('start_time')
                ->get([
                    'e_id',
                    'title',
                    'location_name',
                    'start_time',
                    'end_time',
                    'merit_value',
                    'description',
                    'telegram_link',
                    'whatsapp_link',
                    'category',
                    'event_banner',
                    'event_details',
                ]);
        } else {
            $query = DB::table('semester_attendances')
                ->where('semester_name', $semester);

            if ($request->filled('search')) {
                $query->where('event_name', 'like', '%' . $request->search . '%');
            }

            $events = $query->orderByDesc('event_date')
                ->get([
                    'id as e_id',
                    'event_name as title',
                    DB::raw("'Past Event' as location_name"),
                    'event_date as start_time',
                    'merit_value',
                ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $events,
            'semesters' => $semesters,
            'selectedSemester' => $semester,
        ]);
    }

    public function participation(Request $request, $s_id)
    {
        $student = Student::find($s_id);
        if (!$student) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Student not found.'
            ], 404);
        }

        $semester = $request->query('semester', 'current');
        $semesters = DB::table('semester_merits')
            ->select('semester_name')
            ->orderByDesc('semester_name')
            ->pluck('semester_name')
            ->toArray();

        $semesters = array_values(array_unique(array_merge(['current'], $semesters)));

        if ($semester === 'current') {
            $participations = DB::table('attendances')
                ->join('events', 'events.e_id', '=', 'attendances.e_id')
                ->where('attendances.s_id', $s_id)
                ->where('attendances.status', 'success')
                ->where('attendances.semester_name', 'current')
                ->orderByDesc('events.start_time')
                ->select(
                    'events.title as event_name',
                    'events.location_name',
                    'events.start_time as event_date',
                    'events.merit_value'
                )
                ->get();
        } else {
            $participations = DB::table('semester_attendances')
                ->where('semester_attendances.s_id', $s_id)
                ->where('semester_attendances.semester_name', $semester)
                ->orderByDesc('semester_attendances.event_date')
                ->select(
                    'semester_attendances.event_name',
                    DB::raw("'Past Event' as location_name"),
                    'semester_attendances.event_date',
                    'semester_attendances.merit_value'
                )
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $participations,
            'semesters' => $semesters,
            'selectedSemester' => $semester,
        ]);
    }

    public function profile($s_id)
    {
        $student = Student::find($s_id);
        if (!$student) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Student not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                's_id' => $student->s_id,
                'name' => $student->name,
                'email' => $student->email,
                'phone' => $student->phone,
                'num_matrics' => $student->num_matrics,
                'total_merit' => $student->total_merit,
                'preferred_categories' => $student->preferred_categories,
            ],
        ]);
    }

    public function updateProfile(Request $request, $s_id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|digits_between:10,11',
            'preferred_categories' => 'nullable|string',
        ]);

        $student = Student::find($s_id);
        if (!$student) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Student not found.'
            ], 404);
        }

        $student->name = $request->name;
        $student->phone = $request->phone;
        
        if ($request->has('preferred_categories')) {
            $student->preferred_categories = $request->preferred_categories;
        }
        
        $student->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => [
                'name' => $student->name,
                'phone' => $student->phone,
                'preferred_categories' => $student->preferred_categories,
            ],
        ]);
    }

    public function updatePassword(Request $request, $s_id)
    {
        $request->validate([
            'password' => 'required|min:6',
        ]);

        $student = Student::find($s_id);
        if (!$student) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Student not found.'
            ], 404);
        }

        $student->pass_hash = Hash::make($request->password);
        $student->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully',
        ]);
    }

    // 📐 HAVERSINE (meter)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earth = 6371000;

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        return acos(
            sin($lat1) * sin($lat2) +
            cos($lat1) * cos($lat2) * cos($lon2 - $lon1)
        ) * $earth;
    }

    public function history($s_id)
    {
        $history = \DB::table('attendances')
            ->join('events', 'events.e_id', '=', 'attendances.e_id')
            ->where('attendances.s_id', $s_id)
            ->where('attendances.status', 'success')
            ->orderByDesc('attendances.scan_time')
            ->select(
                'events.title as event_title',
                'attendances.scan_time'
            )
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $history
        ]);
    }

    public function ranking(Request $request, $s_id)
    {
        $semester = $request->query('semester', 'current');

        $student = Student::find($s_id);
        if (!$student) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Student not found.'
            ], 404);
        }

        // Generate daily snapshot if it doesn't exist yet
        Student::saveDailyRankingSnapshot();

        $semesters = DB::table('semester_merits')
            ->select('semester_name')
            ->distinct()
            ->orderByDesc('semester_name')
            ->pluck('semester_name')
            ->toArray();

        $semesters = array_values(array_unique(array_merge(['current'], $semesters)));

        if ($semester === 'current') {
            $totalMerit = DB::table('merit_logs')
                ->where('s_id', $s_id)
                ->where('semester_name', 'current')
                ->sum('points_added');

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

            $eventsAttended = Attendance::where('s_id', $s_id)
                ->where('status', 'success')
                ->where('semester_name', 'current')
                ->count();
        } else {
            $totalMerit = DB::table('semester_merits')
                ->where('s_id', $s_id)
                ->where('semester_name', $semester)
                ->value('total_merit') ?? 0;

            $students = DB::table('semester_merits')
                ->join('students', 'students.s_id', '=', 'semester_merits.s_id')
                ->where('semester_merits.semester_name', $semester)
                ->select(
                    'students.s_id',
                    'students.name',
                    'semester_merits.total_merit as total_merit'
                )
                ->orderByDesc('semester_merits.total_merit')
                ->get();

            $eventsAttended = DB::table('semester_attendances')
                ->where('s_id', $s_id)
                ->where('semester_name', $semester)
                ->count();
        }

        $rankIndex = $students->pluck('s_id')->search($s_id);
        $rank = $rankIndex !== false ? $rankIndex + 1 : null;

        $eligibleStudents = Setting::where('key', 'hostel_eligible_students')->value('value');
        $eligibleStudents = $eligibleStudents !== null ? intval($eligibleStudents) : 130;
        $hostelEligible = $rank !== null && $rank <= $eligibleStudents;

        $topStudents = $students->values()->map(function ($s, $index) {
            return [
                'rank' => $index + 1,
                's_id' => $s->s_id,
                'name' => $s->name,
                'total_merit' => $s->total_merit,
            ];
        });

        if ($semester === 'current') {
            $rankingHistory = DB::table('ranking_snapshots')
                ->where('s_id', $s_id)
                ->orderBy('snapshot_date')
                ->get()
                ->map(function ($row) {
                    return [
                        'date' => $row->snapshot_date,
                        'rank' => $row->rank
                    ];
                })
                ->toArray();
        } else {
            $rankingHistory = [];
        }

        return response()->json([
            'status' => 'success',
            'semesters' => $semesters,
            'selectedSemester' => $semester,
            'data' => [
                'student' => [
                    's_id' => $student->s_id,
                    'name' => $student->name,
                    'num_matrics' => $student->num_matrics,
                    'total_merit' => $totalMerit,
                    'rank' => $rank,
                    'current_semester_active' => (bool) $student->current_semester_active,
                ],
                'events_attended' => $eventsAttended,
                'top_students' => $topStudents,
                'total_students' => $students->count(),
                'eligible_students' => $eligibleStudents,
                'hostel_eligible' => $hostelEligible,
                'rankingHistory' => $rankingHistory,
            ],
        ]);
    }

    public function joinSemester(Request $request)
    {
        $request->validate([
            's_id'          => 'required|integer',
            'semester_code' => 'required|string',
        ]);

        $activeCode = Setting::where('key', 'current_semester_code')->value('value');

        if (!$activeCode || strtoupper($request->semester_code) !== strtoupper($activeCode)) {
            return response()->json([
                'status' => 'fail',
                'message'=> 'Invalid Semester Join Code. Please contact Admin HEP for the correct code.'
            ], 400);
        }

        $student = Student::find($request->s_id);
        if (!$student) {
            return response()->json([
                'status' => 'fail',
                'message'=> 'Student not found.'
            ], 404);
        }

        $student->current_semester_active = true;
        $student->save();

        return response()->json([
            'status' => 'success',
            'message'=> 'Successfully joined the new semester!'
        ]);
    }
}
