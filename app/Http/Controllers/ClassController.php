<?php

namespace App\Http\Controllers;

use App\Constants\ConfigConstants;
use App\Constants\ItemConstants;
use App\Constants\UserConstants;
use App\Models\Configuration;
use App\Models\Item;
use App\Models\ItemResource;
use App\Models\Schedule;
use App\Models\User;
use App\Models\UserLocation;
use App\Services\ItemServices;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;
use Vanthao03596\HCVN\Models\Province;

class ClassController extends Controller
{
    public function list(Request $request)
    {
        $user = Auth::user();
        $classService = new ItemServices();
        $this->data['navText'] = __('Quản lý lớp học');
        $this->data['courseList'] = $classService->itemList($request, in_array($user->role, UserConstants::$modRoles) ? null : $user->id, ItemConstants::TYPE_CLASS);
        return view('class.list', $this->data);
    }

    public function create(Request $request)
    {
        $courseService = new ItemServices();
        if ($request->input('action') == 'create') {
            $input = $request->all();
            $rs = $courseService->createItem($input, ItemConstants::TYPE_CLASS);
            if ($rs === false || $rs instanceof Validator) {
                return redirect()->back()->withErrors($rs)->withInput()->with('notify', __('Tạo lớp học thất bại! Vui lòng kiểm tra lại dữ liệu'));
            } else {
                return redirect()->route('class.edit', ['id' => $rs])->with(['tab' => 'schedule', 'notify' => __('Tạo lớp học thành công, vui lòng cập nhật lịch học.')]);
            }
        }
        $configM = new Configuration();
        $this->data['configs'] = $configM->gets([
        ConfigConstants::CONFIG_DISCOUNT, 
        ConfigConstants::CONFIG_COMMISSION, 
        ConfigConstants::CONFIG_COMMISSION_FOUNDATION]);
        $this->data['companyCommission'] = null;
        $this->data['isSchool'] = false;
        $this->data['navText'] = __('Tạo lớp học');
        $this->data['hasBack'] = route('class');
        return view('class.edit', $this->data);
    }

    public function edit(Request $request, $courseId)
    {
        $courseService = new ItemServices();
        if ($request->input('action') == 'update') {
            $input = $request->all();
            try {
                $rs = $courseService->updateItem($request, $input);
            } catch (Exception $e) {
                return redirect()->back()->with(['tab' => $input['tab'], 'notify' => $e->getMessage()]);
            }
            
            if ($rs === false || $rs instanceof Validator) {
                return redirect()->back()->withErrors($rs)->withInput()->with(['tab' => $input['tab'], 'notify' => __('Sửa lớp học thất bại! Vui lòng kiểm tra lại dữ liệu')]);
            } else {
                return redirect()->back()->with(['notify' => $rs, 'tab' => $input['tab']]);
            }
        }
        $courseDb =  $courseService->itemInfo($courseId);
        if (empty($courseDb)) {
            return redirect()->route('class')->with('notify', __('Lớp học không tồn tại'));
        }
        $author = User::find($courseDb['info']->user_id);
        if ($author->role == UserConstants::ROLE_SCHOOL) {
            $this->data['isSchool'] = true;
            $this->data['teachers'] = DB::table('users')
                ->leftjoin('class_teachers AS ct', function ($join) use ($courseDb) {
                    $join->on('ct.user_id', '=', 'users.id');
                    $join->on('ct.class_id', '=', DB::raw($courseDb['info']->id));
                })

                ->where('users.user_id', $courseDb['info']->user_id)
                ->where('users.role', UserConstants::ROLE_TEACHER)
                ->select('users.*', 'ct.id AS isSelected')
                ->get();
        } else {
            $this->data['isSchool'] = false;
        }
        $configM = new Configuration();
        $this->data['configs'] = $configM->gets([
        ConfigConstants::CONFIG_DISCOUNT, 
        ConfigConstants::CONFIG_COMMISSION, 
        ConfigConstants::CONFIG_COMMISSION_FOUNDATION]);

        $this->data['companyCommission'] = json_decode($courseDb['info']->company_commission, true);
        $userLocations = UserLocation::where('user_id', $author->id)->orderby('is_head', 'desc')->get();
        $this->data['userLocations'] = $userLocations;
        $this->data['openings'] = DB::table('items')
        ->join('user_locations', 'user_locations.id', '=', 'user_location_id')
        ->where('item_id', $courseId)
        ->select('items.title', 'items.id', 'user_locations.title AS location', 'items.user_status', 'items.date_start')
        ->get();

        if ( !$request->session()->get('tab') && $request->get('tab')) {
            $request->session()->flash('tab', $request->get('tab'));
        } 
        if ($request->get('op')) {
            $op = Item::find($request->get('op'));
            $this->data['opening'] = $op ?? null;
            $courseDb['schedule'] = Schedule::where('item_id', $op->id)->get();
        }
        $this->data['course'] = $courseDb;
        $this->data['navText'] = __('Chỉnh sửa lớp học');
        $this->data['hasBack'] = route('class');
        $this->data['courseId'] = $courseId;

        return view('class.edit', $this->data);
    }

    public function resourceDelete($id)
    {
        $resourceM = new ItemResource();
        $rs = $resourceM->deleteRes($id);
        return redirect()->back()->with([
            'tab' => 'resource',
            'notify' => $rs
        ]);
    }

    public function delSchedule($id)
    {
        $rs = Schedule::where('item_id', $id)->delete();
        return redirect()->back()->with([
            'tab' => 'schedule',
            'notify' => ($rs > 0)
        ]);
    }
}
