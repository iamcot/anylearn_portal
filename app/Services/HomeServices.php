<?php

namespace App\Services;

use App\Constants\ConfigConstants;
use App\Constants\FileConstants;
use App\Constants\ItemConstants;
use App\Constants\NotifConstants;
use App\Constants\OrderConstants;
use App\Constants\UserConstants;
use App\Models\Article;
use App\Models\VoucherEvent;
use App\Models\Configuration;
use App\Models\CourseSeries;
use App\Models\I18nContent;
use App\Models\Item;
use App\Models\ItemActivity;
use App\Models\ItemCategory;
use App\Models\ItemResource;
use App\Models\ItemUserAction;
use App\Models\ItemVideoChapter;
use App\Models\ItemVideoLesson;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Participation;
use App\Models\Schedule;
use App\Models\SocialPost;
use App\Models\Ask;
use App\Models\Spm;
use Aws\Endpoint\Partition;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class HomeServices
{
    public function getPointBox($user) 
    { 
        $goingClass = DB::table('orders')
            ->join('order_details as od', 'od.order_id', 'orders.id') 
            ->join('participations as pa', 'pa.schedule_id', 'od.id')
            ->join('items', 'items.id', 'od.item_id')
            ->where('pa.organizer_confirm', 0)
            ->where('pa.participant_confirm', 0)
            ->where('orders.user_id', $user->id)
            ->orderBy('pa.created_at', 'desc')
            ->first();

        $ratingClass = DB::table('orders')
            ->join('order_details as od', 'od.order_id', 'orders.id')
            ->join('participations as pa', 'pa.schedule_id', 'od.id')
            ->join('items', 'items.id', 'od.item_id')
            ->leftjoin('item_user_actions as iua', 
                function($join) {
                    $join->on('od.item_id', 'iua.item_id');
                    $join->on('orders.user_id', 'iua.user_id');
                }, 
            ) 
            ->where('orders.user_id', $user->id) 
            ->where('pa.participant_confirm', 1)
            ->where('pa.organizer_confirm', 1)
            ->whereNull('iua.id')
            ->select(
                'orders.user_id',
                'items.title',
                'iua.id'
            )
            ->orderByDesc('pa.created_at')
            ->first(); 
        
        $itemS = new ItemServices();
        $goingClass = $goingClass ? $goingClass : $itemS->getLastRegistered($user->id);
        $ratingClass = $ratingClass ? $ratingClass : $itemS->getLastCompleted($user->id);
        
        $data['anypoint']  = $user->wallet_c; 
        $data['goingClass'] = $goingClass ? $goingClass->title : ''; 
        $data['ratingClass'] = $ratingClass ? $ratingClass->title : ''; 

        return $data;
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
            )
            ->distinct('items.id')
            ->orderByDesc('od.created_at')
            ->get();
    }

    public function getRegisteredData($user)
    {
        $data = new \stdClass();
        $registereds = DB::table('orders')
            ->join('order_details as od', 'od.order_id', 'orders.id')
            ->join('items', 'items.id', 'od.item_id')
            ->where('orders.user_id', $user->id)
            ->select(
                'od.item_id as id',
                'item_category_id',
                'subtype',
                'price',
                'ages_min',
                'ages_max'
            )
            ->distinct('od.item_id')
            ->get();

        if ($registereds) {
            $data->registeredIds = [];
            $data->categoryIds = [];
            $data->subtypes = [];

            $data->minPrice = $registereds[0]->price;
            $data->maxPrice = $registereds[0]->price;

            $data->minAge = $registereds[0]->ages_min;
            $data->maxAge = $registereds[0]->ages_max;

            foreach ($registereds as $item) {
                array_push($data->registeredIds, $item->id);

                if (!in_array($item->item_category_id, $data->categoryIds)) {
                    array_push($data->categoryIds, $item->item_category_id);
                }
               
                if (!in_array($item->subtype, $data->subtypes)) {
                    array_push($data->subtypes, $item->subtype);
                }
                
                if ($item->price < $data->minPrice) {
                    $data->minPrice = $item->price;
                }

                if ($item->price > $data->maxPrice) {
                    $data->maxPrice = $item->price;
                }

                if ($item->ages_min < $data->minAge) {
                    $data->minAge = $item->ages_min;
                }

                if ($item->ages_max > $data->maxAge) {
                    $data->maxAge = $item->ages_max;
                }
            }
        }

        return $data;
    }

    public function getJ4u($user)
    {
        $data = [];

        // By list of search log
        /*$searchLog = Spm::join(
            DB::raw('(select distinct ip from spms where user_id = '. $request->get('_user')->id . ') as s2'),
            's2.ip', 
            'spms.ip'
        );*/

        $searchLog = Spm::where('user_id', $user->id)
            ->where('spmc', 'search')
            ->distinct('extra')
            ->pluck('extra');
        
        if ($searchLog) {
            $searchByLog = DB::table('items')
            ->join('categories', 'categories.id', 'item_category_id')
            ->leftjoin(
                DB::raw('(select item_id, avg(value) as rating from item_user_actions group by(item_id)) as rv'), 
                'rv.item_id',
                'items.id'
            )
            ->where('items.status', 1)
            ->where('user_status', 1)
            ->whereIn('items.title', $searchLog)
            ->select(
                'items.id',
                'items.title',
                'items.image',
                'items.price',
                'categories.title as category',
                'rv.rating',
            )
            ->take(3)
            ->get();

            foreach($searchByLog as $value) {
                $data[] = $value;
            }
        }

        // By list of registered items
        $registeredData = $this->getRegisteredData($user);
        if ($registeredData) {
            $searchByRegisteredData = DB::table('items')
            ->join('categories', 'categories.id', 'item_category_id')
            ->leftjoin(
                DB::raw('(select item_id, avg(value) as rating from item_user_actions group by(item_id)) as rv'), 
                'rv.item_id',
                'items.id'
            )
            ->where('items.status', 1)
            ->where('user_status', 1)
            ->whereNotIn('items.id', $registeredData->registeredIds)
            ->whereIn('item_category_id', $registeredData->categoryIds)
            ->whereIn('subtype', $registeredData->subtypes)
            ->where('price', '>=', $registeredData->minPrice)
            ->where('price', '<=', $registeredData->maxPrice);

            if ($registeredData->minAge) {
                $searchByRegisteredData->where('ages_min', '>=', $registeredData->minAge);
            }

            if ($registeredData->maxAge) {
                $searchByRegisteredData->where('ages_max', '<=', $registeredData->maxAge);
            }
             
            $searchByRegisteredData = $searchByRegisteredData->select(
                'items.id',
                'items.title',
                'items.image',
                'items.price',
                'categories.title as category',
                'rv.rating',
            )
            ->take(3)
            ->get();
            
            foreach($searchByRegisteredData as $value) {
                $data[] = $value;
            }
        }
        
        return $data;
    }

    public function getAsk()
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

    public function getVoucherEvents()
    {
        return VoucherEvent::select('id', 'title')
            ->orderByDesc('id')
            ->take(2)
            ->get();
    }

    public function getRecommendations()
    {
        return DB::table('items')
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
            ->get();
    }

    public function setTemplates($route, $title, $items)
    {
        return [
            'route' => $route,
            'title' => $title,
            'items' => $items
        ];
    }

}