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
                $items->join('items_categories as ic', 'ic.item_id', '=', 'items.id');
                $items->where('ic.category_id', $request->get('categoryId'));
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

                if ($request->get('sortBy') == 'alphabetASC'){
                    $partner->items->orderBy('items.title');
                }

                if ($request->get('sortBy') == 'alphabetDESC'){
                    $partner->items->orderByDesc('items.title');
                }

                if ($request->get('sortBy') == 'dateASC') {
                    $partner->items->orderBy('items.created_at');
                }

                if ($request->get('sortBy') == 'dateDESC') {
                    $partner->items->orderByDesc('items.created_at');
                }

                if ($request->get('sortBy') == 'hotASC') {
                    $partner->items->orderBy('items.is_hot');
                }

                if ($request->get('sortBy') == 'hotDESC') {
                    $partner->items->orderByDesc('items.is_hot');
                }

                if ($request->get('sortBy') == 'priceASC') {
                    $partner->items->orderBy('items.price');
                }

                if ($request->get('sortBy') == 'priceDESC') {
                    $partner->items->orderByDesc('items.price');
                }

                if ($request->get('sortBy') == 'ratingASC') {
                    $partner->items->orderBy('rv.rating');
                }
                
                if ($request->get('sortBy') == 'ratingDESC') {
                    $partner->items->orderByDesc('rv.rating');
                }

                $partner->items = $partner->items->take(3)->get(); 
                $data[] = $partner;
            }   
        }

        return response()->json($data);   
    }
}
