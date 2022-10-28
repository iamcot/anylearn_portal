<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Feedback;
use App\Models\User;
use App\Services\DashboardServices;
use App\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\I18nContent;
use Illuminate\Support\Facades\DB;

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
        $this->data['user'] = Auth::user();
        $userServ = new UserServices();
        if ($userServ->isSale()) {
            return view('dashboard.sale', $this->data);
        } else if ($userServ->isMod()) {
            return view('dashboard.index', $this->data);
        } else {
            return view('dashboard.member', $this->data);
        }
    }

    public function meDashboard(Request $request)
    {
        $editUser = Auth::user();
        $userService = new UserServices();

            // $input = $request->all();
            // dd($input);
        if ($request->input('save')) {
            $input = $request->all();
            $input['role'] = $editUser->role;
            $input['user_id'] = $editUser->user_id;
            $input['boost_score'] = $editUser->boost_score;
            $input['commission_rate'] = $editUser->commission_rate;
            $userM = new User();
            $rs = $userM->saveMember($request, $input);
            return redirect()->route('me.edit')->with('notify', $rs);
        }

        $friends = User::where('user_id', $editUser->id)->paginate(20);
        $userI18n = $userService->userInfo($editUser->id);
        $this->data['friends'] = $friends;
        $this->data['user'] = $userI18n;
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
