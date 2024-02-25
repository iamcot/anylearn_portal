<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ItemConstants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\StudyServices;
use App\Services\UserServices;

class StudyApi extends Controller
{
    private $studyServ;
    private $userServ;

    public function __construct(StudyServices $studyServ, UserServices $userServ) 
    {
        $this->studyServ = $studyServ;
        $this->userServ = $userServ;
    }

    public function index(Request $request) 
    {
        try {    
            $data['user_info'] = $request->get('_user');
            $data['user_accounts'] = $this->userServ->accountC($data['user_info']->id);
            
            if ($request->get('child')) {
                $data['user_info'] = $data['user_accounts']->find($request->get('child')) ?? $data['user_info'];
            }
        
            $data['num_items'] = $this->studyServ->getSchedulePlansForDay($data['user_info'])->count();
            $data['ongoing_items'] = $this->studyServ->getRegisteredItems($data['user_info'], ItemConstants::STATUS_ONGOING);
            $data['upcoming_items'] = $this->studyServ->getRegisteredItems($data['user_info'], ItemConstants::STATUS_UPCOMING);
            $data['completed_items'] = $this->studyServ->getRegisteredItems($data['user_info'], ItemConstants::STATUS_COMPLETED);

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
    
    public function show(Request $request, $orderItemID) 
    {
        $userInfo = $request->get('_user');
        $data = $this->studyServ->getRegisteredItemInfo($userInfo, $orderItemID);
        if (!$data) {
            return response()->json(['error' => 'Item not found'], 404);
        }
        return response()->json($data);
    }
    
    public function lookup(Request $request)
    {
        $lookupDate = $request->get('date');
        $schedulePlans = $this->studyServ
            ->getSchedulePlansForMonth($request->get('_user'), $lookupDate);

        $data['lookup_date'] = $lookupDate;
        $data['schedule_plans'] = $schedulePlans;
        $data['current_plans'] = isset($schedulePlans[$lookupDate]) ? $schedulePlans[$lookupDate] : []; 
        
        return response()->json($data);
    }
}
