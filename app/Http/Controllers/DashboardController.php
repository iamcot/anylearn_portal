<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        return view('dashboard.index', $this->data);
    }
}
