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
        $newItem = $itemService->createItem($request->all(), ItemConstants::TYPE_CLASS, $user);
        if ($newItem instanceof Validator) {
            return response($newItem->errors()->first(), 400);
        }
        return response()->json(['result' => $newItem === false ? false : true]);
    }
    public function edit(Request $request, $id)
    {
        $user = $request->get('_user');

        $item = Item::where('id', $id)
            ->where('user_id', $user->id)
            ->first()->makeVisible(['content']);
        if (!$item) {
            return response("Không có dữ liệu", 404);
        }
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
        if ($newItem instanceof Validator) {
            return response($newItem->errors()->first(), 400);
        }
        return response()->json(['result' => $newItem === false ? false : true]);
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
        $user = User::find($userId);
        if (!$user) {
            return response('Trang không tồn tại', 404);
        }
        $pageSize = $request->get('pageSize', 9999);
        $items = DB::table('items')
            ->where('user_id', $userId)
            // ->where('update_doc', UserConstants::STATUS_ACTIVE)
            ->where('status', ItemConstants::STATUS_ACTIVE)
            ->where('user_status', '>', ItemConstants::STATUS_INACTIVE)
            ->orderby('is_hot', 'desc')
            ->orderby('id', 'desc')
            ->select(
                'items.*',
                DB::raw("(select avg(iua.value) from item_user_actions AS iua WHERE type = 'rating' AND iua.item_id = items.id) AS rating")
            )
            ->paginate($pageSize);
        return response()->json([
            'user' => $user,
            'items' => $items,
        ], 200);
    }

    public function pdp(Request $request, $itemId)
    {
        $item = Item::find($itemId)->makeVisible(['content']);
        if (!$item) {
            return response('Trang không tồn tại', 404);
        }
        $item->content = "<html><body>" . $item->content . "</body></html>";
        $configM = new Configuration();
        $configs = $configM->gets([ConfigConstants::CONFIG_IOS_TRANSACTION, ConfigConstants::CONFIG_BONUS_RATE, ConfigConstants::CONFIG_DISCOUNT]);
        $author = User::find($item->user_id);
        
        $userService = new UserServices();
        $authorCommissionRate = $item->commission_rate > 0 ? $item->commission_rate : $author->commission_rate;
        $commission = $userService->calcCommission($item->price, $authorCommissionRate, $configs[ConfigConstants::CONFIG_DISCOUNT], $configs[ConfigConstants::CONFIG_BONUS_RATE]);
        $hotItems = Item::where('status', ItemConstants::STATUS_ACTIVE)
            ->where('user_status', ItemConstants::STATUS_ACTIVE)
            ->where('id', '!=', $itemId)
            ->orderby('is_hot', 'desc')
            ->orderby('id', 'desc')
            ->take(5)->get();

        $numSchedule = Schedule::where('item_id', $itemId)->count();

        $itemUserActionM = new ItemUserAction();
        $user = $this->isAuthedApi($request);
        $item->num_favorite = $itemUserActionM->numFav($itemId);
        $item->num_cart = $itemUserActionM->numReg($itemId);
        $item->rating = $itemUserActionM->rating($itemId);

        return response()->json([
            'commission' => $commission,
            'author' => $author,
            'item' => $item,
            'num_schedule' => $numSchedule,
            'ios_transaction' => (int)$configs[ConfigConstants::CONFIG_IOS_TRANSACTION],
            'is_fav' =>  !($user instanceof User) ? false : $itemUserActionM->isFav($itemId, $user->id),
            'hotItems' =>  [
                'route' => '/event',
                'title' => 'Sản phẩm liên quan',
                'list' => $hotItems
            ],
        ]);
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
}
