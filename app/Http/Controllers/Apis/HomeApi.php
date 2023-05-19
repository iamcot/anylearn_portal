<?php

namespace App\Http\Controllers\APIs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Constants\ConfigConstants;
use App\Models\Configuration;
use App\Services\ArticleServices;
use App\Services\ItemServices;
use App\Services\J4uServices;
use App\Services\CommonServices;
use App\Services\UserServices;
use App\Services\VoucherServices;

class HomeApi extends Controller
{
    public function index(Request $request) 
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
        
        $articleS = new ArticleServices();
        $data['articles'] = $articleS->getArticles();
        $data['promotions'] = $articleS->getPromotions(); 

        $commonS = new CommonServices();
        $data['asks'] = $commonS->getLatestQuestion();
        $data['recommendations'] = $commonS->setTemplate('/', 'anyLEARN đề Xuất', $commonS->getRecommendations());

        $data['classes'] = (new ItemServices)->getConfigItemsByCategories($request);
        $data['vouchers'] = (new VoucherServices)->getVoucherEvents();

        $user = $request->get('_user');
        $data['repurchases'] = $data['pointBox'] = $data['j4u'] = []; 

        if ($user) {          
            $data['pointBox'] = (new UserServices)->getPointBox($user);
            $data['j4u'] = $commonS->setTemplate('/', 'Có thể bạn sẽ thích', (new J4uServices)->get($user));
            $data['repurchases'] = $commonS->setTemplate('/', 'Đăng ký lại', $commonS->getRepurchases($user));           
        }

        return response()->json($data);
    }
}

