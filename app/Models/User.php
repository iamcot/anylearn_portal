<?php

namespace App\Models;

use App\Constants\UserConstants;
use App\Validators\ValidRef;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'api_token', 'phone', 'role', 'status', 'user_id',
        'expire', 'wallet_m', 'wallet_c', 'commission_rate', 'is_hot', 'image', 'introduce',
        'introduce', 'address', 'country', 'dob', 'update_doc',
        'refcode', 'title', 'num_friends', 'package_id', 'banner',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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
        if ($data['ref']) {
            $refUser = $this->where('phone', $data['ref'])->first();
            $obj['user_id'] = $refUser->id;
        }
        if ($data['role'] == UserConstants::ROLE_SCHOOL || $data['role'] == UserConstants::ROLE_TEACHER) {
            $obj['update_doc'] = UserConstants::STATUS_INACTIVE;
        }

        return $this->create($obj);
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

    public function saveMember($input)
    {
        if (empty($input['id'])) {
            return 0;
        }
        $obj = [
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'role' => $input['role'],
            'user_id' => $input['user_id'],
            'commission_rate' => $input['commission_rate'],

        ];
        if (!empty($input['password'])) {
            $obj['password'] = Hash::make($input['password']);
        }
        return $this->find($input['id'])->update($obj);
    }


    public function redirectToUpdateDocs()
    {
        if ($this->needUpdateDocs()) {
            return route('user.update_doc');
        }
        return '/';
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
        $members = $members->orderby('updated_at', 'desc')
            ->with('refuser')
            ->paginate(UserConstants::PP);

        return $members;
    }
}
