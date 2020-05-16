<?php

namespace App\Services;

use App\Constants\FileConstants;
use App\Constants\ItemConstants;
use App\Constants\UserConstants;
use App\Models\CourseSeries;
use App\Models\Item;
use App\Models\ItemResource;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ItemServices
{
    const PP = 20;

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
        $courses = $courses->orderby('id', 'desc')
            ->with('series')
            ->paginate(self::PP);
        return $courses;
    }

    public function itemResources($courseId)
    {
        $files = ItemResource::where('item_id', $courseId)->get();
        if ($files) {
            $files = $files->toArray();
        }
        $fileService = new FileServices();
        for ($i = 0; $i < sizeof($files); $i++) {
            $files[$i]['data'] = $fileService->urlFromPath(FileConstants::DISK_COURSE, $files[$i]['data']);
        }
        return $files;
    }

    public function itemInfo($courseId)
    {
        $item = Item::find($courseId);
        if (!$item) {
            return false;
        }
        $item->image = $this->itemImageUrl($item->image);

        $data['info'] = $item;
        $data['resource'] = $this->itemResources($courseId);
        $data['schedule'] = Schedule::where('item_id', $courseId)->get();
        return $data;
    }

    public function createItem($input, $itemType = ItemConstants::TYPE_COURSE)
    {
        $user = Auth::user();
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

        $newCourse = Item::create($input);
        if ($newCourse) {
            if ($newCourse->type == ItemConstants::TYPE_COURSE) {
                Schedule::create([
                    'item_id' => $newCourse->id,
                    'date' => $newCourse->date_start,
                    'time_start' => $newCourse->time_start,
                    'time_end' => $newCourse->time_end,
                    'content' => $newCourse->title,
                ]);
            }
            return $newCourse->id;
        }
        return false;
    }

    public function updateItem(Request $request, $input)
    {
        $user = Auth::user();
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

        $canUpdate = $itemUpdate->update($input);

        if ($canUpdate) {
            if ($itemUpdate->type == ItemConstants::TYPE_COURSE) {
                Schedule::where('item_id', $itemUpdate->id)->update([
                    'date' => $input['date_start'],
                    'time_start' => $input['time_start'],
                    'time_end' => $input['time_end'],
                    'content' => $input['title'],
                ]);
            } elseif ($itemUpdate->type == ItemConstants::TYPE_CLASS) {
                $canUpdateSchedule = $this->updateClassSchedule($request, $input['id']);
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
                    Schedule::find($date['id'])->update([
                        'date' => $date['date'],
                        'time_start' => $date['time_start'],
                        'time_end' => $date['time_end'],
                    ]);
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
            'price' => ['required'],
            'date_start' => ['required'],
            'content' => ['required'],
        ]);
    }

    private function itemImageUrl($path)
    {
        $fileService = new FileServices();
        return $fileService->urlFromPath(FileConstants::DISK_COURSE, $path);
    }

    private function changeItemImage(Request $request, $courseId)
    {
        $fileService = new FileServices();
        $file = $fileService->doUploadImage($request, 'image', FileConstants::DISK_COURSE, true, $courseId);
        if ($file !== false) {
            $this->deleteOldItemImage($courseId);
            return $file['path'];
        }
        return '';
    }

    private function addItemResource(Request $request, $courseId)
    {
        $fileService = new FileServices();
        $file = $fileService->doUploadFile($request, 'resource_data', FileConstants::DISK_COURSE, true, $courseId);
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
        $fileService->deleteFiles([$course->image], FileConstants::DISK_COURSE);
    }
}
