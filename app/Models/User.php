<?php

namespace App\Models;

use App\Constants\ConfigConstants;
use App\Constants\FileConstants;
use App\Constants\UserConstants;
use App\Services\FileServices;
use App\Validators\ValidRef;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    const LOGIN_3RD_FACEBOOK = 'facebook';
    const LOGIN_3RD_APPLE = 'apple';
    const LOGIN_3RD_GOOGLE = 'google';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'api_token', 'notif_token', 'phone', 'role', 'status', 'user_id',
        'expire', 'wallet_m', 'wallet_c', 'commission_rate', 'is_hot', 'image', 'introduce',
        'address', 'country', 'dob', 'update_doc', 'user_category_id', 'boost_score',
        'refcode', 'title', 'num_friends', 'package_id', 'banner', 'first_name', 'full_content',
        'is_test', 'is_signed', 'dob_place', '3rd_id', '3rd_type', '3rd_token', 'is_child'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token', 'full_content'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function refuser()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function validateMember($data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['string', 'email', 'max:255'],
            'phone' => ['required', 'min:10', 'max:10', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'ref' => [new ValidRef()],
            'role' => ['required', 'in:member,teacher,school'],
        ]);
    }

    public function createNewMember($data)
    {
        $obj = [
            'name' => $data['name'],
            'email' => isset($data['email']) ? $data['email'] : null,
            'phone' => $data['phone'],
            'dob' => isset($data['dob']) ? $data['dob'] : null,
            'address' => isset($data['address']) ? $data['address'] : null,
            'role' => $data['role'],
            'country' => isset($data['country']) ? $data['country'] : null,
            'password' => Hash::make($data['password']),
            'status' => UserConstants::STATUS_ACTIVE,
            'update_doc' => UserConstants::STATUS_ACTIVE,
            'refcode' => $data['phone'],

        ];
        $obj['first_name'] = in_array($data['role'], [UserConstants::ROLE_TEACHER, UserConstants::ROLE_MEMBER]) ? $this->firstnameFromName($data['name']) : $data['name'];
        if (!empty($data['ref'])) {
            $refUser = $this->where('refcode', $data['ref'])->first();
            $obj['user_id'] = $refUser->id;
        }
        if ($data['role'] == UserConstants::ROLE_SCHOOL || $data['role'] == UserConstants::ROLE_TEACHER) {
            $obj['update_doc'] = UserConstants::STATUS_INACTIVE;
            $configM = new Configuration();
            $configs = $configM->gets([
                ConfigConstants::CONFIG_COMMISSION_AUTHOR,
                ConfigConstants::CONFIG_COMMISSION_SCHOOL
            ]);
            $obj['commission_rate'] = $data['role'] == UserConstants::ROLE_TEACHER 
            ? $configs[ConfigConstants::CONFIG_COMMISSION_AUTHOR] 
            : $configs[ConfigConstants::CONFIG_COMMISSION_SCHOOL];
        }

        $newMember = $this->create($obj);
        if ($newMember) {
            $notifM = new Notification();
            $notifM->notifNewUser($newMember->id, $newMember->name);
            if ($newMember->user_id > 0) {
                $notifM->notifNewFriend($newMember->user_id, $newMember->name);
            }

            // if (!empty($newMember->user_id)) {
            $voucherEvent = new VoucherEventLog();
            $voucherEvent->useEvent(VoucherEvent::TYPE_REGISTER, $newMember->id, $newMember->user_id ?? 0);
            // }
        }
        $this->updateUpTree($newMember->user_id);
        return $newMember;
    }

    public function createNewMod($input)
    {
        $obj = [
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'role' => $input['role'],
            'password' => Hash::make($input['password']),
            'status' => UserConstants::STATUS_ACTIVE,
        ];
        return $this->create($obj) ? 1 : 0;
    }

    public function saveMod($input)
    {
        if (empty($input['id'])) {
            return 0;
        }
        $obj = [
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'role' => $input['role'],

        ];
        if (!empty($input['password'])) {
            $obj['password'] = Hash::make($input['password']);
        }
        return $this->find($input['id'])->update($obj);
    }

    public function saveMember(Request $request, $input)
    {
        if (empty($input['id'])) {
            return 0;
        }
        $obj = [
            'name' => $input['name'],
            'refcode' => $input['refcode'],
            'title' => $input['title'],
            'introduce' => $input['introduce'],
            'full_content' => $input['full_content'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'role' => $input['role'],
            'user_id' => $input['user_id'],
            'boost_score' => $input['boost_score'],
            'commission_rate' => $input['commission_rate'],

        ];
        if (!empty($input['password'])) {
            $obj['password'] = Hash::make($input['password']);
        }
        $obj['first_name'] = in_array($input['role'], [UserConstants::ROLE_TEACHER, UserConstants::ROLE_MEMBER]) ? $this->firstnameFromName($input['name']) : $input['name'];

        $fileService = new FileServices();
        $avatar = $fileService->doUploadImage($request, 'image');
        $banner = $fileService->doUploadImage($request, 'banner');

        $currentData = $this->find($input['id']);
        $needDelete = [];
        if (!empty($avatar['url'])) {
            $needDelete[] = $currentData->image;
            $obj['image'] = $avatar['url'];
        }
        if (!empty($banner['url'])) {
            $needDelete[] = $currentData->banner;
            $obj['banner'] = $banner['url'];
        }

        $rs = $this->find($input['id'])->update($obj);
        if ($rs) {
            $fileService->deleteFiles($needDelete);
        }
        return $rs;
    }


    public function redirectToUpdateDocs()
    {
        if ($this->needUpdateDocs()) {
            return route('user.update_doc');
        }
        return '/admin';
    }

    public function needUpdateDocs()
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        return ($user->role == UserConstants::ROLE_TEACHER || $user->role == UserConstants::ROLE_SCHOOL)
            && $user->update_doc == UserConstants::STATUS_INACTIVE ? true : false;
    }

    public function searchMembers(Request $request)
    {
        $members = User::whereIn('role', UserConstants::$memberRoles);
        if (!empty($request->input('s'))) {
            switch ($request->input('t')) {
                case "phone":
                    $members = $members->where('phone', $request->input('s'));
                    break;
                case "role":
                    $members = $members->where('role', $request->input('s'));
                    break;
                default:
                    $members = $members->where('name', 'like', '%' . $request->input('s') . '%');
                    break;
            }
        }
        $members = $members
            ->orderby('is_hot', 'desc')
            ->orderby('boost_score', 'desc')
            ->orderby('id', 'desc')
            ->with('refuser')
            ->paginate(UserConstants::PP);

        return $members;
    }

    public function validateUpdate($userId, $input)
    {
        if (!empty($input['phone'])) {
            $phoneExists = $this->where('phone', $input['phone'])
                ->where('id', '!=', $userId)->count();
            if ($phoneExists) {
                return "Số điện thoại này đã được sử dụng";
            }
        }
        if (!empty($input['refcode'])) {
            $refExists = $this->where('refcode', $input['refcode'])
                ->where('id', '!=', $userId)->count();
            if ($refExists) {
                return "Mã giới thiệu này đã được sử dụng";
            }
        }
        return "";
    }

    public function correctTreeNum()
    {
        $usersNoUpTree = DB::table('users')->whereNull('user_id')->get();
        foreach ($usersNoUpTree as $user) {
            print("\n- start root node " . $user->id);
            $this->updateTreeNum($user->id);
        }
    }

    private function updateTreeNum($userId = null, $level = 2)
    {
        $tab = "";
        for ($i = 1; $i <= $level; $i++) {
            $tab .= "-";
        }

        $friends = $this->where('user_id', $userId)->get();
        if (count($friends) == 0) {
            print("\n" . $tab . " node " . $userId . " 0 friend");
            $this->find($userId)->update([
                'num_friends' => 0,
            ]);
            return 1;
        }
        print("\n" . $tab . " friends of node " . $userId);

        $numFriends = 0;
        foreach ($friends as $friend) {
            $numFriends += $this->updateTreeNum($friend->id, $level + 1);
        }

        print("\n" . $tab . " update node " . $userId . " = " . $numFriends);
        $this->find($userId)->update([
            'num_friends' => $numFriends,
        ]);
        return 1 + $numFriends;
    }

    private function firstnameFromName($name)
    {
        $splitWords = explode(' ', $name);
        return end($splitWords);
    }

    private function updateUpTree($upUserId)
    {
        $upUser = $this->find($upUserId);
        if (!$upUser) {
            return;
        }
        $upUser->update(['num_friends' => $upUser->num_friends + 1]);
        if ($upUser->user_id > 0) {
            $this->updateUpTree($upUser->user_id);
        }
        return;
    }
}
