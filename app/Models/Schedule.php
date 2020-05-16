<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'item_id', 'date', 'status', 'content', 'time_start', 'time_end'
    ];
}
