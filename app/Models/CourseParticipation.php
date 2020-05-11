<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseParticipation extends Model
{
    protected $fillable = [
        'item_id', 'schedule_id', 'organizer_user_id', 'participant_user_id', 'organizer_confirm', 'participant_confirm', 
        'organizer_comment', 'participant_comment'
    ];
}
