<?php

namespace App\Services;

use App\Constants\ConfigConstants;
use App\Models\Article;
use App\Models\VoucherEvent;
use Illuminate\Support\Facades\DB;


class CommonServices
{
    public function getArticles() 
    {
        return Article::where('status', 1)
            ->whereIn('type', [Article::TYPE_READ, Article::TYPE_VIDEO])
            ->orderby('id', 'desc')
            ->take(10)
            ->get()
            ->makeHidden(['content']);
    }

    public function getPromotions()
    {
        return Article::where('type', Article::TYPE_PROMOTION)
            ->where('status', 1)
            ->orderby('id', 'desc')
            ->take(5)
            ->get();
    }

    public function getRecommendations()
    {
        return DB::table('orders')
            ->join('order_details as od', 'od.order_id', 'orders.id')
            ->join('items', 'items.id', 'od.item_id')
            ->join('categories', 'categories.id', 'item_category_id')
            ->leftjoin(
                DB::raw('(select item_id, avg(value) as rating from item_user_actions group by(item_id)) as rv'), 
                'rv.item_id',
                'items.id'
            )
            ->leftJoin('transactions', function ($query) {
                $query->on('transactions.order_id', '=', 'orders.id')
                    ->where('transactions.type', '=', ConfigConstants::TRANSACTION_EXCHANGE);
            })
            ->select(
                'items.id',
                'items.title',
                'items.image', 
                'items.price',
                'categories.title as category',
                'rv.rating',
                'items.is_hot',
                'items.boost_score'
            )
            ->orderbyRaw('items.is_hot desc, items.boost_score desc')
            ->get();
    }

    public function getVoucherEvents()
    {
        return VoucherEvent::select('id', 'title')
            ->orderByDesc('id')
            ->take(2)
            ->get();
    }

    public function getRepurchaseds($user) 
    {
        return DB::table('orders')
            ->join('order_details as od', 'od.order_id', '=', 'orders.id')        
            ->join('items', 'items.id', '=', 'od.item_id')
            ->join('items_categories as ic', 'ic.item_id', '=', 'od.item_id')
            ->join('categories', 'categories.id', '=', 'ic.category_id')
            /*->leftjoin(
                DB::raw('(select item_id, avg(value) as rating from item_user_actions where type = "rating" group by(item_id)) as rv'), 
                'rv.item_id',
                'items.id'
            )*/
            ->where('orders.user_id', $user->id)
            ->select(
                'items.id',
                'items.title',
                'items.image',
                'items.price',
                'rv.rating',
                'od.created_at',
                'items.is_hot',
                DB::raw('group_concat(categories.title) as cats')
            )
            ->groupBy('items.id')
            ->orderByRaw('od.created_at desc, items.price desc, items.is_hot desc')
            ->get();
    }

    public function setTemplate($route, $title, $items)
    {
        return [
            'route' => $route,
            'title' => $title,
            'items' => $items
        ];
    }
}
