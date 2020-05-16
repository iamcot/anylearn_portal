<?php

namespace App\Services;

use App\Constants\UserConstants;
use App\Constants\UserDocConstants;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserServices
{
    private $roles = [
        UserConstants::ROLE_ADMIN => [],
        UserConstants::ROLE_MOD => [],
        UserConstants::ROLE_TEACHER => [
            'course',
        ],
        UserConstants::ROLE_SCHOOL => [
            'course',
            'school'
        ],
    ];

    private $blocked = [
        UserConstants::ROLE_MOD => [
            'root',
        ],
    ];

    public function userRoles()
    {
        return array_keys($this->roles);
    }

    public function isMod() {
        $user = Auth::user();
        return in_array($user->role, UserConstants::$modRoles);
    }

    public function haveAccess($role, $routeName)
    {
        if (!isset($this->roles[$role])) {
            return false;
        }
        $grantAccess = $this->roles[$role];
        if (empty($grantAccess)) {
            if (isset($this->blocked[$role]) && in_array($routeName, $this->blocked[$role])) {
                return  false;
            }
            return true;
        }
        if (in_array($routeName, $grantAccess)) {
            return true;
        }
        return false;
    }

    public function statusText($status)
    {
        if (!isset(UserConstants::$statusText[$status])) {
            return "N/A";
        }
        return UserConstants::$statusText[$status];
    }

    public function statusOperation($userId, $status)
    {
        if ($status == UserConstants::STATUS_ACTIVE) {
            return '<a class="btn btn-sm btn-danger" href="' . route('user.status.touch', ['userId' => $userId]) . '"><i class="fas fa-lock"></i> Khóa</a>';
        } else {
            return '<a class="btn btn-sm btn-success" href="' . route('user.status.touch', ['userId' => $userId]) . '"><i class="fas fa-unlock"></i> Mở khóa</a>';
        }
    }

    public function statusIcon($status)
    {
        if ($status == UserConstants::STATUS_ACTIVE) {
            return '<i class="fas fa-check-circle text-success" title="Đang hoạt động"></i>';
        } else {
            return '<i class="fas fa-stop-circle text-danger" title="Tạm khóa"></i>';
        }
    }

    public function hotIcon($status)
    {
        if ($status == UserConstants::STATUS_ACTIVE) {
            return '<i class="fas fa-fire text-danger" title="Nổi bật"></i>';
        } else {
            return '<i class="fas fa-fire text-black-50" title="Bình thường"></i>';
        }
    }

    public function requiredDocIcon($userId, $isUpdateDoc)
    {
        if ($isUpdateDoc == UserConstants::STATUS_ACTIVE) {
            return '<a class="check_doc" href="#" data-id="' . $userId . '"><i class="fas fa-cloud-upload-alt text-success" title="Đã cập nhật"></i></a>';
        } else {
            return '<i class="fas fa-cloud-upload-alt text-gray" title="Chưa cập nhật"></i>';
        }
    }
}
