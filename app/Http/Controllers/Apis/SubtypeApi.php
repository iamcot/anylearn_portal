<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ConfigConstants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Configuration;
use App\Models\Item;
use App\Models\User;
use App\Services\CommonServices;
use App\Services\J4uServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

class SubtypeApi extends Controller
{
    public function index(Request $request, $name) 
    {
        /*$data['schools'] = DB::table('items')
            ->join('users', 'items.user_id', 'users.id')
            ->where('items.subtype', $name)
            ->where('users.role', 'school')
            ->orderByRaw('users.is_hot desc, users.boost_score desc')
            ->take(5)
            ->pluck('name');

        $user = $request->get('_user');
        $commonS = new CommonServices();

        $data['repurchareds'] = $commonS->setTemplate('/', 'Đăng ký lại', $commonS->getRepurchaseds($user));
        $data['j4u'] =  $commonS->setTemplate('/', 'Có thể bạn quan tâm', (new J4uServices )->get($user));
        $data['vouchers'] = $commonS->setTemplate('/', 'Học bổng', $commonS->getVoucherEvents());
        $data['partners'] = DB::table('users')
            ->join('items', 'items.user_id', 'users.id')
            ->whereIn('role', ['teacher', 'school'])  
            ->where('items.subtype', $name)
            ->select('users.id', 'name', 'users.image')
            ->orderByRaw('users.is_hot desc, users.boost_score desc')
            ->distinct('users.id')
            ->take(6)
            ->get();

        $data['pClasses'] =  DB::table('users')
            ->join('items', 'items.user_id', 'users.id')
            ->whereIn('role', ['teacher', 'school'])  
            ->where('items.subtype', $name)
            ->select('users.id', 'name', 'users.image')
            ->orderByRaw('users.is_hot desc, users.boost_score desc')
            ->distinct('users.id')
            ->take(6)
            ->get();
        
        $pClasses = [];
        foreach($data['partners'] as $item) {
            $tmp = DB::table('items')
                ->where('user_id', $item->id)
                ->where('subtype', $name)
                ->orderByRaw('is_hot desc, boost_score desc')
                ->take(6)
                ->get();

            $pClasses[] = $commonS->setTemplate('/', 'Các lớp học của '. $item->name, $tmp);
        }
        $data['pClasses'] = $pClasses;

        $cClasses = [];
        foreach($data['partners'] as $item) {
            $tmp = DB::table('items')
                ->where('user_id', $item->id)
                ->where('subtype', $name)
                ->orderByRaw('is_hot desc, boost_score desc')
                ->take(6)
                ->get();

            $pClasses[] = $commonS->setTemplate('/', 'Các lớp học của '. $item->name, $tmp);
        }*/
        /*$configM = new Configuration();
        $isEnableIosTrans = $configM->enableIOSTrans($request);

        $categories = [];
        $categoriesDb = Configuration::where('key', ConfigConstants::CONFIG_HOME_SPECIALS_CLASSES)->first();
        if ($categoriesDb) {
            $appLocale = App::getLocale();
            foreach (json_decode($categoriesDb->value, true) as $block) {
                if (empty($block)) {
                    continue;
                }
                $categories[] = isset($block['title'][$appLocale]) ? $block['title'][$appLocale] : json_encode($block['title']);
            }
        }*/
        /*
        $categories = DB::table('items')
            ->where('subtype', $name) 
            ->select('count(items.id) as nums, item_category_id as id')
            ->groupBy('item_category_id')
            ->orderByDesc('nums')
            ->take(5)
            ->get();
        
        /*$cClasses = [];
        foreach($categories as $item) {
            $tmp = DB::table('items')
                ->where('item_category_id', $item->id)
                ->where('subtype', $name)
                ->get();
                
            $cClasses[] = $commonS->setTemplate('/', 'Các lớp học của '. $item->name, $tmp);
        }

        return $categories;*/

        //return $data;

    }

    
}
