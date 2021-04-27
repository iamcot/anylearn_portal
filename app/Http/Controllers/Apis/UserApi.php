<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ConfigConstants;
use App\Constants\ItemConstants;
use App\Constants\NotifConstants;
use App\Constants\OrderConstants;
use App\Constants\UserConstants;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Contract;
use App\Models\Item;
use App\Models\ItemUserAction;
use App\Models\Notification;
use App\Models\OrderDetail;
use App\Models\Participation;
use App\Models\Schedule;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserDocument;
use App\Services\FileServices;
use App\Services\TransactionService;
use App\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            if ($request->get('notif_token')) {
                if (empty($user->notif_token)) {
                    $notifM = new Notification();
                    $notifM->resendUnsentMessage($user->id, $request->get('notif_token'));
                }
                User::find($user->id)->update([
                    'notif_token' => $request->get('notif_token')
                ]);
            }
            $userDB = User::find($user->id)->makeVisible(['api_token']);
            return response()->json($userDB, 200);
        }
        return response('Thông tin xác thực không hợp lệ', 401);
    }

    public function loginFacebook(Request $request)
    {
        $input = $request->all();
        Log::debug($input);
        $existsUser = User::where('3rd_id', $input['id'])->first();
        if (!$existsUser) {
            $data = [
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => $input['id'],
                'role' => UserConstants::ROLE_MEMBER,
                'password' => $input['email'],
            ];
            $userModel = new User();
            $existsUser = $userModel->createNewMember($data);
            if ($existsUser) {
                User::find($existsUser->id)->update([
                    '3rd_id' => $input['id'],
                    '3rd_type' => User::LOGIN_3RD_FACEBOOK,
                    '3rd_token' => null,
                    'image' => $input['picture'],
                ]);
            }
        }
        if (!$existsUser->status) {
            return response('Tài khoản của bạn đã bị khóa.', 403);
        }
        if (empty($existsUser->api_token)) {
            $saveToken = User::find($existsUser->id)->update(
                ['api_token' => hash('sha256', Str::random(60))]
            );
            if (!$saveToken) {
                return response('Không thể hoàn tất xác thực', 500);
            }
        }
        if ($request->get('notif_token')) {
            if (empty($existsUser->notif_token)) {
                $notifM = new Notification();
                $notifM->resendUnsentMessage($existsUser->id, $request->get('notif_token'));
            }
            User::find($existsUser->id)->update([
                'notif_token' => $request->get('notif_token')
            ]);
        }
        $userDB = User::find($existsUser->id)->makeVisible(['api_token']);
        return response()->json($userDB, 200);
    }

    public function loginApple(Request $request)
    {
        $input = $request->all();
        Log::debug($input);
        $existsUser = User::where('3rd_id', $input['id'])->first();
        if (!$existsUser) {
            $data = [
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => $input['id'],
                'role' => UserConstants::ROLE_MEMBER,
                'password' => $input['email'],
            ];
            $userModel = new User();
            $existsUser = $userModel->createNewMember($data);
            if ($existsUser) {
                User::find($existsUser->id)->update([
                    '3rd_id' => $input['id'],
                    '3rd_type' => User::LOGIN_3RD_APPLE,
                    '3rd_token' => null,
                    'image' => $input['picture'],
                ]);
            }
        }
        if (!$existsUser->status) {
            return response('Tài khoản của bạn đã bị khóa.', 403);
        }
        if (empty($existsUser->api_token)) {
            $saveToken = User::find($existsUser->id)->update(
                ['api_token' => hash('sha256', Str::random(60))]
            );
            if (!$saveToken) {
                return response('Không thể hoàn tất xác thực', 500);
            }
        }
        if ($request->get('notif_token')) {
            if (empty($existsUser->notif_token)) {
                $notifM = new Notification();
                $notifM->resendUnsentMessage($existsUser->id, $request->get('notif_token'));
            }
            User::find($existsUser->id)->update([
                'notif_token' => $request->get('notif_token')
            ]);
        }
        $userDB = User::find($existsUser->id)->makeVisible(['api_token']);
        return response()->json($userDB, 200);
    }

    public function logout(Request $request)
    {
        $user  = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }
        User::find($user->id)->update([
            'notif_token' => '',
        ]);
        return response('OK', 200);
    }

    public function edit(Request $request)
    {
        $user  = $request->get('_user');

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

    public function userInfoLess(Request $request)
    {
        $user  = $request->get('_user');
        $user->makeVisible(['api_token']);
        $user->reflink = "https://anylearn.vn/ref/" . $user->refcode;

        $configM = new Configuration();
        $user->ios_transaction = (int)$configM->get(ConfigConstants::CONFIG_IOS_TRANSACTION);
        $user->children = User::where('user_id', $user->id)
            ->where('is_child', 1)
            ->get();

        return response()->json($user, 200);
    }

    public function userInfo(Request $request)
    {
        $user  = $request->get('_user');
        $user->makeVisible(['api_token', 'full_content']);
        $user->reflink = "https://anylearn.vn/ref/" . $user->refcode;
        $user->children = User::where('user_id', $user->id)
            ->where('is_child', 1)
            ->get();
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
        $user = $request->get('_user');

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
        $user = $request->get('_user');
        if (!$userId) {
            return response('Yêu cầu không đúng', 400);
        }
        $friendsOfUser = User::find($userId);
        if (!$friendsOfUser) {
            return response('Yêu cầu không đúng', 400);
        }
        $friends = User::where('user_id', $userId)
            ->where('is_child', 0)
            ->orderby('first_name')
            ->get();
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
        $list = DB::table('users')->where('role', $role)
            ->where('update_doc', UserConstants::STATUS_ACTIVE)
            ->where('status', UserConstants::STATUS_ACTIVE)
            ->where('is_test', 0)
            ->orderby('is_hot', 'desc')
            ->orderby('boost_score', 'desc')
            ->orderby('first_name')
            ->select(
                'users.*',
                DB::raw("(select avg(iua.value) from item_user_actions AS iua WHERE type = 'rating' AND iua.item_id in (select items.id from items where items.user_id = users.id) ) AS rating")
            )
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
        $user = $request->get('_user');

        $orderDetailM = new OrderDetail();
        return response()->json($orderDetailM->userRegistered($user->id));
    }

    public function confirmJoinCourse(Request $request, $scheduleId)
    {
        $user = $request->get('_user');
        $childId = $request->get('child');
        $joinedUserId = !empty($childId) ? $childId : $user->id;

        $schedule = Schedule::find($scheduleId);
        if (!$schedule) {
            return response("Không có lịch cho buổi học này", 404);
        }

        $item = Item::find($schedule->item_id);
        if (!$item) {
            return response("Khóa  học không tồn tại", 404);
        }
        $itemId = $item->id;

        $isConfirmed = Participation::where('item_id', $itemId)
            ->where('schedule_id',  $schedule->id)
            ->where('participant_user_id', $joinedUserId)
            ->count();
        if ($isConfirmed > 0) {
            return response("Bạn đã xác nhận rồi", 400);
        }
        $rs = Participation::create([
            'item_id' => $itemId,
            'schedule_id' =>  $scheduleId,
            'organizer_user_id' => $item->user_id,
            'participant_user_id' => $joinedUserId,
            'organizer_confirm' => 1,
            'participant_confirm' => 1,
        ]);
        $author = User::find($item->user_id);
        $notifServ = new Notification();
        $notifServ->createNotif(NotifConstants::COURSE_JOINED, $author->id, [
            'username' => $user->name,
            'course' => $item->title,
        ]);

        $transService = new TransactionService();
        // approve direct and indirect commission
        $directCommission = DB::table('transactions')
            ->join('order_details AS od', 'od.id', '=', 'transactions.order_id')
            ->join('orders', 'orders.id', '=', 'od.order_id')
            ->where('orders.user_id', $joinedUserId)
            ->where('od.item_id', $item->id)
            ->where('transactions.status', ConfigConstants::TRANSACTION_STATUS_PENDING)
            ->where('transactions.type', ConfigConstants::TRANSACTION_COMMISSION)
            ->where('transactions.user_id', $user->id)
            ->select('transactions.*')
            ->first();
        if ($directCommission) {
            $transService->approveWalletcTransaction($directCommission->id);
        }

        // approve up tree transaction, just 1 level
        $refUser = User::find($user->user_id);
        if ($refUser) {
            $inDirectCommission = DB::table('transactions')
                ->where('transactions.order_id', $directCommission->order_id)
                ->where('transactions.status', ConfigConstants::TRANSACTION_STATUS_PENDING)
                ->where('transactions.type', ConfigConstants::TRANSACTION_COMMISSION)
                ->where('transactions.user_id', $refUser->id)
                ->select('transactions.*')
                ->first();
            if ($inDirectCommission) {
                $transService->approveWalletcTransaction($inDirectCommission->id);
            }
        }

        // No limit time class => just touch transaction related to approved user 
        if ($item->nolimit_time == 1) {
            //get transaction relate order id & user & item
            $trans = DB::table('transactions')
                ->join('order_details AS od', function ($query) use ($user) {
                    $query->on('od.id', '=', 'transactions.order_id')
                        ->where('od.user_id', '=', $user->id);
                })
                ->join('orders', 'orders.id', '=', 'od.order_id')
                ->where('orders.user_id', $joinedUserId)
                ->where('od.item_id', $item->id)
                ->where('transactions.user_id', $author->id)
                ->where('transactions.status', ConfigConstants::TRANSACTION_STATUS_PENDING)
                ->where('transactions.type', ConfigConstants::TRANSACTION_COMMISSION)
                ->select('transactions.*')
                ->first();
            // approve author transaction
            if ($trans) {
                $transService->approveWalletcTransaction($trans->id);
            }
            // approve foundation transaction
            DB::table('transactions')
                ->where('transactions.order_id', $trans->order_id)
                ->where('transactions.status', ConfigConstants::TRANSACTION_STATUS_PENDING)
                ->where('transactions.type', ConfigConstants::TRANSACTION_FOUNDATION)
                ->update([
                    'status' => ConfigConstants::TRANSACTION_STATUS_DONE
                ]);
        } elseif ($item->got_bonus == 0) { // Normal class and still not get bonus => touch all transaction when reach % of approved users
            $configM = new Configuration();
            $needNumConfirm = $configM->get(ConfigConstants::CONFIG_NUM_CONFIRM_GOT_BONUS);
            $totalReg = OrderDetail::where('item_id', $itemId)->count();
            $totalConfirm = Participation::where('item_id', $itemId)->count();
            //update author commssion when reach % of approved users
            if ($totalConfirm / $totalReg >= $needNumConfirm) {
                //get ALL transaction relate order id & item
                $allTrans = DB::table('transactions')
                    ->join('order_details AS od', 'od.id', '=', 'transactions.order_id')
                    ->where('od.item_id', $item->id)
                    ->where('transactions.user_id', $author->id)
                    ->where('transactions.status', ConfigConstants::TRANSACTION_STATUS_PENDING)
                    ->where('transactions.type', ConfigConstants::TRANSACTION_COMMISSION)
                    ->select('transactions.*')
                    ->get();

                // approve author transaction
                if ($allTrans) {
                    foreach ($allTrans as $trans) {
                        $transService->approveWalletcTransaction($trans->id);
                    }
                }
                // approve foundation transaction
                DB::table('transactions')
                    ->join('order_details AS od', 'od.id', '=', 'transactions.order_id')
                    ->where('od.item_id', $item->id)
                    ->where('transactions.user_id', $author->id)
                    ->where('transactions.status', ConfigConstants::TRANSACTION_STATUS_PENDING)
                    ->where('transactions.type', ConfigConstants::TRANSACTION_FOUNDATION)
                    ->update([
                        'status' => ConfigConstants::TRANSACTION_STATUS_DONE
                    ]);

                Item::find($itemId)->update([
                    'got_bonus' => 1
                ]);
            }
        }

        return response()->json(['result' => 1]);
    }

    public function courseRegisteredUsers(Request $request, $itemId)
    {
        $user = $request->get('_user');

        $item = Item::find($itemId);
        if (!$item) {
            return response("Khóa  học không tồn tại", 404);
        }

        $list = DB::table('order_details as od')
            ->join('orders', 'orders.id', '=', 'od.order_id')
            ->join('users', 'users.id', '=', 'od.user_id')
            ->join('users AS childs', 'childs.id', '=', 'orders.user_id')
            ->where('orders.status', OrderConstants::STATUS_DELIVERED)
            ->where('od.item_id', $itemId)
            ->select('users.id', 'users.name', 'users.phone', 'users.image', 'childs.name AS child')
            ->get();
        return response()->json($list);
    }

    public function profile($userId)
    {
        $user = User::where('id', $userId)
            ->select('id', 'title', 'name', 'image', 'role', 'introduce', 'banner', 'full_content')
            ->makeVisible(['full_content'])
            ->first();
        if (!$user) {
            return response("User không tồn tại", 404);
        }
        $user->docs = UserDocument::where('user_id', $user->id)->get();
        $user->registered = DB::table('order_details AS od')
            ->join('items', 'items.id', '=', 'od.item_id')
            ->where('od.user_id', $userId)
            ->select('items.title', 'items.image', 'items.short_content', 'items.id')
            ->take(10)
            ->get();
        $user->faved = DB::table('item_user_actions AS iua')
            ->join('items', 'items.id', '=', 'iua.item_id')
            ->where('iua.type', ItemUserAction::TYPE_FAV)
            ->where('iua.user_id', $userId)
            ->where('iua.value', 1)
            ->select('items.title', 'items.image', 'items.short_content', 'items.id')
            ->take(10)
            ->get();
        $user->rated = DB::table('item_user_actions AS iua')
            ->join('items', 'items.id', '=', 'iua.item_id')
            ->where('iua.type', ItemUserAction::TYPE_RATING)
            ->where('iua.user_id', $userId)
            ->select('items.title', 'items.image', 'items.short_content', 'iua.value', 'items.id')
            ->take(10)
            ->get();


        return response()->json($user);
    }

    public function getDocs(Request $request)
    {
        $user = $request->get('_user');
        $docs = UserDocument::where('user_id', $user->id)->get();
        return response()->json($docs);
    }

    public function addDoc(Request $request)
    {
        $user = $request->get('_user');
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
        $user = $request->get('_user');

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

    public function notification(Request $request)
    {
        $user = $request->get('_user');
        $notif = Notification::where('user_id', $user->id)
            ->orderby('id', 'desc')
            ->paginate(Notification::PAGESIZE);
        return response()->json($notif);
    }

    public function notifRead(Request $request, $id)
    {
        $user = $request->get('_user');
        $notif = Notification::find($id)->update([
            'read' => DB::raw('now()')
        ]);
        return response('OK', 200);
    }

    public function allFriends(Request $request)
    {
        $user = $request->get('_user');
        $userServ = new UserServices();
        $data = $userServ->allFriends($user->id);
        return response()->json($data);
    }

    public function getContract(Request $request)
    {
        $user = $request->get('_user');
        $lastContract = Contract::where('user_id', $user->id)
            ->orderby('id', 'desc')->first();
        $configM = new Configuration();
        if ($lastContract->type == UserConstants::ROLE_TEACHER) {
            $key = ConfigConstants::CONTRACT_TEACHER;
            $template = $configM->get($key);
            $lastContract->template = Contract::makeContent($template, $user, $lastContract);
        } else {
            $key = ConfigConstants::CONTRACT_SCHOOL;
            $template = $configM->get($key);
            $lastContract->template = Contract::makeContent($template, $user, $lastContract);
        }
        return response()->json($lastContract);
    }

    public function saveContract(Request $request)
    {
        $user = $request->get('_user');
        $contractJson = $request->get('contract');
        $contract = json_decode($contractJson, true);
        if (empty($contract)) {
            return response("Không có thông tin hợp đồng.", 400);
        }
        $contract['user_id'] = $user->id;
        $contract['type'] = $user->role;
        $contract['status'] = UserConstants::CONTRACT_SIGNED;

        Contract::where('user_id', $user->id)->update([
            'status' => UserConstants::CONTRACT_DELETED,
        ]);
        $newContract = Contract::create($contract);
        if ($newContract) {
            $dataUpdate = [
                "is_signed" => UserConstants::CONTRACT_SIGNED,
                "email" => $contract['email'],
                "address" => $contract['address'],
            ];
            if ($user->role == UserConstants::ROLE_TEACHER) {
                $dataUpdate['dob'] = $contract['dob'];
            } else {
                $dataUpdate['title'] = $contract['ref'];
            }
            User::find($user->id)->update($dataUpdate);
        }

        return response()->json([
            'status' => true,
        ]);
    }

    public function signContract(Request $request)
    {
        $user = $request->get('_user');
        $fileService = new FileServices();
        $fileuploaded = $fileService->doUploadImage($request, 'image');
        if ($fileuploaded === false) {
            return response('Upload file không thành công.', 500);
        }
        $lastContract = Contract::where('user_id', $user->id)
            ->orderby('id', 'desc')->first();
        $oldImageUrl = $lastContract->signed;
        if ($oldImageUrl) {
            $fileService->deleteUserOldImageOnS3($oldImageUrl);
        }

        Contract::find($lastContract->id)->update([
            'signed' => $fileuploaded['url'],
            'status' => UserConstants::CONTRACT_SIGNED,
        ]);

        User::find($lastContract->user_id)->update([
            'is_signed' => UserConstants::CONTRACT_SIGNED
        ]);
        return response($fileuploaded['url'], 200);
    }

    public function listChildren(Request $request)
    {
        $user = $request->get('_user');
        $children = User::where('user_id', $user->id)
            ->where('is_child', 1)
            ->get();
        return response()->json($children);
    }

    public function saveChildren(Request $request)
    {
        $user = $request->get('_user');
        $id = $request->get('id');
        $name = $request->get('name');
        if ($id == 0) {
            $phoneByTime = $user->phone . time();
            $newChild = User::create([
                'is_child' => 1,
                'user_id' => $user->id,
                'name' => $name,
                'phone' => $phoneByTime,
                'refcode' => $phoneByTime,
                'password' => Hash::make($user->phone),
                'role' => UserConstants::ROLE_MEMBER,
                'status' => UserConstants::STATUS_ACTIVE,
            ]);
        } else {
            $child = User::find($id);
            if ($child->is_child == 1 && $child->user_id == $user->id) {
                $child->update([
                    'name' => $name,
                ]);
            }
        }
        return response()->json([
            'result' => true
        ]);
    }
}
