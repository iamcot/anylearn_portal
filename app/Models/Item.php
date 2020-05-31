<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'title', 'type', 'user_id', 'short_content', 'content', 'price', 'org_price',
        'date_start', 'time_start', 'date_end', 'time_end', 'is_hot', 'status', 'seo_title', 'seo_url',
        'seo_desc', 'image', 'location_type', 'location', 'series_id', 'user_status', 'boost_score'
    ];

    public function series()
    {
        return $this->belongsTo('App\Models\CourseSeries', 'series_id', 'id');
    }
}
