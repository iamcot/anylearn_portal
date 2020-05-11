<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseResource extends Model
{
    protected $fillable = [
        'item_id', 'type', 'title', 'desc', 'data', 
    ];
}
