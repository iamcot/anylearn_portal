<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ConfigConstants;
use App\Constants\FileConstants;
use App\Constants\UserConstants;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\User;
use App\Services\FileServices;
use App\Services\ItemServices;
use App\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConfigApi extends Controller
{
    public function home($role = 'guest') {
        $fileService = new FileServices();
        $bannersDriver = $fileService->getAllFiles(FileConstants::DISK_S3, FileConstants::FOLDER_BANNERS);
        $banners = [];
        foreach($bannersDriver as $file) {
            $banners[] = $fileService->urlFromPath(FileConstants::DISK_S3, $file);
        }
        $userService = new UserServices();
        $hotSchools = $userService->hotUsers(UserConstants::ROLE_SCHOOL);
        $hotTeachers = $userService->hotUsers(UserConstants::ROLE_TEACHER);

        $itemService = new ItemServices();
        $monthItems = $itemService->monthItems();

        return response()->json([
            'banners' => $banners,
            'hot_items' => [
                $hotSchools,
                $hotTeachers
            ],
            'month_courses' => $monthItems,
        ]);
    }
}