<?php

namespace App\Services;

use App\Constants\OrderConstants;
use App\Models\Ask;
use Illuminate\Support\Facades\DB;

class CommonServices
{
    public function getLatestQuestion()
    {
        $data = [];
        $question = Ask::where('type', 'question')->orderbyDesc('id')->first();

        if ($question) {
            $data['question'] = $question->content;
            $data['comments'] = Ask::where('ask_id', $question->id)->where('type', 'comment')->get();
            $data['answers'] = Ask::where('ask_id', $question->id)->where('type', 'answer')->get();
        }

        return $data;
    }

    public function getRecommendations()
    {
        return DB::table('orders')
            ->join('order_details as od', 'od.order_id', '=', 'orders.id')        
            ->join('items', 'items.id', '=', 'od.item_id')
            ->join('items_categories as ic', 'ic.item_id', '=', 'items.id')
            ->join('categories', 'categories.id', '=', 'ic.category_id')
            ->leftjoin(
                DB::raw('(select item_id, avg(value) as rating from item_user_actions where type = "rating" group by(item_id)) as rv'), 
                'rv.item_id',
                'items.id'
            )
            ->select(
                'items.id',
                'items.title',
                'items.image',
                'items.price',
                'items.is_hot',
                'rv.rating',
                DB::raw('max(od.created_at) as created_at'),
                DB::raw('group_concat(categories.title) as categories')
            )
            ->groupBy('items.id')
            ->orderbyRaw('items.is_hot desc, items.boost_score desc')
            ->take(10)
            ->get();
    }

    public function getRepurchases($user) 
    {
        return DB::table('orders')
            ->join('order_details as od', 'od.order_id', '=', 'orders.id')        
            ->join('items', 'items.id', '=', 'od.item_id')
            ->join('items_categories as ic', 'ic.item_id', '=', 'items.id')
            ->join('categories', 'categories.id', '=', 'ic.category_id')
            ->leftjoin(
                DB::raw('(select item_id, avg(value) as rating from item_user_actions where type = "rating" group by(item_id)) as rv'), 
                'rv.item_id',
                'items.id'
            )
            ->where('orders.user_id', $user->id)
            ->select(
                'items.id',
                'items.title',
                'items.image',
                'items.price',
                'items.is_hot',
                'rv.rating',
                DB::raw('max(od.created_at) as created_at'),
                DB::raw('group_concat(categories.title) as categories')
            )
            ->groupBy('items.id')
            ->orderByRaw('items.is_hot desc, items.price desc')
            ->take(10)
            ->get(); 
    }

    public function getRepurchasesbySubtype($user, $subtype) 
    {
        return DB::table('orders')
            ->join('order_details as od', 'od.order_id', '=', 'orders.id')        
            ->join('items', 'items.id', '=', 'od.item_id')
            ->join('items_categories as ic', 'ic.item_id', '=', 'items.id')
            ->join('categories', 'categories.id', '=', 'ic.category_id')
            ->leftjoin(
                DB::raw('(select item_id, avg(value) as rating from item_user_actions where type = "rating" group by(item_id)) as rv'), 
                'rv.item_id',
                'items.id'
            )
            ->where('orders.user_id', $user->id)
            ->where('items.subtype', $subtype)
            ->select(
                'items.id',
                'items.title',
                'items.image',
                'items.price',
                'items.is_hot',
                'rv.rating',
                DB::raw('max(od.created_at) as created_at'),
                DB::raw('group_concat(categories.title) as categories')
            )
            ->groupBy('items.id')
            ->orderByRaw('items.is_hot desc, items.price desc')
            ->take(10)
            ->get(); 
    }

    public function setTemplate($route, $title, $items)
    {
        foreach ($items as $item) {
            $item->categories = array_unique(explode(',', $item->categories));
        }

        return [
            'route' => $route,
            'title' => $title,
            'items' => $items
        ];
    }

}
