<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\User;
use App\Models\UserDocument;
use App\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    private $response = [
        'success' => false,
        'data' => null,
    ];

    public function toc() 
    {
        $configM = new Configuration();
        $toc = $configM->getToc();
        $this->buildResponse($toc != null ? true : false, $configM->getToc());
        return response()->json($this->response);
    }

    public function userDocs($userId) {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->haveAccess($user->role, 'admin')) {
            $this->buildResponse(false, __('Bạn không có quyền cho thao tác này'));
        } else {
            $files = UserDocument::where('user_id', $userId)->get();
            $this->buildResponse(true, $files);
        }
        return response()->json($this->response);
    }

    public function userRemoveUpdateDoc($userId) {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->haveAccess($user->role, 'admin')) {
            $this->buildResponse(false, __('Bạn không có quyền cho thao tác này'));
        } else {
            $rs = User::find($userId)->update(['update_doc' => 0]);
            $this->buildResponse(true, $rs);
        }
        return response()->json($this->response);
    }

    private function buildResponse($success, $data) {
        $this->response['success'] = $success;
        $this->response['data'] = $data;
    }
}
