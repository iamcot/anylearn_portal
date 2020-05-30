<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FileServices;
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
            //TODO to allow login on multi devices or not.
            if (empty($user->api_token)) {
                $saveToken = User::find($user->id)->update(
                    ['api_token' => hash('sha256', Str::random(60))]
                );
                if (!$saveToken) {
                    return response('Không thể hoàn tất xác thực', 500);
                }
            }
            $userDB = User::find($user->id)->makeVisible(['api_token']);
            return response()->json($userDB, 200);
        }
        return response('Thông tin xác thực không hợp lệ', 401);
    }

    public function edit(Request $request)
    {
        $user  = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }
        $inputs = $request->all();
        $userModel = new User();
        $validateExists = $userModel->validateUpdate($user->id, $inputs);
        if ($validateExists != "") {
            return response($validateExists, 400);
        }

        $rs = User::find($user->id)->update($inputs);
        if ($rs) {
            return response('{"result": true}', 200);
        }
        return response("Cập nhật thông tin thất bại, vui lòng thử lại", 500);
    }

    public function userInfo(Request $request)
    {
        $user  = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }
        $user->makeVisible(['api_token']);
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

    public function uploadImage(Request $request, $type)
    {
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }
        $fileService = new FileServices();
        $fileuploaded = $fileService->doUploadImage($request, 'image');
        if ($fileuploaded === false) {
            return response('Upload file không thành công.', 500);
        }

        User::find($user->id)->update([
            $type => $fileuploaded['url']
        ]);
        return response($fileuploaded['url'], 200);
    }

    public function friends(Request $request, $userId)
    {
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }
        if (!$userId) {
            return response('Yêu cầu không đúng', 400);
        }
        $friendsOfUser = User::find($userId);
        if (!$friendsOfUser) {
            return response('Yêu cầu không đúng', 400);
        }
        $friends = User::where('user_id', $userId)->get();
        return response()->json([
            'user' => $friendsOfUser,
            'friends' => $friends,
        ], 200);
    }
}
