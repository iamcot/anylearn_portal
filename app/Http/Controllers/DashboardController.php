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
        if ($userM->needUpdateDocs()) {
            $this->data['warning'] = __('Vui lòng <a href=":url">nhấn vào đây</a> để cập nhật giấy tờ của bạn', ['url' => route('user.update_doc')]);
        }
        $this->data['navText'] = __('Bảng thông tin');
        $userServ = new UserServices();
        if ($userServ->haveAccess(Auth::user()->role, 'admin')) {
            $dashServ = new DashboardServices();
            $this->data['newUserChartData'] = json_encode($dashServ->userCreatedByDay());
            $this->data['topUsers'] = $dashServ->topUser();
            $this->data['topItems'] = $dashServ->topItem();
            return view('dashboard.index', $this->data);
        } else {
            return view('dashboard.member', $this->data);
        }
    }

    public function meDashboard() {
        $this->data['navText'] = __('Bảng thông tin');
        return view(env('TEMPLATE', '') . 'me.dashboard', $this->data);
    }

    public function feedback(Request $request)
    {
        $this->data['feedbacks'] = Feedback::with('user')->orderby('id', 'desc')->paginate(20);
        // dd($this->data['feedbacks']);
        return view('dashboard.feedback', $this->data);
    }
}
