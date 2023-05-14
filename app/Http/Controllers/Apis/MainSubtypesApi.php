<?php

namespace App\Http\Controllers\APIs;

use App\Constants\ItemConstants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Services\CommonServices;
use App\Services\ItemServices;
use App\Services\J4uServices;
use App\Services\UserServices;
use App\Services\VoucherServices;

class MainSubtypesApi extends Controller
{
    public function index(Request $request, $subtype, $role='guest') 
    { 
        if (in_array($subtype, config('subtype_list'))) {
            
            $data['schools'] = config('subtype_categories')[$subtype];
            $data['partners'] = (new UserServices)->getPartnersBySubtype($subtype);
            $data['vouchers'] = (new VoucherServices)->getVoucherEventsBySubtype($subtype);

            $itemS = new ItemServices();
            $data['partnerItems'] = $itemS->getItemsByPartners($data['partners'], $subtype);
            $data['categoryItems'] = $itemS->getItemsByCategories($itemS->getCategoriesBySubtype($subtype), $subtype);
            
            if ($role == 'member') {
                $user = $request->get('_user'); 
                $commonS = new CommonServices();
                $data['j4u'] = $commonS->setTemplate('/', 'Có thể bạn quan tâm', (new J4uServices)->get($user, $subtype));
                $data['repurchaseds'] = $commonS->setTemplate('/', 'Đăng ký lại', $commonS->getRepurchasedsBySubtype($user, $subtype));           
            }

            return $data;
        }
    }
}

