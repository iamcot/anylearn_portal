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
        $data = [];
        if ($request->all()) {
            $items = DB::table('items')
                ->join('users', 'users.id', '=', 'items.user_id')
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
                $items->where('items.price', '<=', $request->get('price'));
            }

            if ($request->get('provinceCode')) {
                $items->join('user_locations as ul', 'ul.user_id', '=', 'items.user_id');
                $items->where('ul.province_code', $request->get('provinceCode'));
            }

            if ($request->get('search')) {
                $items->where('items.title', 'like', '%'. $request->get('search') . '%');
            }

            $partners = $items->select(
                    'users.id',
                    'users.name',
                    DB::raw('group_concat(items.id) as itemIds')
                )
                ->groupBy('items.user_id')
                ->get();

            foreach($partners as $value) {

                $partner = new \stdClass();
                $partner->id = $value->id;
                $partner->name = $value->name;

                $partner->items = DB::table('items')
                    ->leftjoin(
                        DB::raw('(select item_id, avg(value) as rating from item_user_actions where type = "rating" group by(item_id)) as rv'), 
                        'rv.item_id',
                        'items.id'
                    )
                    ->whereIn('items.id', explode(',', $value->itemIds))
                    ->select(
                        'items.id',
                        'items.title',
                        'items.image',
                        'items.price',
                        'items.is_hot',
                        'items.boost_score',
                        'items.short_content',
                        'rv.rating'
                    )
                    ->orderByRaw('items.is_hot desc, items.boost_score desc') 
                    ->get();

                $data[] = $partner;
            }   
        }

        return response()->json($data);   
    }
}
