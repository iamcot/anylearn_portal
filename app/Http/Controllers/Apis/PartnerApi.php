<?php

namespace App\Http\Controllers\APIs;

use App\Constants\ItemConstants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherEvent;

use Illuminate\Support\Facades\DB;

class PartnerApi extends Controller
{
    public function index(Request $request, $id) 
    {
        $data = [];
        $user = DB::table('users')
            ->whereIn('role', ['teacher', 'school'])
            ->where('users.id', $id)
            ->select(
                'users.id',
                'users.name',
                'users.image',
                'users.banner',
                'users.introduce'
            )
            ->first();
        
        if ($user) {
            $sumReviews =  DB::table('items')
            ->join('item_user_actions as iua', 'iua.item_id', '=', 'items.id')
            ->where('items.user_id', $id)
            ->where('iua.type', 'rating')
            ->select(
                DB::raw('count(iua.id) as reviews'),
                DB::raw('avg(iua.value) as rating')
            )
            ->get();

            $data['partner'] = $user;
            $data['sumRating'] = $sumReviews[0]->rating;
            $data['sumReviews']= $sumReviews[0]->reviews;
            


            $data['hotItems'] = DB::table('items')
                ->join('users', 'users.id', '=', 'items.user_id')
                ->where('items.status', ItemConstants::STATUS_ACTIVE)
                ->where('items.user_status', ItemConstants::USERSTATUS_ACTIVE)
                ->where('items.is_hot', 1)
                ->select(
                    'items.id',
                    'items.image',
                    'items.short_content',
                    'items.boost_score',
                    'items.created_at'
                )
                ->orderByRaw('items.boost_score desc', 'items.created_at desc')
                ->take(4)
                ->get();

            $data['normalItems'] = DB::table('items')
                ->join('users', 'users.id', '=', 'items.user_id')
                ->where('items.status', ItemConstants::STATUS_ACTIVE)
                ->where('items.user_status', ItemConstants::USERSTATUS_ACTIVE)
                ->where('items.is_hot', '!=', 1)
                ->select(
                    'items.id',
                    'items.title',
                    'items.image',
                    'items.short_content',
                    'items.boost_score',
                    'items.created_at'
                )
                ->orderByRaw('items.boost_score desc', 'items.created_at desc')
                ->take(4)
                ->get(); 
            
            $voucherE = VoucherEvent::where('trigger', $id)->first();
            if ($voucherE) {
                $data['voucher'] = Voucher::whereIn('voucher_group_id', explode(',', $voucherE->targets))->first();
            }

            
            
            $data['reviews'] = DB::table('items')
                ->join('item_user_actions as iua', 'iua.item_id', '=', 'items.id')
                ->where('items.user_id', $id)
                ->where('iua.type', 'rating')
                ->select(
                    'iua.*'
                )
                ->orderByDesc('iua.created_at')
                ->take(3)
                ->get();

            return $data;
        }
    }
}
