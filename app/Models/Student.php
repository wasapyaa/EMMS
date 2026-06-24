<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $primaryKey = 's_id';

    protected $fillable = [
        'num_matrics',
        'name',
        'email',
        'phone',
        'pass_hash',
        'total_merit',
        'current_semester_active',
        'preferred_categories',
        'course',
        'otp_code',
        'otp_expires_at',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 's_id');
    }

    public function meritLogs()
    {
        return $this->hasMany(MeritLog::class, 's_id');
    }

    public static function saveDailyRankingSnapshot()
    {
        $today = now()->toDateString();

        $snapshotExists = \Illuminate\Support\Facades\DB::table('ranking_snapshots')
            ->where('snapshot_date', $today)
            ->exists();

        if (!$snapshotExists) {
            $studentsToday = \Illuminate\Support\Facades\DB::table('students')
                ->where('students.current_semester_active', true)
                ->leftJoin('merit_logs', function($join) {
                    $join->on('merit_logs.s_id', '=', 'students.s_id')
                         ->where('merit_logs.semester_name', '=', 'current');
                })
                ->select(
                    'students.s_id',
                    \Illuminate\Support\Facades\DB::raw('COALESCE(SUM(merit_logs.points_added), 0) as total_merit')
                )
                ->groupBy('students.s_id')
                ->orderByDesc('total_merit')
                ->get();

            $rank = 1;
            foreach ($studentsToday as $s) {
                \Illuminate\Support\Facades\DB::table('ranking_snapshots')->insert([
                    's_id' => $s->s_id,
                    'rank' => $rank,
                    'snapshot_date' => $today,
                    'created_at' => now()
                ]);
                $rank++;
            }
        }
    }
}

