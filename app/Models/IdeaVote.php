<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IdeaVote extends Model
{
    use HasFactory;
    protected $fillable = ['idea_id', 's_id'];
}
