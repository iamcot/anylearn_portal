<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ItemConstants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ItemServices;
use App\Services\UserServices;
use Exception;

class StudyApi extends Controller
{
    public function index(Request $request) 
    {
        try {
            $userServ = new UserServices();   
            $data['user_info'] = $request->get('_user');
            $data['user_accounts'] = $userServ->accountC($data['user_info']->id);
            
            if ($request->get('child')) {
                $data['user_info'] = $data['user_accounts']->find($request->get('child')) ?? $data['user_info'];
            }
        
            $itemServ = new ItemServices();
            $data['num_items'] = $itemServ->getSchedulePlans($data['user_info'])->count();
            $data['ongoing_items'] = $itemServ->getRegisteredItems($data['user_info'], ItemConstants::STATUS_STUDYING);
            $data['upcoming_items'] = $itemServ->getRegisteredItems($data['user_info'], ItemConstants::STATUS_UPCOMING);
            $data['completed_items'] = $itemServ->getRegisteredItems($data['user_info'], ItemConstants::STATUS_COMPLETED);

            return response()->json($data);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
    
    public function show(Request $request, $orderItemID) 
    {
        $data['item_info'] = (new ItemServices)
            ->getRegisteredItemInfo($request->get('_user'), $orderItemID);
        if (!$data['item_info']) {
            return response()->json(['error' => 'Item not found'], 404);
        }
    
        return response()->json($data);
    }
    

    public function calendar(Request $request)
    {
        $itemServ = new ItemServices();
        $userInfo = $request->get('_user');
        
        $data['school_days'] = $itemServ->getSchoolDays($userInfo, $request->get('date'));
        $data['schedule_plans'] = $itemServ->getSchedulePlans($userInfo, $request->get('date'));

        return response()->json($data);
    }
}
