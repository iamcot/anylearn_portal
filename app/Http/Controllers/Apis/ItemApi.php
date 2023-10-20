<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ConfigConstants;
use App\Constants\FileConstants;
use App\Constants\ItemConstants;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Item;
use App\Models\ItemUserAction;
use App\Models\Notification;
use App\Models\Schedule;
use App\Models\User;
use App\Services\FileServices;
use App\Services\ItemServices;
use App\Services\UserServices;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemApi extends Controller
{
    public function create(Request $request)
    {
        $user = $request->get('_user');

        if (empty($request->get('type'))) {
            return response('Chưa có loại khóa học', 400);
        }
        $itemService = new ItemServices();
        $newItem = $itemService->createItem($request, ItemConstants::TYPE_CLASS, $user);
        if ($newItem instanceof Validator) {
            return response($newItem->errors()->first(), 400);
        }
        return response()->json(['result' => $newItem === false ? false : true]);
    }
    public function update(Request $request)
    {
        $user = $request->get('_user');
        $input = $request->all();
        if (empty($request->get('type'))) {
            return response('Chưa có loại khóa học', 400);
        }
        $itemService = new ItemServices();
        $updateItem = $itemService->updateItem($request, $input, $user);
        if ($updateItem instanceof Validator) {
            return response($updateItem->errors()->first(), 400);
        }
        return response()->json(['result' => $updateItem === false ? false : true]);
    }
    public function updateItem(Request $request)
    {
        $user = $request->get('_user');
        $input = $request->all();
        if (empty($request->get('type'))) {
            return response('Chưa có loại khóa học', 400);
        }
        $updateItem = Item::find($input['id']);
        $updateItem->update($input);
        return response()->json(['result' => $updateItem === false ? false : true]);
    }
    public function edit(Request $request, $id)
    {
        $user = $request->get('_user');

        $item = Item::where('id', $id)
            ->where('user_id', $user->id)
            ->first()->makeVisible(['content']);

        if (!$item) {
            return response()->json(['message' => 'Không có dữ liệu'], 404);
        }

        dd($item->content);
        $itemService = new ItemServices();
        $digital = $itemService->getDigitalCourse($id);

        // Thêm trường 'digital' vào dữ liệu đối tượng $item

        $item->digital = $digital->original;

        return response()->json($item);
    }


    public function save(Request $request, $id)
    {
        $user = $request->get('_user');

        $item = Item::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        if (!$item) {
            return response("Không có dữ liệu", 404);
        }

        $itemService = new ItemServices();
        $newItem = $itemService->updateItem($request, $request->all(), $user);
        $digital = $itemService->DigitalCourse($request,$id);
        if ($newItem instanceof Validator) {
            return response($newItem->errors()->first(), 400);
        }
        return response()->json(['result' => $newItem, 'digital' => $digital]);
        // return response()->json(['result' => $newItem === false ? false : true]);
    }

    public function list(Request $request)
    {
        $user = $request->get('_user');

        $open = Item::where('user_id', $user->id)
            ->where('user_status', '<=', ItemConstants::USERSTATUS_ACTIVE)
            ->orderby('user_status', 'desc')
            ->orderby('id', 'asc')
            ->get();
        $close = Item::where('user_id', $user->id)
            ->where('user_status', ItemConstants::USERSTATUS_DONE)
            ->orderby('id', 'desc')
            ->get();
        return response()->json([
            'open' => $open,
            'close' => $close
        ]);
    }

    public function uploadImage(Request $request, $itemId)
    {
        $user = $request->get('_user');

        $item = Item::where('id', $itemId)
            ->where('user_id', $user->id)
            ->first();
        if (!$item) {
            return response("Không có dữ liệu", 404);
        }

        $fileService = new FileServices();
        $fileuploaded = $fileService->doUploadImage($request, 'image', FileConstants::DISK_S3, true, FileConstants::FOLDER_ITEMS . '/' . $item->id);
        if ($fileuploaded === false) {
            return response('Upload file không thành công.', 500);
        }
        $oldImageUrl = $item->image;
        $fileService->deleteUserOldImageOnS3($oldImageUrl);

        Item::find($item->id)->update([
            'image' => $fileuploaded['url']
        ]);
        return response($fileuploaded['url'], 200);
    }

    public function changeUserStatus(Request $request, $itemId, $newStatus)
    {
        $user = $request->get('_user');

        if ($newStatus == ItemConstants::USERSTATUS_DONE) {
            $notifService = new Notification();
            $notifService->notifRemindConfirms($itemId);
        }
        $rs = Item::where('id', $itemId)
            ->where('user_id', $user->id)
            ->update([
                'user_status' => $newStatus,
            ]);

        return response()->json([
            'result' => $rs == 0 ? false : true,
        ]);
    }

    public function userItems(Request $request, $userId)
    {
        $user = User::find($userId)->makeVisible(['full_content']);
        if (!$user) {
            return response('Trang không tồn tại', 404);
        }
        $pageSize = $request->get('pageSize', 9999);
        // DB::enableQueryLog();
        $configM = new Configuration();
        $isEnableIosTrans = $configM->enableIOSTrans($request);

        $items = DB::table('items')
            ->where(function($query) use ($userId) {
                $query->where('user_id', $userId)
                ->orWhereRaw('items.id in (SELECT class_id from class_teachers AS ct where ct.user_id = ?)', [$userId]);
            })
            // ->where('update_doc', UserConstants::STATUS_ACTIVE)
            ->whereNotIn("user_id", $isEnableIosTrans == 0 ? explode(',', env('APP_REVIEW_DIGITAL_SELLERS', '')) : [])
            ->where('status', ItemConstants::STATUS_ACTIVE)
            ->where('user_status', '>', ItemConstants::STATUS_INACTIVE)
            ->whereNull('items.item_id')
            ->orderby('is_hot', 'desc')
            ->orderby('id', 'desc')
            ->select(
                'items.*',
                DB::raw("(select avg(iua.value) from item_user_actions AS iua WHERE type = 'rating' AND iua.item_id = items.id) AS rating")
            )
            ->paginate($pageSize);


            // dd(DB::getQueryLog());
        return response()->json([
            'user' => $user,
            'items' => $items,
        ], 200);
    }

    public function pdp(Request $request, $itemId)
    {
        $itemService = new ItemServices();
        $user = $this->isAuthedApi($request);
        try {
            $data = $itemService->pdpData($request, $itemId, $user);
            return response()->json($data);
        } catch (Exception $e) {
            return response($e->getMessage(), $e->getCode());
        }
    }

    public function share(Request $request, $itemId)
    {
        $user = $request->get('_user');

        $item = Item::find($itemId);
        if (!$item) {
            return response('Trang không tồn tại', 404);
        }

        $friends = $request->get('friends');
        $notifM = new Notification();
        //TODO need to avoid spam later
        if ($friends == 'ALL') {
            $userServ = new UserServices();
            $allFriends = $userServ->allFriends($user->id);
            foreach ($allFriends as $friend) {
                $notifM->notifCourseShare($item, $user->name, $friend['id']);
            }
        } elseif (count(json_decode($friends, true)) > 0) {
            foreach (json_decode($friends, true) as $id) {
                $notifM->notifCourseShare($item, $user->name, $id);
            }
        }

        return response()->json(['result' => true]);
    }

    public function touchFav(Request $request, $itemId)
    {
        $user = $request->get('_user');
        $item = Item::find($itemId);
        if (!$item) {
            return response('Trang không tồn tại', 404);
        }
        $itemUserActionM = new ItemUserAction();
        $rs = $itemUserActionM->touchFav($itemId, $user->id);
        return response()->json(['is_fav' => $rs]);
    }

    public function saveRating(Request $request, $itemId)
    {
        $user = $request->get('_user');
        $item = Item::find($itemId);
        if (!$item) {
            return response('Trang không tồn tại', 404);
        }
        if($user->role !== "school" || $user->role !== 'teacher'){
            return response('Bạn phải là chuyên gia mới có quyền thực hiện thao tác này', 403);
        }
        $rating = $request->get('rating', 5);
        $comment = $request->get('comment', '');
        $itemUserActionM = new ItemUserAction();
        $rs = $itemUserActionM->saveRating($itemId, $user->id, $rating, $comment);
        return response()->json(['result' => $rs]);
    }

    public function reviews($itemId)
    {
        $data = DB::table('item_user_actions AS iua')
            ->join('users', 'users.id', '=', 'iua.user_id')
            ->where('iua.item_id', $itemId)
            ->where('iua.type', ItemUserAction::TYPE_RATING)
            ->select('iua.*', 'users.name AS user_name', 'users.id AS user_id', 'users.image AS user_image')
            ->get();
        return response()->json($data);
    }
    public function schadule($id) {
        $itemService = new ItemServices();
        $data = $itemService->getClassSchedulePlan($id);

        return response()->json($data);
    }
    public function updateSchadule(Request $request)
    {
        $itemService = new ItemServices();
        $input = $request->get('input');
        $data = $itemService->updateClassSchedule($request, $input);
        return response()->json($data);
    }
}
