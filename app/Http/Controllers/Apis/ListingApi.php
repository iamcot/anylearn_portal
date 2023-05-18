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

            if ($request->get('category')) {
                $items->join('items_categories as ic', 'ic.item_id', '=', 'items.id');
                $items->where('ic.category_id', $request->get('category'));
            }

            if ($request->get('price')) {
                $items->where('items.price', '<=', $request->get('price'));
            }

            if ($request->get('province')) {
                $items->join('user_locations as ul', 'ul.user_id', '=', 'items.user_id');
                $items->where('ul.province_code', $request->get('province'));
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
                        'items.created_at',
                        'rv.rating'
                    );

                if ($request->get('sort') == 'alphabet-asc'){
                    $partner->items->orderBy('items.title');
                }

                if ($request->get('sort') == 'alphabet-desc'){
                    $partner->items->orderByDesc('items.title');
                }

                if ($request->get('sort') == 'date-asc') {
                    $partner->items->orderBy('items.created_at');
                }

                if ($request->get('sort') == 'date-desc') {
                    $partner->items->orderByDesc('items.created_at');
                }

                if ($request->get('sort') == 'hot-asc') {
                    $partner->items->orderBy('items.is_hot');
                }

                if ($request->get('sort') == 'hot-desc') {
                    $partner->items->orderByDesc('items.is_hot');
                }

                if ($request->get('sort') == 'price-asc') {
                    $partner->items->orderBy('items.price');
                }

                if ($request->get('sort') == 'price-desc') {
                    $partner->items->orderByDesc('items.price');
                }

                if ($request->get('sort') == 'rating-asc') {
                    $partner->items->orderBy('rv.rating');
                }
                
                if ($request->get('sort') == 'rating-desc') {
                    $partner->items->orderByDesc('rv.rating');
                }

                $partner->items = $partner->items->take(3)->get(); 
                $data[] = $partner;
            }   
        }

        return response()->json($data);   
    }
}
