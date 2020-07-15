<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use App\Services\DashboardServices;
use Illuminate\Http\Request;

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
        $dashServ = new DashboardServices();
        $this->data['newUserChartData'] = json_encode($dashServ->userCreatedByWeek());
        $this->data['topUsers'] = $dashServ->topUser();
        $this->data['topItems'] = $dashServ->topItem();
        return view('dashboard.index', $this->data);
    }

    public function feedback(Request $request)
    {
        $this->data['feedbacks'] = Feedback::with('user')->orderby('id', 'desc')->paginate(20);
        // dd($this->data['feedbacks']);
        return view('dashboard.feedback', $this->data);
    }
}
