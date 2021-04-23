<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ConfigConstants;
use App\Constants\FileConstants;
use App\Constants\OrderConstants;
use App\Constants\UserConstants;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Configuration;
use App\Models\Feedback;
use App\Models\Item;
use App\Models\Schedule;
use App\Models\Tag;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FileServices;
use App\Services\ItemServices;
use App\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConfigApi extends Controller
{
    public function home($role = 'guest')
    {
        $fileService = new FileServices();
        $bannersDriver = $fileService->getAllFiles(FileConstants::DISK_S3, FileConstants::FOLDER_BANNERS);
        $banners = [];
        if ($bannersDriver != null) {
            foreach ($bannersDriver as $file) {
                $banners[] = $fileService->urlFromPath(FileConstants::DISK_S3, $file);
            }
        }

        $userService = new UserServices();
        $hotSchools = $userService->hotUsers(UserConstants::ROLE_SCHOOL);
        $hotTeachers = $userService->hotUsers(UserConstants::ROLE_TEACHER);
        $hotUserInCate = $userService->hotUsers(UserConstants::ROLE_TEACHER, 1);

        $itemService = new ItemServices();
        $monthItems = $itemService->monthItems();

        $homeConfig = config('home_config');
        $lastConfig = Configuration::where('key', ConfigConstants::CONFIG_HOME_POPUP)->first();
        if (!empty($lastConfig)) {
            $homePopup = json_decode($lastConfig->value, true);
            if ($homePopup['status'] == 1) {
                $homeConfig['popup'] = $homePopup;
            }
        }

        return response()->json([
            'banners' => $banners,
            'hot_items' => [
                $hotSchools,
                $hotTeachers,
                // $hotUserInCate,
            ],
            'month_courses' => $monthItems,
            'articles' => Article::where('status', 1)->orderby('id', 'desc')->take(5)->get()->makeHidden(['content']),
            'configs' => $homeConfig,
        ]);
    }

    public function transaction(Request $request, $type)
    {
        $user = $request->get('_user');

        $trans = Transaction::where('user_id', $user->id)
            ->where('type', $type)
            ->orderby('id', 'desc')
            ->take(5)->get();

        $configM = new Configuration();
        $rate = $configM->get(ConfigConstants::CONFIG_BONUS_RATE);
        $transM = new Transaction();
        $config = [
            'pending_wallet_m' => (int) $transM->pendingWalletM($user->id),
            'pending_wallet_c' => (int) $transM->pendingWalletC($user->id),
            'suggest' => [930000, 5000000, 2000000, 1000000, 500000, 200000],
            'vip_fee' => 0,
            'vip_days' => 30,
            'bank' => [
                'bank_name' => 'Ngân hàng Quốc Tế VIB',
                'bank_no' => '002159817',
                'bank_branch' => 'CN Hoàng Văn Thái',
                'account_name' => 'Công ty cổ phần Đầu Tư và Giáo dục anyLEARN',
                'content' => 'Nap tien anyLEARN',
            ],
            'suggest_columns' => 3,
            'rate' => (int)$rate,
            'transactions' => $trans,
        ];
        return response()->json($config);
    }

    public function foundation()
    {
        $configM = new Configuration();
        $configs = $configM->gets([ConfigConstants::CONFIG_IOS_TRANSACTION]);

        $foundation = Transaction::where('type', ConfigConstants::TRANSACTION_FOUNDATION)
            ->sum('amount');
        $data = [
            'value' => (int) $foundation,
            'ios_transaction' => (int)$configs[ConfigConstants::CONFIG_IOS_TRANSACTION],
            'history' => Transaction::where('type', ConfigConstants::TRANSACTION_FOUNDATION)
                ->orderby('id', 'desc')->take(20)->get(),
            'news' => DB::table('tags')->where('tags.type', Tag::TYPE_ARTICLE)
                ->where('tags.tag', ConfigConstants::FOUNDATION_TAG)
                ->join('articles', 'articles.id', '=', 'tags.item_id')
                ->where('articles.status', 1)
                ->orderBy('articles.id', 'desc')
                ->take(20)->get(),
        ];

        return response()->json($data);
    }

    public function getDoc($key)
    {
        $configM = new Configuration();
        $data = $configM->getDoc($key);
        if (!$data) {
            return response('Trang không tìm thấy', 404);
        }
        return response()->json([
            'content' => $data->value,
            'updated_at' => $data->updated_at,
        ]);
    }

    public function event(Request $request, $month)
    {

        $startDay = $month . "-01";
        $endDay = $month . "-31";
        // $db = Item::where('date_start', '>=', $startDay)
        //     ->where('date_start', '<=', $endDay)
        //     ->where('status', 1)
        //     ->where('user_status', 1)
        //     ->with('user')
        //     ->get();
        $db = DB::table('schedules')
            ->join('items', 'items.id', '=', 'schedules.item_id')
            ->join('users', 'users.id', '=', 'items.user_id')
            ->where('schedules.date', '>=', $startDay)
            ->where('schedules.date', '<=', $endDay)
            ->where('items.status', 1)
            ->where('items.user_status', 1)
            ->where('users.status', 1)
            ->where('items.is_test', 0)
            ->select(
                'items.id',
                'items.id AS item_id',
                'items.title',
                'schedules.date',
                'schedules.time_start',
                'users.name as author',
                'items.image',
                'items.short_content'
            )
            ->get();
        // $user = $this->isAuthedApi($request);
        // if ($user instanceof User) {
        // }
        $data = [];
        if ($db) {
            foreach ($db as $event) {
                $data[$event->date][] = [
                    'id' => $event->id,
                    'item_id' => $event->id,
                    'title' => $event->title,
                    'date' => $event->date,
                    'time' => $event->time_start,
                    'author' => $event->author,
                    'image' => $event->image,
                    'content' => $event->short_content,
                ];
            }
        }
        return response()->json($data);
    }

    public function saveFeedback(Request $request)
    {
        $user = $request->get('_user');

        $fileService = new FileServices();
        $fileuploaded = $fileService->doUploadImage($request, 'image', FileConstants::DISK_S3, true, 'feedbacks');
        Feedback::create([
            'user_id' => $user->id,
            'content' => $request->get('content'),
            'file' => $fileuploaded['url'],
        ]);
        return response()->json([
            'result' => true
        ]);
    }

    public function search(Request $request)
    {
        $type = $request->get('t', 'item');
        $screen = $request->get('s', '');
        $query = $request->get('q', '');
        if (empty($query)) {
            return response()->json(null);
        }
        $result = null;
        if ($type == 'user') {
            $result = User::where('status', 1);
            if ($screen == UserConstants::ROLE_SCHOOL || $screen == UserConstants::ROLE_TEACHER) {
                $result = $result->where('role', $screen);
            }
            $result = $result->where('name', 'like', "%$query%")
                ->orderby('boost_score', 'desc')
                ->orderby('is_hot', 'desc')
                ->orderby('first_name')
                ->get();
        } else {
            $result = DB::table('items')
                ->where('items.status', 1)
                ->where('items.user_status', '>', 0)
                ->join('users', 'users.id', '=', 'items.user_id')
                ->where('items.title', 'like', "%$query%")
                ->select('items.*', 'users.name AS author', 'users.role AS author_type')
                ->orderby('items.is_hot', 'desc')
                ->orderby('users.is_hot', 'desc')
                ->orderby('users.boost_score', 'desc')
                ->get();
        }
        return response()->json($result);
    }

    public function searchTags()
    {
        $db = Tag::select('tag')->get();
        $data = [];
        foreach ($db as $tag) {
            $data[] = $tag->tag;
        }
        return response()->json($data);
    }
}
