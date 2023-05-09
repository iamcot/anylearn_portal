<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Spm;
use Carbon\Carbon;

class J4uServices
{
    public function collect($user)
    {
        $data = new \stdClass();
        
        // Get data from search log      
        $data->searchLogs = Spm::where('user_id', $user->id)
            ->where('spmc', 'search')
            ->distinct('extra')
            ->pluck('extra');

        // Get data from registered items 
        $registereds = DB::table('orders')
            ->join('order_details as od', 'od.order_id', 'orders.id')
            ->join('items', 'items.id', 'od.item_id')
            ->where('orders.user_id', $user->id)
            ->select(
                'od.item_id as id',
                'item_category_id as categoryId',
                'ages_min as minAge',
                'ages_max as maxAge',
                'subtype',
                'price'
            )
            ->distinct('od.item_id')
            ->get();
            
        if ($registereds) {
            $data->categoryIds = [];
            $data->subtypes = [];

            $data->minAge = $registereds[0]->minAge;
            $data->maxAge = $registereds[0]->maxAge;

            $data->minPrice = $registereds[0]->price;
            $data->maxPrice = $registereds[0]->price;
                
            foreach ($registereds as $item) {
                $data->itemIds[] = $item->id; 
    
                if (!in_array($item->categoryId, $data->categoryIds)) {
                    $data->categoryIds[] = $item->categoryId;
                }
                   
                if (!in_array($item->subtype, $data->subtypes)) {
                    $data->subtypes[] = $item->subtype;
                }            
                    
                if (isset($item->minAge) && $item->minAge < $data->minAge) {
                    $data->minAge = $item->minAge;
                }
    
                if (isset($item->maxAge) && $item->maxAge > $data->maxAge) {
                    $data->maxAge = $item->maxAge;
                }

                if ($item->price < $data->minPrice) {
                    $data->minPrice = $item->price;
                }
    
                if ($item->price > $data->maxPrice) {
                    $data->maxPrice = $item->price;
                }
            }
        }
    
        // Get data from dob - subaccounts 
        $dob = DB::table('users')
            ->where('user_id', $user->id)
            ->selectRaw('max(dob) as minAge, min(dob) as maxAge')
            ->groupBy('user_id')
            ->first();     
        
        if ($dob) {
            $dob->minAge = isset($dob->minAge) ? Carbon::now()->diffInYears($dob->minAge) : null;
            $dob->maxAge = isset($dob->maxAge) ? Carbon::now()->diffInYears($dob->maxAge) : null;
            
            if (isset($data->minAge, $dob->minAge) && $dob->minAge < $data->minAge) {
                $data->minAge = $dob->minAge;
            }

            if (isset($data->maxAge, $dob->maxAge) && $dob->minAge < $data->minAge) {
                $data->maxAge = $dob->maxAge;
            }

            if (!isset($data->minAge) && isset($dob->minAge)) {
                $data->minAge = $dob->minAge;
            }

            if (!isset($data->maxAge) && isset($dob->maxAge)) {
                $data->maxAge = $dob->maxAge;
            }  
 
            $data->minAge = isset($data->minAge) ? $data->minAge : 0;
            $data->maxAge = isset($data->maxAge) ? $data->maxAge : 60;
        }

        // Get data from user location
        $data->location = $user->address;

        return $data;
    }

    public function get($user) 
    {
        $data = $this->collect($user);
        
        $items = DB::query();
        $items->fromRaw(DB::raw('(select * from items where user_status = 1 && status = 1) as items'))
            ->join('categories', 'categories.id', 'item_category_id')
            ->leftjoin(
                DB::raw('(select item_id, avg(value) as rating from item_user_actions group by(item_id)) as rv'), 
                'rv.item_id',
                'items.id'
            );
 
        foreach($data->searchLogs as $value) {
            $items = $items->orwhere('items.title', 'like', '%' . $value . '%');
        }

        $items = $items->orwhere(function ($query) use ($data) {
            $query->whereNotIn('items.id', $data->itemIds)
                ->whereIn('item_category_id', $data->categoryIds)
                ->whereIn('subtype', $data->subtypes)
                ->where('price', '>=', $data->minPrice)
                ->where('price', '<=', $data->maxPrice)
                ->where('ages_min', '>=', $data->minAge)
                ->where('ages_max', '<=', $data->maxAge);
            })
            ->select(
                'items.id',
                'items.title',
                'items.image',
                'items.price',
                'categories.title as category',
                'rv.rating'
            )
            ->distinct('items.id')
            ->orderByRaw('is_hot desc, boost_score desc, items.created_at desc')   
            ->take(10)
            ->get();

        // Location

        return $items;
    }
}
