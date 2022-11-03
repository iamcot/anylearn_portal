<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\models\SocialPost;
use App\Models\User;

class SocialController extends Controller
{
    const PER_PAGE = 10;

    public function profile(Request $request, $userId = null)
    {
        if ($userId) {
            $user = User::find($userId);
        } else {
            $user = $request->get('_user');
        }
        if (!$user) {
            return response('Profile không tồn tại.', 400);
        }
        $user = User::select('id', 'name', 'first_name', 'dob', 'image', 'banner', 'role', 'introduce')->find($userId);

        $dbPosts = SocialPost::where('user_id', $user->id)
            ->where('status', 1)
            ->orderby('day', 'desc')
            ->paginate(self::PER_PAGE);

        $dbPosts->getCollection()->transform(function ($value) use ($user) {
            $value->title = $this->titleFromPostType($user->name, $value->type, $value->ref_id);
            $value->description = "";
            $value->like_counts = SocialPost::where('type', SocialPost::TYPE_ACTION_COMMENT)->where('post_id', $value->id)->count();
            $value->share_counts = SocialPost::where('type', SocialPost::TYPE_ACTION_SHARE)->where('post_id', $value->id)->count();
            $value->user = $user;
            $value->comments = [];
            $value->like = [];
            return $value;
        });
        return response()->json([
            'profile' => $user,
            'post' => $dbPosts,
        ]);
    }

    public function post(Request $request, $postId)
    {
    }

    private function titleFromPostType($userName, $action, $refId)
    {
        switch ($action) {
            case SocialPost::TYPE_CLASS_REGISTER:
                $item = Item::find($refId);
                return __(":name đã đăng ký khóa học :class", [
                    'name' => $userName,
                    'class' => $item->title
                ]);
            case SocialPost::TYPE_CLASS_COMPLETE:
                $item = Item::find($refId);
                return __(":name đã hoàn thành khóa học :class", [
                    'name' => $userName,
                    'class' => $item->title
                ]);
            case SocialPost::TYPE_CLASS_CERT:
                $item = Item::find($refId);
                return __(":name đã đạt được chứng chỉ hoàn thành khóa học :class", [
                    'name' => $userName,
                    'class' => $item->title
                ]);
            case SocialPost::TYPE_CLASS_FAV:
                $item = Item::find($refId);
                return __(":name đã ưa thích khóa học :class", [
                    'name' => $userName,
                    'class' => $item->title
                ]);
            case SocialPost::TYPE_CLASS_REGISTER:
                $item = Item::find($refId);
                return __(":name đã chia sẻ khóa học :class", [
                    'name' => $userName,
                    'class' => $item->title
                ]);
            case SocialPost::TYPE_CLASS_RATING:
                $item = Item::find($refId);
                return __(":name đã đánh giá khóa học :class", [
                    'name' => $userName,
                    'class' => $item->title
                ]);
        }
        return "";
    }
}
