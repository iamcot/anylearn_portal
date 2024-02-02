<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ItemConstants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ItemServices;
use App\Services\UserServices;

class StudyApi extends Controller
{
    public function index(Request $request) {
        $user = $request->get('_user');
        $userServ = new UserServices();
        
        $data['user_info'] = $user;
        $data['user_accounts'] = $userServ->accountC($user->id);
    
        $itemServ = new ItemServices();
        $data['num_items'] = $itemServ->getSchedulePlans($user->id)->count();
        $data['ongoing_items'] = $itemServ->getRegisteredItems($user->id, ItemConstants::STATUS_STUDYING);
        $data['upcoming_items'] = $itemServ->getRegisteredItems($user->id, ItemConstants::STATUS_UPCOMING);
        $data['completed_items'] = $itemServ->getRegisteredItems($user->id, ItemConstants::STATUS_COMPLETED);

        return response()->json($data);
    }
    
    public function show(Request $request, $orderItemID) 
    {
        $user = $request->get('_user');        
        $data['item_info'] = (new ItemServices)->getRegisteredItemInfo($user->id, $orderItemID);
        if (!$data['item_info']) {
            return response()->json(['error' => 'Item not found'], 404);
        }
    
        return response()->json($data);
    }
    

    public function calendar(Request $request)
    {
        $user = $request->get('_user');
        $itemServ = new ItemServices();
        $data['school_days'] = $itemServ->getSchoolDays($user->id, $request->get('date'));
        $data['schedule_plans'] = $itemServ->getSchedulePlans($user->id, $request->get('date'));

        return response()->json($data);
    }
}
