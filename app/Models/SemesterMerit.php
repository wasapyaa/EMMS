<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SemesterMerit extends Model
{
    protected $fillable = [
        's_id',
        'semester_name',
        'total_merit'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 's_id');
    }
}
