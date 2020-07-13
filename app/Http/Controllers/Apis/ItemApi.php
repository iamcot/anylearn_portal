<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ConfigConstants;
use App\Constants\FileConstants;
use App\Constants\ItemConstants;
use App\Constants\UserConstants;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Item;
use App\Models\User;
use App\Services\FileServices;
use App\Services\ItemServices;
use App\Services\UserServices;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ItemApi extends Controller
{
    public function create(Request $request)
    {
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }
        if (empty($request->get('type'))) {
            return response('Chưa có loại khóa học', 400);
        }
        $itemService = new ItemServices();
        $newItem = $itemService->createItem($request->all(), $request->get('type'), $user);
        if ($newItem instanceof Validator) {
            return response($newItem->errors()->first(), 400);
        }
        return response()->json(['result' => $newItem === false ? false : true]);
    }
    public function edit(Request $request, $id)
    {
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }

        $item = Item::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        if (!$item) {
            return response("Không có dữ liệu", 404);
        }
        return response()->json($item);
    }

    public function save(Request $request, $id)
    {
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }
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
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }
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
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }

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
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
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
        $items = Item::where('user_id', $userId)
            // ->where('update_doc', UserConstants::STATUS_ACTIVE)
            ->where('status', ItemConstants::STATUS_ACTIVE)
            ->where('user_status', '>', ItemConstants::STATUS_INACTIVE)
            ->orderby('is_hot', 'desc')
            ->orderby('id', 'desc')
            ->paginate($pageSize);
        return response()->json([
            'user' => $user,
            'items' => $items,
        ], 200);
    }

    public function pdp(Request $request, $itemId)
    {
        $item = Item::find($itemId);
        if (!$item) {
            return response('Trang không tồn tại', 404);
        }
        $item->content = "<html><body>" . $item->content . "</body></html>";
        $configM = new Configuration();
        $configs = $configM->gets([ConfigConstants::CONFIG_BONUS_RATE, ConfigConstants::CONFIG_DISCOUNT]);
        $author = User::find($item->user_id);

        $userService = new UserServices();
        $commission = $userService->calcCommission($item->price, $author->commission_rate, $configs[ConfigConstants::CONFIG_DISCOUNT], $configs[ConfigConstants::CONFIG_BONUS_RATE]);
        $hotItems = Item::where('status', ItemConstants::STATUS_ACTIVE)
            ->where('user_status', ItemConstants::STATUS_ACTIVE)
            ->where('id', '!=', $itemId)
            ->orderby('is_hot', 'desc')
            ->orderby('id', 'desc')
            ->take(5)->get();
        return response()->json([
            'commission' => $commission,
            'author' => $author,
            'item' => $item,
            'hotItems' => [
                'route' => '/event',
                'title' => 'Sản phẩm liên quan',
                'list' => $hotItems
            ],
        ]);
    }
}
