<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ConfigConstants;
use App\Constants\ItemConstants;
use App\Constants\OrderConstants;
use App\Constants\UserConstants;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Item;
use App\Models\OrderDetail;
use App\Models\Participation;
use App\Models\Schedule;
use App\Models\User;
use App\Models\UserDocument;
use App\Services\FileServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $user->reflink = "https://anylearn.vn/ref/" . $user->refcode;
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
        $oldImageUrl = $user->$type;
        $fileService->deleteUserOldImageOnS3($oldImageUrl);

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

    public function usersList(Request $request, $role)
    {
        if (!in_array($role, [UserConstants::ROLE_SCHOOL, UserConstants::ROLE_TEACHER])) {
            return response('Yêu cầu không đúng', 400);
        }
        $pageSize = $request->get('pageSize', 9999);
        $list = User::where('role', $role)
            ->where('update_doc', UserConstants::STATUS_ACTIVE)
            ->where('status', UserConstants::STATUS_ACTIVE)
            ->orderby('is_hot', 'desc')
            ->orderby('id', 'desc')
            ->paginate($pageSize);
        $configM = new Configuration();
        $banner = $configM->get($role == UserConstants::ROLE_TEACHER ? ConfigConstants::CONFIG_TEACHER_BANNER : ConfigConstants::CONFIG_SCHOOL_BANNER);
        return response()->json([
            'banner' => $banner,
            'list' => $list,
        ], 200);
    }

    public function myCalendar(Request $request)
    {
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }

        $orderDetailM = new OrderDetail();
        return response()->json($orderDetailM->userRegistered($user->id));
    }

    public function confirmJoinCourse(Request $request, $itemId)
    {
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }

        $schedule = Schedule::where('item_id', $itemId)->first();
        if (!$schedule) {
            return response("Không có lịch cho buổi học này", 404);
        }

        $item = Item::find($itemId);
        if (!$item) {
            return response("Khóa  học không tồn tại", 404);
        }

        $isConfirmed = Participation::where('item_id', $itemId)
            ->where('schedule_id',  $schedule->id)
            ->where('participant_user_id', $user->id)
            ->count();
        if ($isConfirmed > 0) {
            return response("Bạn đã xác nhận rồi", 400);
        }
        $rs = Participation::create([
            'item_id' => $itemId,
            'schedule_id' =>  $schedule->id,
            'organizer_user_id' => $item->user_id,
            'participant_user_id' => $user->id,
            'organizer_confirm' => 1,
            'participant_confirm' => 1,
        ]);

        return response()->json(['result' => $rs ? (int) $itemId : 0]);
    }

    public function courseRegisteredUsers(Request $request, $itemId)
    {
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }

        $item = Item::find($itemId);
        if (!$item) {
            return response("Khóa  học không tồn tại", 404);
        }

        $list = DB::table('order_details as od')
            ->join('orders', 'orders.id', '=', 'od.order_id')
            ->join('users', 'users.id', '=', 'od.user_id')
            ->where('orders.status', OrderConstants::STATUS_DELIVERED)
            ->where('od.item_id', $itemId)
            ->select('users.id', 'users.name', 'users.phone', 'users.image')
            ->get();
        return response()->json($list);
    }

    public function profile($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response("User không tồn tại", 404);
        }
        $user->docs = UserDocument::where('user_id', $user->id)->get();
        return response()->json($user);
    }

    public function getDocs(Request $request)
    {
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }
        $docs = UserDocument::where('user_id', $user->id)->get();
        return response()->json($docs);
    }

    public function addDoc(Request $request)
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
        $userDocM = new UserDocument();
        $userDocM->addDocWeb($fileuploaded, $user);

        $docs = UserDocument::where('user_id', $user->id)->get();
        return response()->json($docs);
    }

    public function removeDoc(Request $request, $fileId)
    {
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }
        $file = UserDocument::find($fileId);
        if (!$file) {
            return response("Tài liệu không có", 404);
        }
        if ($file->user_id != $user->id) {
            return response("Bạn không có quyền", 400);
        }
        $fileService = new FileServices();
        $oldImageUrl = $file->data;
        $fileService->deleteUserOldImageOnS3($oldImageUrl);

        $rs = UserDocument::find($fileId)->delete();
        $docs = UserDocument::where('user_id', $user->id)->get();
        if (count($docs) == 0) {
            User::find($user->id)->update(['update_doc' => 0]);
        }
        return response()->json($docs);
    }
}
