<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ItemConstants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\StudyServices;
use App\Services\UserServices;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HTTPConstants;

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
            return response()->json(
                ['error' => 'Internal Server Error'], 
                HTTPConstants::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
    
    public function show(Request $request, $orderItemID) 
    {
        $user = $request->get('_user');
        $data = $this->studyServ->getRegisteredItemInfo($user, $orderItemID);
        if (!$data) {
            return response()->json(['error' => 'Item not found!'], HTTPConstants::HTTP_NOT_FOUND);
        }
        $data['activation_info'] = json_decode($data['activation_info'], true) ?? ['code' => $data['activation_info']];
        return response()->json($data);
    }
    
    public function lookup(Request $request)
    {
        try {
            $user = $request->get('_user');
            $data = $request->validate([
                'lookup_date' => 'required|date|date_format:Y-m-d',
                'date_from' => 'required|date|date_format:Y-m-d',
                'date_to' => 'required|date|date_format:Y-m-d',
            ]);

            $plans = $this->studyServ->getSchedulePlans($user, $data['date_from'], $data['date_to']);
            $data['current_plans'] = array_key_exists($data['lookup_date'], $plans) 
               ? $plans[$request->get('lookup_date')] 
               : [];
            $data['schedule_plans'] = $plans;

            return response()->json($data);

        } catch (ValidationException $e) {
            return response()->json(
                ['error' => 'Invalid Arguments'], 
                HTTPConstants::HTTP_UNPROCESSABLE_ENTITY
            );
        } 
    }
}
