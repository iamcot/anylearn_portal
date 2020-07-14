<?php

namespace App\Services;

use App\Constants\ConfigConstants;
use App\Constants\UserConstants;
use App\Constants\UserDocConstants;
use App\Models\Configuration;
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

    public function isMod()
    {
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

    public function requiredDocIcon($user)
    {
        if ($user->role == UserConstants::ROLE_MEMBER) {
            return '';
        }
        if ($user->update_doc == UserConstants::STATUS_ACTIVE) {
            return '<a class="check_doc" href="#" data-id="' . $user->id . '"><i class="fas fa-cloud-upload-alt text-success" title="Đã cập nhật"></i></a>';
        } else {
            return '<a class="check_doc text-black-50" href="#" data-id="' . $user->id . '"><i class="fas fa-cloud-upload-alt text-gray" title="Chưa cập nhật"></i></a>';
        }
    }

    public function hotUsers($role, $catId = 0)
    {
        $title = $role == UserConstants::ROLE_TEACHER ? "anyProfessor" : "anyCenter";
        if ($catId > 0) {
            $title = 'anyKinder';
        }
        $route = $role == UserConstants::ROLE_TEACHER ? "/teacher" : "/school";
        $configM = new Configuration();
        $keyConfig = $role == UserConstants::ROLE_TEACHER ? ConfigConstants::CONFIG_NUM_TEACHER : ConfigConstants::CONFIG_NUM_SCHOOL;
        $pageSize = $configM->get($keyConfig);
        $list = User::where('role', $role)
            ->where('update_doc', UserConstants::STATUS_ACTIVE)
            ->where('status', UserConstants::STATUS_ACTIVE)
            ->where('user_category_id', $catId)
            ->where('is_hot', 1)
            ->orderby('boost_score', 'desc')
            ->orderby('first_name')
            ->select('id', 'name', 'role', 'image', 'banner', 'introduce', 'title')
            ->take($pageSize)->get();
        return [
            'title' => $title,
            'route' => $route,
            'list' => $list,
        ];
    }

    public function calcCommission($price, $bonusSeller, $bonusRate, $exchangeRate) {
        $netValue = $price * (1 - $bonusSeller);
        return  floor($netValue * $bonusRate / $exchangeRate);
    }
}
