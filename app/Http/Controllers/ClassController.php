<?php

namespace App\Http\Controllers;

use App\Constants\ItemConstants;
use App\Constants\UserConstants;
use App\Models\ItemResource;
use App\Models\Schedule;
use App\Models\User;
use App\Services\ItemServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

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
        $this->data['navText'] = __('Tạo lớp học');
        $this->data['hasBack'] = route('class');
        return view('class.edit', $this->data);
    }

    public function edit(Request $request, $courseId)
    {
        $courseService = new ItemServices();
        if ($request->input('action') == 'update') {
            $input = $request->all();
            $rs = $courseService->updateItem($request, $input);
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
