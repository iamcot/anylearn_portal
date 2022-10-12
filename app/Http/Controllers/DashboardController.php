<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use App\Services\DashboardServices;
use App\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userM = new User();
        // if ($userM->needUpdateDocs()) {
        //     $this->data['warning'] = __('Vui lòng <a href=":url">nhấn vào đây</a> để cập nhật giấy tờ của bạn', ['url' => route('user.update_doc')]);
        // }
        $this->data['navText'] = __('Bảng thông tin');
        $userServ = new UserServices();
        if ($userServ->isMod()) {
            $dashServ = new DashboardServices();
            $this->data['newUserChartData'] = json_encode($dashServ->userCreatedByDay());
            $this->data['topUsers'] = $dashServ->topUser();
            $this->data['topItems'] = $dashServ->topItem();
            return view('dashboard.index', $this->data);
        } else {
            return view('dashboard.member', $this->data);
        }
    }

    public function meDashboard(Request $request) {
        // $this->data['navText'] = __('THÔNG TIN CỦA TÔI');
        $editUser = Auth::user();
        $userService = new UserServices();

        if ($request->input('save')) {
            $input = $request->all();
            $input['role'] = $editUser->role;
            $input['user_id'] = $editUser->user_id;
            $input['boost_score'] = $editUser->boost_score;
            $input['commission_rate'] = $editUser->commission_rate;
            $userM = new User();
            $rs = $userM->saveMember($request, $input);
            $i18 = new I18nContent();
                $i18->i18nSave('en','users', auth()->user()->id,"introduce", $input['introduce']['en']);
                $i18->i18nSave('en','users', auth()->user()->id, "full_content", $input['full_content']['en']);
            return redirect()->route('me.edit')->with('notify', $rs);
        }
        $userselect = User::all();
        $UserDT = $userService->userInfo(auth()->user()->id);
        $this->data['userselect'] = $userselect;
        $this->data['userDT'] = $UserDT;
        $this->data['user'] = $editUser;
       // $this->data['navText'] = __('Thông tin');
        $this->data['type'] = 'member';
        return view(env('TEMPLATE', '') . 'me.dashboard', $this->data);
    }

    public function feedback(Request $request)
    {
        $this->data['feedbacks'] = Feedback::with('user')->orderby('id', 'desc')->paginate(20);
        // dd($this->data['feedbacks']);
        return view('dashboard.feedback', $this->data);
    }
}
