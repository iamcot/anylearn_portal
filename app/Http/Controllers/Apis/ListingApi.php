<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ConfigConstants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Spm;
use App\Services\CommonServices;
use Illuminate\Support\Facades\DB;

class ListingApi extends Controller
{
    public function index(Request $request) 
    {
        $data = new \stdClass();
        if ($request->get('page')) {
            $partners = (new CommonServices)
                ->getSearchResults($request)
                ->paginate($request->get('size', ConfigConstants::CONFIG_NUM_PAGINATION), ['*'], 'page', $request->get('page'));

            $data->numPage = ceil($partners->total() / $request->get('size', ConfigConstants::CONFIG_NUM_PAGINATION));
            $data->currentPage = (int) $request->get('page');
            
            foreach($partners->items() as $value) {
                $partner = new \stdClass();
                $partner->id = $value->id;
                $partner->name = $value->name;
                $partner->image = $value->image; 

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

                $partner->items = $partner->items->take(ConfigConstants::CONFIG_NUM_ITEM_DISPLAY)->get(); 
                $data->searchResults[] = $partner;
            } 
        }

        $spm = new Spm();
        $spm->addSpm($request);

        return response()->json($data);   
    }
}
