<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $primaryKey = 'e_id';

    protected $fillable = [
        'o_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'location_name',
        'location_lat',
        'location_long',
        'radius_meter',
        'qr_code_token',
        'qr_path',
        'merit_value',
        'status',
        'proposal_path',
        'telegram_link',
        'whatsapp_link',
        'category',
        'event_banner',
        'event_details'
    ];

    public function organizer()
    {
        return $this->belongsTo(Organizer::class, 'o_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'e_id');
    }

    public function meritLogs()
    {
        return $this->hasMany(MeritLog::class, 'e_id');
    }
}
