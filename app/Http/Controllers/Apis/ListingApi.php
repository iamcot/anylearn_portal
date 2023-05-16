<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ItemConstants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ListingApi extends Controller
{
    public function index(Request $request) 
    {
        if ($request->all()) {
            $items = DB::table('items')
                ->where('items.status', ItemConstants::STATUS_ACTIVE)
                ->where('items.user_status', ItemConstants::USERSTATUS_ACTIVE)
                ->whereNull('items.item_id');

            if ($request->get('subtype')) {
                $items->where('items.subtype', $request->get('subtype'));
            }

            if ($request->get('categoryId')) {
                $items->where('items.item_category_id', $request->get('categoryId'));
            }

            if ($request->get('price')) {
                $items->where('items.price', $request->get('price'));
            }

            if ($request->get('provinceCode')) {
                $items->join('user_locations as ul', 'ul.user_id', '=', 'items.user_id');
                $items->where('ul.province_code', $request->get('provinceCode'));
            }

            if ($request->get('search')) {
                $items->where('items.title', 'like', '%'. $request->get('search') . '%');
            }
        }

        $items = $items->select(
            'items.user_id',
             DB::raw('group_concat(items.id) as itemIds')
        )
        ->groupBy('items.user_id')
        ->get();

        return response()->json($data);
    }
}
