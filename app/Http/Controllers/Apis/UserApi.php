<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserApi extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('phone', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (!$user->status) {
                return response('Tài khoản của bạn đã bị khóa.', 403);
            }
            if (empty($user->api_token)) {
                $saveToken = User::find($user->id)->update(
                    ['api_token' => hash('sha256', Str::random(60))]
                );
                if ($saveToken) {
                    $userDB = User::find($user->id);
                    return response()->json($userDB, 200);
                }
            } else {
                return response()->json($user, 200);
            }
        }
        return response('Thông tin xác thực không hợp lệ', 401);
    }

    public function userInfo(Request $request)
    {
        $token = $request->get('api_token');
        if (empty($token)) {
            return response('Yêu cầu không đúng', 400);
        }
        $user = User::where('api_token', $token)->first();
        if ($user == null) {
            return response('Thông tin xác thực không hợp lệ', 401);
        }
        if (!$user->status) {
            return response('Tài khoản của bạn đã bị khóa.', 403);
        }
        return response()->json($user, 200);
    }

    public function register(Request $request)
    {
        $inputs = $request->all();
        $userM = new User();
        if (!isset($inputs['password_confirmation'])) {
            $inputs['password_confirmation'] = $inputs['password'];
        }
        $validator = $userM->validateMember($inputs);
        if ($validator->fails()) {
            return response($validator->errors()->first(), 400);
        }
        try {
            // $inputs['api_token'] = hash('sha256', Str::random(60));
            $newUser = $userM->createNewMember($inputs);
            return response()->json($newUser, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response($e->__toString(), 500);
        }
    }
}
