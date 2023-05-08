<?php

namespace App\Services;

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
        /*$orders->leftJoin('vouchers_used', 'vouchers_used.order_id', '=', 'orders.id')
            ->leftJoin('vouchers', 'vouchers_used.voucher_id', '=', 'vouchers.id')
            ->leftJoin('transactions', function ($query) {
                $query->on('transactions.order_id', '=', 'orders.id')
                    ->where('transactions.type', '=', ConfigConstants::TRANSACTION_EXCHANGE);
            })
            ->select(
                'orders.*',
                'users.name',
                'users.phone',
                'users.address',
                'vouchers.voucher',
                'vouchers.value AS voucher_value',
                'transactions.amount AS anypoint',
                DB::raw("(SELECT GROUP_CONCAT(items.title SEPARATOR ',' ) as classes FROM order_details AS os JOIN items ON items.id = os.item_id WHERE os.order_id = orders.id) as classes")
            )->orderby('orders.id', 'desc')*/
            
        return DB::table('orders')
         
            ->join('order_details as od', 'od.order_id', 'orders.id')
            ->join('items', 'items.id', 'od.item_id')
            ->join('categories', 'categories.id', 'item_category_id')
            ->leftjoin(
                DB::raw('(select item_id, avg(value) as rating from item_user_actions group by(item_id)) as rv'), 
                'rv.item_id',
                'items.id'
            )
            ->select(
                'items.id',
                'items.title',
                'items.image',
                'items.price',
                'categories.title as category',
                'rv.rating',
                'od.created_at'
            ) 
            ->distinct('items.id')
            ->orderByRaw('is_hot desc, boost_score desc')
            ->get();
        /*return DB::table('items')
            ->join('categories', 'categories.id', 'item_category_id')
            ->leftjoin(
                DB::raw('(select item_id, avg(value) as rating from item_user_actions group by(item_id)) as rv'), 
                'rv.item_id',
                'items.id'
            )
            ->select(
                'items.id',
                'items.title as title',
                'items.image',
                'items.price',
                'categories.title as category',
                'rv.rating',
            )
            ->where('is_hot', 1)
            ->orderByDesc('boost_score')
            ->take(10)
            ->get();*/
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
            ->join('order_details as od', 'od.order_id', 'orders.id')
            ->join('items', 'items.id', 'od.item_id')
            ->join('categories', 'categories.id', 'item_category_id')
            ->leftjoin(
                DB::raw('(select item_id, avg(value) as rating from item_user_actions group by(item_id)) as rv'), 
                'rv.item_id',
                'items.id'
            )
            ->where('orders.user_id', $user->id)
            ->select(
                'items.id',
                'items.title',
                'items.image',
                'items.price',
                'categories.title as category',
                'rv.rating',
                'od.created_at'
            ) 
            ->distinct('items.id')
            ->orderByRaw('od.created_at desc, price desc, is_hot desc')
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
