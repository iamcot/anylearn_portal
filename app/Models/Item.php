<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'title', 'type', 'subtype', 'user_id', 'short_content', 'content', 'price', 'org_price', 'commission_rate', 'got_bonus',
        'date_start', 'time_start', 'date_end', 'time_end', 'is_hot', 'status', 'seo_title', 'seo_url',
        'seo_desc', 'image', 'location_type', 'location', 'series_id', 'user_status', 'boost_score', 'item_category_id',
        'is_test', 'tags', 'nolimit_time', 'company_commission', 'item_id', 'user_location_id',
        'sale_id', 'is_paymentfee',
    ];

    protected $hidden = [
        'content'
    ];

    public function series()
    {
        return $this->belongsTo('App\Models\CourseSeries', 'series_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
