<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Models\Student;
use Illuminate\Support\Facades\Schedule;

Artisan::command('ranking:snapshot', function () {
    Student::saveDailyRankingSnapshot();
    $this->info('Daily ranking snapshot saved successfully.');
})->purpose('Save daily student ranking snapshot');

Schedule::command('ranking:snapshot')->dailyAt('23:59');

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

Artisan::command('emms:send-reminders', function () {
    $this->info('Starting emms:send-reminders command...');
    // Prevent script timeout in CLI
    set_time_limit(0);

    // Fetch active students and calculate their merits dynamically
    $students = DB::table('students')
        ->where('students.current_semester_active', true)
        ->leftJoin('merit_logs', function($join) {
            $join->on('merit_logs.s_id', '=', 'students.s_id')
                 ->where('merit_logs.semester_name', '=', 'current');
        })
        ->select(
            'students.s_id',
            'students.name',
            'students.email',
            DB::raw('COALESCE(SUM(merit_logs.points_added), 0) as total_merit')
        )
        ->groupBy('students.s_id', 'students.name', 'students.email')
        ->orderByDesc('total_merit')
        ->get();
    
    $studentCount = $students->count();
    $this->info("Found {$studentCount} active students.");

    if ($studentCount === 0) {
        $this->warn('No active students found. Exiting.');
        return;
    }

    $eligibleStudents = Setting::where('key', 'hostel_eligible_students')->value('value');
    $eligibleStudents = $eligibleStudents !== null ? intval($eligibleStudents) : 130;

    // Calculate dynamic brackets based on the eligible limit
    $bracket1Max = max(1, intval(round($eligibleStudents * 0.23))); // equivalent to top ~30 of 130
    $bracket2Max = max($bracket1Max + 1, intval(round($eligibleStudents * 0.77))); // equivalent to top ~100 of 130
    $bracket3Max = $eligibleStudents; // up to the limit

    foreach ($students as $index => $student) {
        $rank = $index + 1;
        $this->info("({$rank}/{$studentCount}) Sending email to {$student->name} ({$student->email}) with merit {$student->total_merit}...");

        $messageBody = "";

        if ($rank >= 1 && $rank <= $bracket1Max) {
            $messageBody = "Congratulations {$student->name}!\n\nYou are currently ranked #{$rank} based on your merit points.\n\nExcellent performance! Keep up the great work and maintain your position to secure hostel accommodation for the upcoming semester.";
        } elseif ($rank > $bracket1Max && $rank <= $bracket2Max) {
            $messageBody = "Dear {$student->name},\n\nYour current merit ranking is #{$rank}.\n\nYou are in a competitive position. Continue participating in events to improve your merit points and strengthen your chances of obtaining hostel accommodation.";
        } elseif ($rank > $bracket2Max && $rank <= $bracket3Max) {
            $messageBody = "Dear {$student->name},\n\nYour current merit ranking is #{$rank}.\n\nPlease be cautious. Only the top {$eligibleStudents} students are eligible for hostel accommodation.\nYou are advised to actively participate in more events to maintain or improve your ranking.";
        } else {
            $messageBody = "Dear {$student->name},\n\nYour current merit ranking is #{$rank}.\n\nUnfortunately, your ranking is currently outside the hostel eligibility range (top {$eligibleStudents} students).\nYou are encouraged to participate in more events to earn additional merit points and improve your chances of securing hostel accommodation next semester.";
        }

        // SEND EMAIL
        try {
            Mail::raw(
                $messageBody . "\n\nRegards,\nStudent Affairs Department",
                function ($message) use ($student) {
                    $message->to($student->email)
                            ->subject('Merit Ranking & Hostel Accommodation Reminder');
                }
            );
            $this->info("Successfully sent email to {$student->name}.");
        } catch (\Exception $e) {
            $this->error("Failed to send email to {$student->name}: " . $e->getMessage());
            \Log::error("Failed to send merit reminder to student {$student->name} ({$student->email}): " . $e->getMessage());
        }

        // Delay 0.1s to prevent rate limits
        usleep(100000);
    }

    $this->info('Completed sending all merit reminders.');
})->purpose('Send merit reminders to all active students in the background');
