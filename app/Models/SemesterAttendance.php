<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SemesterAttendance extends Model
{
    protected $fillable = [
        's_id',
        'semester_name',
        'event_name',
        'event_date',
        'merit_value'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 's_id');
    }
}
