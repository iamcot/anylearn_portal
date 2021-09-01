<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $data  = [];

    public function developing()
    {
        return view('developing');
    }

    public function isAuthedApi(Request $request)
    {
        $token = $request->get('api_token');
        if (empty($token)) {
            return response('Yêu cầu không hợp lệ', 400);
        }
        $user = User::where('api_token', $token)->first();
        if ($user == null) {
            return response('Thông tin xác thực không hợp lệ', 401);
        }
        if (!$user->status) {
            return response('Tài khoản của bạn đã bị khóa.', 403);
        }
        return $user;
    }

    protected function detectUserAgent(Request $request)
    {
        $userAgent = $request->header('User-Agent');
        if ($userAgent == "anylearn-app") {
            $this->data['isApp'] = true;
        } else {
            $this->data['isApp'] = false;
        }
    }
}
