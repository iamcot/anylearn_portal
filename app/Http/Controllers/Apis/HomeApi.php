<?php

namespace App\Http\Controllers\APIs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Constants\ConfigConstants;
use App\Models\Ask;
use App\Models\Item;
use App\Models\Configuration;
use App\Services\ItemServices;
use App\Services\J4uServices;
use App\Services\CommonServices;

class HomeApi extends Controller
{
    public function index(Request $request, $role = 'guest') 
    {  
        $homeConfig = config('home_config'); 
        $lastConfig = Configuration::where('key', ConfigConstants::CONFIG_HOME_POPUP)->first();
        if (!empty($lastConfig)) {
            $homePopup = json_decode($lastConfig->value, true);
            if ($homePopup['status'] == 1) {
                $homeConfig['popup'] = $homePopup;
            }
        }

        $bannerConfig = [];
        $newBanners = Configuration::where('key', ConfigConstants::CONFIG_APP_BANNERS)->first();
        if ($newBanners) {
            $bannerConfig = array_values(json_decode($newBanners->value, true));
        }
        
        $data['config'] = $homeConfig;
        $data['banners'] = $bannerConfig;

        $data['asks'] = $this->getLatestQuestion();
        $data['classes'] = $this->getItemsByCategories($request);
        
        $commonS = new CommonServices();
        $data['articles'] = $commonS->getArticles();
        $data['vouchers'] = $commonS->getVoucherEvents();
        $data['promotions'] = $commonS->getPromotions();  
        $data['repurchaseds'] = $data['pointBox'] = $data['j4u'] = []; 
        
        if ($role == 'member') {
            $user = $request->get('_user'); 
            
            $data['pointBox'] = $this->getPointBox($user);
            $data['j4u'] = $commonS->setTemplate('/', 'Có thể bạn sẽ thích', (new J4uServices)->get($user));
            $data['repurchaseds'] = $commonS->setTemplate('/', 'Đăng ký lại', $commonS->getRepurchaseds($user));           
        }

        $data['recommendations'] = $commonS->setTemplate('/', 'anyLEARN đề Xuất', $commonS->getRecommendations());

        return response()->json($data['repurchaseds']);
    }

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

    public function getItemsByCategories(Request $request)
    {
        // Config on admin page
        $configM = new Configuration();
        $isEnableIosTrans = $configM->enableIOSTrans($request);

        $homeClasses = [];
        $homeClassesDb = Configuration::where('key', ConfigConstants::CONFIG_HOME_SPECIALS_CLASSES)->first();

        if ($homeClassesDb) {
            $appLocale = App::getLocale();
            foreach (json_decode($homeClassesDb->value, true) as $block) {
                if (empty($block)) {
                    continue;
                }
                $items = Item::whereIn('id', explode(",", $block['classes']))
                    ->whereNotIn("user_id", $isEnableIosTrans == 0 ? explode(',', env('APP_REVIEW_DIGITAL_SELLERS', '')) : [])
                    ->where('status', 1)
                    ->where('user_status', 1)
                    ->get();
                $homeClasses[] = [
                    'title' => isset($block['title'][$appLocale]) ? $block['title'][$appLocale] : json_encode($block['title']),
                    'classes' => $items
                ];
            }
        }

        return $homeClasses;
    }

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
}

