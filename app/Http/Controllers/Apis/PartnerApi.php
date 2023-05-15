<?php

namespace App\Http\Controllers\APIs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherEvent;
use App\Services\ItemServices;
use Illuminate\Support\Facades\DB;

class PartnerApi extends Controller
{
    public function index(Request $request, $id) 
    {
        $data['partner'] = $this->getPartner($id);
        if ($data['partner']) {

            $sumReviews = DB::table('items')
            ->join('item_user_actions as iua', 'iua.item_id', '=', 'items.id')
            ->where('items.user_id', $id)
            ->where('iua.type', 'rating')
            ->select(
                DB::raw('count(iua.id) as reviews'),
                DB::raw('avg(iua.value) as rating')
            )
            ->get();

            $data['sumRating'] = $sumReviews[0]->rating;
            $data['sumReviews']= $sumReviews[0]->reviews;
            
            $itemS = new ItemServices();
            $data['hotItems'] = $itemS->getItemsByPartner($id, 1);
            $data['normalItems'] = $itemS->getItemsByPartner($id);
            $data['reviews'] = $itemS->getItemReviewsByPartner($id);
            $data['vourcher'] = $this->getVoucherByPartner($id); 
        
            return response()->json($data);
        }
        
        return response()->json(404);
        
    }

    public function getPartner($id)
    {
        return DB::table('users')
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
    }

    public function getVoucherByPartner($id) 
    {
        $voucherE = VoucherEvent::where('trigger', $id)->first();
        return $voucherE ? Voucher::whereIn('voucher_group_id', explode(',', $voucherE->targets))->first() : '';
    }
}

