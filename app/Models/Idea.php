<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Idea extends Model
{
    use HasFactory;
    protected $fillable = ['s_id', 'title', 'description', 'status'];

    public function student() {
        return $this->belongsTo(Student::class, 's_id', 's_id');
    }
    
    public function votes() {
        return $this->hasMany(IdeaVote::class, 'idea_id', 'id');
    }
}
