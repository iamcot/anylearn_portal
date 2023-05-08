<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ConfigConstants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Configuration;
use App\Models\User;
use App\Services\CommonServices;
use App\Services\J4uServices;
use Illuminate\Support\Facades\DB;

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

        $categories = [];
        $categoriesDb = Configuration::where('key', ConfigConstants ::CONFIG_HOME_SPECIALS_CLASSES)->first();
        foreach($categoriesDb as $value) {
            $categories[] = $categoriesDb->title;
        }
       return $categories;

       //return $data;

    }

    
}
