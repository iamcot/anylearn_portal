<?php

namespace App\Services;

use App\Constants\FileConstants;
use App\Constants\ItemConstants;
use App\Constants\UserConstants;
use App\Models\CourseSeries;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CourseServices
{
    const PP = 20;

    public function courseList(Request $request, $userId = null) {
        $courses = Item::where('type', ItemConstants::TYPE_COURSE);
        if ($userId) {
            $courses = $courses->where('user_id', $userId);
        }
        if (!empty($request->input('s'))) {
            switch ($request->input('t')) {
                case "series":
                    $serieseId = '';
                    if (is_numeric($request->input('s')) ) {
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

    public function courseResources($courseId) {
        $files = ItemResource::where('item_id', $courseId)->get();
        if ($files) {
            $files = $files->toArray();
        }
        $fileService = new FileServices();
        for($i = 0; $i < sizeof($files); $i++) {
            $files[$i]['data'] = $fileService->urlFromPath(FileConstants::DISK_COURSE, $files[$i]['data']);
        }
        return $files;
    }

    public function createCourse($input)
    {
        $user = Auth::user();
        $validator = $this->validate($input);
        if ($validator->fails()) {
            return $validator;
        }

        if ($input['series_id'] == ItemConstants::NEW_COURSE_SERIES && !empty($input['series'])) {
            $newSeriesId = $this->createCourseSeries($user->id, $input['series']);
            if ($newSeriesId === false) {
                $validator->errors()->add('series', __('Tạo chuỗi khóa học mới không thành công'));
                return $validator;
            }
            $input['series_id'] = $newSeriesId;
        }

        $input['type'] = ItemConstants::TYPE_COURSE;

        if (in_array($user->role, UserConstants::$modRoles)) {
            $input['user_id'] = ItemConstants::COURSE_SYSTEM_USERID;
        } else {
            $input['user_id'] = $user->id;
        }

        $newCourse = Item::create($input);
        if ($newCourse) {
            return $newCourse->id;
        }
        return false;
    }

    public function courseInfo($courseId)
    {
        $item = Item::find($courseId);
        if (!$item) {
            return false;
        }
        $item->image = $this->courseImageUrl($item->image);
      
        $data['info'] = $item;
        $data['resource'] = $this->courseResources($courseId);
        return $data;
    }

    public function updateCourse(Request $request, $input)
    {
        $user = Auth::user();
        $itemUpdate = Item::find($input['id']);
        if (!in_array($user->role, UserConstants::$modRoles) || $user->id != $itemUpdate->user_id) {
            return false;
        }
        $validator = $this->validate($input);
        if ($validator->fails()) {
            return $validator;
        }

        $canAddResource = $this->addCourseResource($request, $input['id']);

        $user = Auth::user();
        if ($input['series_id'] == ItemConstants::NEW_COURSE_SERIES && !empty($input['series'])) {
            $newSeriesId = $this->createCourseSeries($user->id, $input['series']);
            if ($newSeriesId === false) {
                $validator->errors()->add('series', __('Tạo chuỗi khóa học mới không thành công'));
                return $validator;
            }
            $input['series_id'] = $newSeriesId;
        }
        $courseImage = $this->changeCourseImage($request, $input['id']);
        if($courseImage) {
            $input['image'] = $courseImage;
        }

        return $itemUpdate->update($input);
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
            'title' => ['required', 'string', 'max:255'],
            'price' => ['required'],
            'date_start' => ['required'],
            'content' => ['required'],
        ]);
    }

    private function courseImageUrl($path) {
        $fileService = new FileServices();
        return $fileService->urlFromPath(FileConstants::DISK_COURSE, $path);
    }

    private function changeCourseImage(Request $request, $courseId) {
        $fileService = new FileServices();
        $file = $fileService->doUploadImage($request, 'image', FileConstants::DISK_COURSE, true, $courseId);
        if ($file !== false) {
            $this->deleteOldCourseImage($courseId);
            return $file['path'];
        }
        return '';
    }

    private function addCourseResource(Request $request, $courseId) {
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

    private function deleteOldCourseImage($courseId) {
        $course = Item::find($courseId);
        if (!$course) {
            return true;
        }
        $fileService = new FileServices();
        $fileService->deleteFiles([$course->image], FileConstants::DISK_COURSE );
    }
}
