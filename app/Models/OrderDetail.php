<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderDetail extends Model
{
    protected $fillable = [
        'user_id', 'item_id', 'unit_price', 'paid_price', 'quanity', 'status', 'order_id'
    ];

    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id')->where('status', 1)->where('user_status', 1);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')->where('status', 1);
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }

    public function userRegistered($userId)
    {
        $today = date('Y-m-d');
        $query = DB::table('order_details AS od')
            ->join('items', 'items.id', '=', 'od.item_id')
            ->join('users', 'users.id', '=', 'od.user_id')
            ->join('schedules', 'schedules.item_id', '=', 'od.item_id')
            ->leftJoin('participations AS pa', 'pa.schedule_id', '=', 'schedules.id')
            ->where('od.user_id', $userId)
            ->where('items.status', '>', 0)
            ->where('users.status', '>', 0)
            ->where('items.user_status', '>', 0)
            ->select('schedules.id', 'items.title', 'schedules.date as date', 'schedules.time_start as time', 
            'schedules.time_end', 'items.short_content as content', 'pa.id AS user_joined', 'items.user_status as author_status',
            'items.location')
            ->orderBy('schedules.date')
            ->orderBy('schedules.time_start');
        $query2 = clone $query;
        $done = $query->where('items.date_start', '<', $today)->get();
        $open = $query2->where('items.date_start', '>=', $today)->get();
        return [
            'done' => $done,
            'open' => $open,
        ];
    }
}
