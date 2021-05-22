<?php

namespace App\Services;

use App\Constants\ConfigConstants;
use App\Constants\FileConstants;
use App\Constants\ItemConstants;
use App\Constants\NotifConstants;
use App\Constants\UserConstants;
use App\Models\ClassTeacher;
use App\Models\Configuration;
use App\Models\CourseSeries;
use App\Models\Item;
use App\Models\ItemResource;
use App\Models\ItemUserAction;
use App\Models\Notification;
use App\Models\OrderDetail;
use App\Models\Schedule;
use App\Models\Tag;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ItemServices
{
    const PP = 20;

    public function pdpData($itemId, $user) {
        $item = Item::find($itemId);
        if (!$item) {
            throw new Exception("Trang không tồn tại", 404);
        }
        $item = $item->makeVisible(['content']);
        // $item->content = "<html><body>" . $item->content . "</body></html>";
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
        $item->num_favorite = $itemUserActionM->numFav($itemId);
        $item->num_cart = $itemUserActionM->numReg($itemId);
        $item->rating = $itemUserActionM->rating($itemId);
        return [
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
        ];
    }

    public function classUrl($id) {
        $item = Item::find($id);
        if (!$item) {
            return "";
        }
        return route('page.pdp', ['id' => $id, 'url' => Str::slug($item->title). '.html']);
    }

    public function itemList(Request $request, $userId = null, $itemType = ItemConstants::TYPE_COURSE)
    {
        $courses = Item::where('type', $itemType);
        if ($userId) {
            $courses = $courses->where('user_id', $userId);
        }
        if (!empty($request->input('s'))) {
            switch ($request->input('t')) {
                case "series":
                    $serieseId = '';
                    if (is_numeric($request->input('s'))) {
                        $serieseId = $request->input('s');
                    } else {
                        $seriesDB = CourseSeries::where('title', $request->input('s'))->first();
                        if ($seriesDB) {
                            $serieseId = $seriesDB->id;
                        }
                    }
                    $courses = $courses->where('series_id', $serieseId);
                    break;
                default:
                    $courses = $courses->where('title', 'like', '%' . $request->input('s') . '%');
                    break;
            }
        }
        $courses = $courses->orderby('is_hot', 'desc')
            ->orderby('id', 'desc')
            ->select(
                'items.*',
                DB::raw('(select count(*) from order_details where order_details.item_id = items.id) AS sum_reg')
            )
            ->with('series', 'user')
            ->paginate(self::PP);
        return $courses;
    }

    public function statusOperation($itemId, $status)
    {
        if ($status == ItemConstants::STATUS_ACTIVE) {
            return '<a class="btn btn-sm btn-danger" href="' . route('item.status.touch', ['itemId' => $itemId]) . '"><i class="fas fa-lock"></i> Khóa</a>';
        } else {
            return '<a class="btn btn-sm btn-success" href="' . route('item.status.touch', ['itemId' => $itemId]) . '"><i class="fas fa-unlock"></i> Mở</a>';
        }
    }

    public function articleStatusOperation($itemId, $status)
    {
        if ($status == ItemConstants::STATUS_ACTIVE) {
            return '<a class="btn btn-sm btn-danger" href="' . route('article.status.touch', ['articleId' => $itemId]) . '"><i class="fas fa-lock"></i> Khóa</a>';
        } else {
            return '<a class="btn btn-sm btn-success" href="' . route('article.status.touch', ['articleId' => $itemId]) . '"><i class="fas fa-unlock"></i> Mở</a>';
        }
    }

    public function typeOperation($item)
    {
        if ($item->type == ItemConstants::TYPE_COURSE) {
            return '<a class="btn btn-sm btn-info" href="' . route('item.type.change', ['itemId' => $item->id, 'newType' => ItemConstants::TYPE_CLASS]) . '"><i class="fas fa-exchange-alt"></i> Lớp-học</a>';
        } else {
            return '<a class="btn btn-sm btn-info" href="' . route('item.type.change', ['itemId' => $item->id, 'newType' => ItemConstants::TYPE_COURSE]) . '"><i class="fas fa-exchange-alt"></i> Khóa-học</a>';
        }
    }


    public function itemResources($courseId)
    {
        $files = ItemResource::where('item_id', $courseId)->get();
        if ($files) {
            $files = $files->toArray();
        }
        $fileService = new FileServices();
        for ($i = 0; $i < sizeof($files); $i++) {
            $files[$i]['data'] = $fileService->urlFromPath(FileConstants::DISK_S3, $files[$i]['data']);
        }
        return $files;
    }

    public function itemInfo($courseId)
    {
        $item = Item::find($courseId)->makeVisible(['content']);
        if (!$item) {
            return false;
        }
        // $item->image = $this->itemImageUrl($item->image);

        $data['info'] = $item;
        $data['resource'] = $this->itemResources($courseId);
        $data['schedule'] = Schedule::where('item_id', $courseId)->get();
        return $data;
    }

    public function createItem($input, $itemType = ItemConstants::TYPE_CLASS, $userApi = null)
    {
        $user = $userApi ?? Auth::user();
        $validator = $this->validate($input);
        if ($validator->fails()) {
            return $validator;
        }

        if (isset($input['series_id']) && $input['series_id'] == ItemConstants::NEW_COURSE_SERIES && !empty($input['series'])) {
            $newSeriesId = $this->createCourseSeries($user->id, $input['series']);
            if ($newSeriesId === false) {
                $validator->errors()->add('series', __('Tạo chuỗi khóa học mới không thành công'));
                return $validator;
            }
            $input['series_id'] = $newSeriesId;
        }

        $input['type'] = $itemType;
        $input['user_id'] = in_array($user->role, UserConstants::$modRoles) ? ItemConstants::COURSE_SYSTEM_USERID : $user->id;

        $input['is_test'] = $user->is_test;

        if (!empty($input['nolimit_time']) && $input['nolimit_time'] == 'on') {
            $input['nolimit_time'] = 1;
        } else {
            $input['nolimit_time'] = 0;
        }

        $newCourse = Item::create($input);
        if ($newCourse) {
            $tagsModel = new Tag();
            $tagsModel->createTagFromItem($newCourse, Tag::TYPE_CLASS);
            // if ($newCourse->type == ItemConstants::TYPE_COURSE) {
            Schedule::create([
                'item_id' => $newCourse->id,
                'date' => $newCourse->date_start,
                'time_start' => $newCourse->time_start,
                'time_end' => $newCourse->time_end,
                'content' => $newCourse->title,
            ]);
            // }
            return $newCourse->id;
        }
        return false;
    }

    public function updateItem(Request $request, $input, $userApi = null)
    {
        $user = $userApi ?? Auth::user();
        $itemUpdate = Item::find($input['id']);
        if (!in_array($user->role, UserConstants::$modRoles) && $user->id != $itemUpdate->user_id) {
            return false;
        }
        $validator = $this->validate($input);
        if ($validator->fails()) {
            return $validator;
        }

        $canAddResource = $this->addItemResource($request, $input['id']);

        if (isset($input['series_id']) && $input['series_id'] == ItemConstants::NEW_COURSE_SERIES && !empty($input['series'])) {
            $newSeriesId = $this->createCourseSeries($user->id, $input['series']);
            if ($newSeriesId === false) {
                $validator->errors()->add('series', __('Tạo chuỗi khóa học mới không thành công'));
                return $validator;
            }
            $input['series_id'] = $newSeriesId;
        }
        $courseImage = $this->changeItemImage($request, $input['id']);
        if ($courseImage) {
            $input['image'] = $courseImage;
        }

        if (!empty($input['nolimit_time']) && $input['nolimit_time'] == 'on') {
            $input['nolimit_time'] = 1;
        } else {
            $input['nolimit_time'] = 0;
        }

        $this->updateClassTeachers($request, $input['id']);

        $canUpdate = $itemUpdate->update($input);

        if ($canUpdate) {
            $tagsModel = new Tag();
            $tagsModel->createTagFromItem($itemUpdate, Tag::TYPE_CLASS);
            // if ($itemUpdate->type == ItemConstants::TYPE_COURSE) {
            $hasSchedule = Schedule::where('item_id', $itemUpdate->id)->count();
            if ($hasSchedule == 1) {
                Schedule::where('item_id', $itemUpdate->id)->update([
                    'date' => $input['date_start'],
                    'time_start' => $input['time_start'],
                    'time_end' => empty($input['time_end']) ? "" : $input['time_end'],
                    'content' => $input['title'],
                ]);
            }
            //  elseif ($hasSchedule == 0) {
            //     Schedule::create([
            //         'item_id' => $itemUpdate->id,
            //         'date' => $input['date_start'],
            //         'time_start' => $input['time_start'],
            //         'time_end' => $input['time_end'],
            //         'content' => $input['title'],
            //     ]);
            // }

            // } elseif ($itemUpdate->type == ItemConstants::TYPE_CLASS) {
            else {
                $canUpdateSchedule = $this->updateClassSchedule($request, $input['id']);
            }
            // }
            if (
                $itemUpdate->date_start != $input['date_start']
                || $itemUpdate->date_start != $input['time_start']
                || $itemUpdate->location != $input['location']
            ) {
                $registerUsers = OrderDetail::where('item_id', $itemUpdate->id)->get();
                $notifServ = new Notification();
                foreach ($registerUsers as $user) {
                    $notifServ->createNotif(
                        NotifConstants::COURSE_HAS_CHANGED,
                        $user->user_id,
                        [
                            'course' => $itemUpdate->title
                        ]
                    );
                }
            }
        }
        return $canUpdate;
    }

    public function userCourseSeries($userId)
    {
        return CourseSeries::where('user_id', $userId)
            ->orderby('id', 'desc')->get();
    }

    public function encodeDate($date)
    {
        return strtotime($date);
    }

    public function decodeDate($int)
    {
        if ($int == 0) {
            return "";
        }
        return date('Y-m-d', $int);
    }

    private function updateClassTeachers(Request $request, $itemId)
    {
        if ($request->get('tab') == 'teachers') {
            $teachers = $request->get('teachers');
            if (empty($teachers)) {
                return false;
            }
            ClassTeacher::where('class_id', $itemId)->delete();
            $userIds = array_keys($teachers);
            foreach ($userIds as $userId) {
                ClassTeacher::create([
                    'class_id' => $itemId,
                    'user_id' => $userId,
                ]);
            }
            return true;
        }

        return false;
    }

    private function updateClassSchedule(Request $request, $itemId)
    {
        $schedule = $request->get('schedule');
        if (!empty($schedule)) {
            foreach ($schedule as $date) {
                if (empty($date['date'])) {
                    continue;
                }
                if (empty($date['id'])) {
                    Schedule::create([
                        'item_id' => $itemId,
                        'date' => $date['date'],
                        'time_start' => $date['time_start'],
                        'time_end' => $date['time_end'],
                    ]);
                } else {
                    $data = [
                        'date' => $date['date'],
                        'time_start' => $date['time_start'],
                        'time_end' => $date['time_end'],
                    ];
                    Schedule::find($date['id'])->update($data);
                }
            }
        }
        return true;
    }

    private function createCourseSeries($userId, $courseName)
    {
        $checkExists = CourseSeries::where('title', $courseName)->count();
        if ($checkExists > 0) {
            return false;
        }
        $newCourse = CourseSeries::create([
            'user_id' => $userId,
            'title' => $courseName,
        ]);
        if ($newCourse) {
            return $newCourse->id;
        }
        return false;
    }

    private function validate($data)
    {
        return Validator::make($data, [
            'title' => ['required', 'string', 'max:250'],
            'price' => ['required', 'integer'],
            'date_start' => ['required'],
        ]);
    }

    private function itemImageUrl($path)
    {
        $fileService = new FileServices();
        return $fileService->urlFromPath(FileConstants::DISK_S3, $path);
    }

    private function changeItemImage(Request $request, $courseId)
    {
        $fileService = new FileServices();
        $file = $fileService->doUploadImage($request, 'image', FileConstants::DISK_S3, true, FileConstants::FOLDER_ITEMS . '/' . $courseId);
        if ($file !== false) {

            $this->deleteOldItemImage($courseId);
            return $file['url'];
        }
        return '';
    }

    private function addItemResource(Request $request, $courseId)
    {
        $fileService = new FileServices();
        $file = $fileService->doUploadFile($request, 'resource_data', FileConstants::DISK_S3, true, FileConstants::FOLDER_ITEMS . '/' . $courseId);
        if ($file !== false) {
            $resource = $request->get('resource');
            $db = ItemResource::create([
                'item_id' => $courseId,
                'type' => $file['file_ext'],
                'title' => $resource['title'],
                'desc' => $resource['desc'],
                'data' => $file['path'],
            ]);
            return true;
        }
        return false;
    }

    private function deleteOldItemImage($courseId)
    {
        $course = Item::find($courseId);
        if (!$course) {
            return true;
        }
        $fileService = new FileServices();
        $fileService->deleteUserOldImageOnS3($course->image);
        // $fileService->deleteFiles([$course->image], FileConstants::DISK_S3);
    }

    public function monthItems()
    {
        $configM = new Configuration();
        $pageSize = $configM->get(ConfigConstants::CONFIG_NUM_COURSE);
        $list = Item::whereIn('type', [ItemConstants::TYPE_COURSE, ItemConstants::TYPE_CLASS])
            // ->where('update_doc', UserConstants::STATUS_ACTIVE)
            ->where('status', UserConstants::STATUS_ACTIVE)
            ->where('date_start', '>=', date('Y-m-d'))
            ->where('user_status', ItemConstants::USERSTATUS_ACTIVE)
            ->where('is_hot', 1)
            ->orderby('date_start', 'asc')
            ->take($pageSize)->get();
        return $list;
    }
}
