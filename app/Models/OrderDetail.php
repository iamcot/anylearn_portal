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
            ->leftJoin('participations AS pa', function ($join) {
                $join->on('pa.schedule_id', '=', 'schedules.id')
                    ->on('pa.participant_user_id', '=', 'users.id');
            })
            ->leftJoin('item_user_actions AS iua', function ($query) {
                $query->whereRaw('iua.item_id = items.id AND iua.user_id = users.id AND iua.type=?', [ItemUserAction::TYPE_RATING]);
            })
            ->where('od.user_id', $userId)
            ->where('items.status', '>', 0)
            ->where('users.status', '>', 0)
            ->where('items.user_status', '>', 0)
            ->select(
                'schedules.id',
                'items.title',
                'schedules.date as date',
                'schedules.time_start as time',
                'schedules.time_end',
                'items.short_content as content',
                'pa.id AS user_joined',
                'items.user_status as author_status',
                'items.location',
                'items.id as item_id',
                'items.nolimit_time',
                DB::raw('CASE WHEN iua.value IS  NULL THEN 0 ELSE iua.value END AS user_rating')
            )
            ->orderBy('schedules.date')
            ->orderBy('schedules.time_start');
        $query2 = clone $query;
        // $done = $query->where('items.date_start', '<', $today)->get();
        $done = $query->whereNotNull('pa.id')->get();
        // $open = $query2->where('items.date_start', '>=', $today)->get();
        $open = $query2->whereNull('pa.id')->get();

        $fav = DB::table('item_user_actions AS iua')
            ->join('items', 'items.id', '=', 'iua.item_id')
            ->leftJoin('order_details AS od', 'od.item_id', '=', 'iua.item_id')
            ->whereNull('od.id')
            ->where('iua.user_id', $userId)
            ->where('items.status', '>', 0)
            ->where('items.user_status', '>', 0)
            ->where('iua.type', ItemUserAction::TYPE_FAV)
            ->where('iua.value', ItemUserAction::FAV_ADDED)
            ->select('items.id AS item_id', 'items.title', 'items.date_start as date', 'items.time_start as time')
            ->orderBy('iua.id', 'desc')
            ->get();
        return [
            'done' => $done,
            'open' => $open,
            'fav' => $fav,
        ];
    }
}
