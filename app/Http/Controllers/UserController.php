<?php

namespace App\Http\Controllers;

use App\Constants\ConfigConstants;
use App\Constants\NotifConstants;
use App\Constants\UserConstants;
use App\Models\Configuration;
use App\Models\Contract;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserDocument;
use App\Services\FileServices;
use App\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function updateDoc(Request $request)
    {
        if ($request->hasFile('file')) {
            $fileService = new FileServices();
            $rs = $fileService->doUploadFile($request);
            if ($rs !== false) {
                $userDocM = new UserDocument();
                $rs = $userDocM->addDocLocal($rs);
                if ($rs) {
                    return redirect('/')->with('notify', __('Cập nhật giấy tờ thành công.'));
                }
            }
            return redirect()->back()->with('notify', __('Cập nhật giấy tờ không thành công, vui lòng kiểm tra lại'));
        }
        return view('user.update_doc');
    }

    public function inactivePage()
    {
        return view('user.inactive');
    }

    public function mods(Request $request)
    {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->haveAccess($user->role, 'root')) {
            return redirect('/')->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $this->data['mods'] = User::whereIn('role', UserConstants::$modRoles)
            ->orderby('role')
            ->paginate(UserConstants::PP);
        $this->data['navText'] = __('Quản lý Quản trị viên');
        return view('user.mods', $this->data);
    }

    public function members(Request $request)
    {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->haveAccess($user->role, 'admin')) {
            return redirect('/')->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $userM = new User();
        $this->data['members'] = $userM->searchMembers($request);
        $this->data['navText'] = __('Quản lý Thành viên');
        return view('user.member_list', $this->data);
    }

    public function meEdit(Request $request) {
        $editUser = Auth::user();
        if ($request->input('save')) {
            $input = $request->all();
            $input['role'] = $editUser->role;
            $input['user_id'] = $editUser->user_id;
            $input['boost_score'] = $editUser->boost_score;
            $input['commission_rate'] = $editUser->commission_rate;
            $userM = new User();
            $rs = $userM->saveMember($request, $input);
            return redirect()->route('me.edit')->with('notify', $rs);
        }
        $this->data['user'] = $editUser;
        $this->data['navText'] = __('Chỉnh sửa Thông tin');
        $this->data['type'] = 'member';
        return view('user.me_edit', $this->data);
    }

    public function memberEdit(Request $request, $userId)
    {
        $userService = new UserServices();
        $user = Auth::user();

        if ($userId == 1 || !$userService->haveAccess($user->role, 'admin')) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }

        $editUser = User::find($userId);

        if ($request->input('moneyFix')) {
            $result = $userService->createMoneyFix($editUser, $request->all());
            if ($result === true) {
                return redirect()->back()->with('notify', 'Giao dịch mới đã được cập nhật.');
            } else {
                return redirect()->back()->with('notify', $result);
            }
        }
        if ($request->input('save')) {
            $input = $request->all();
            $userM = new User();
            $rs = $userM->saveMember($request, $input);
            return redirect()->route('user.members')->with('notify', $rs);
        }
        $configM = new Configuration();
        $this->data['configs'] = $configM->gets([ConfigConstants::CONFIG_BONUS_RATE]);

        $this->data['user'] = $editUser;
        $this->data['navText'] = __('Chỉnh sửa Thành viên');
        $this->data['hasBack'] = route('user.members');
        $this->data['type'] = 'member';
        return view('user.member_edit', $this->data);
    }

    public function modCreate(Request $request)
    {
        if ($request->input('save')) {
            $input = $request->all();
            $userM = new User();
            $rs = $userM->createNewMod($input);
            return redirect()->route('user.mods')->with('notify', $rs);
        }
        $this->data['navText'] = __('Thêm mới quản trị viên');
        $this->data['hasBack'] = true;
        $this->data['type'] = 'mod';
        return view('user.mod_edit', $this->data);
    }

    public function modEdit(Request $request, $userId)
    {
        if ($request->input('save')) {
            $input = $request->all();
            $userM = new User();
            $rs = $userM->saveMod($input);
            return redirect()->route('user.mods')->with('notify', $rs);
        }
        $userService = new UserServices();
        $user = Auth::user();
        if ($userId == 1 || !$userService->haveAccess($user->role, 'root')) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        } else {
            $this->data['user'] = User::find($userId);
        }
        $this->data['navText'] = __('Chỉnh sửa quản trị viên');
        $this->data['hasBack'] = true;
        $this->data['type'] = 'mod';
        return view('user.mod_edit', $this->data);
    }

    public function statusTouch($userId)
    {
        $userService = new UserServices();
        $user = Auth::user();
        if ($userId == 1 || !$userService->haveAccess($user->role, 'root')) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $rs = User::find($userId)->update(['status' => DB::raw('1 - status')]);
        return redirect()->back()->with('notify', $rs);
    }

    public function contractList(Request $request)
    {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->haveAccess($user->role, 'admin')) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $list = DB::table('contracts')
            ->join('users', 'users.id', '=', 'contracts.user_id');
        if (!empty($request->input('s'))) {
            switch ($request->input('t')) {
                case "phone":
                    $list = $list->where('users.phone', $request->input('s'));
                    break;
                default:
                    $list = $list->where('users.name', 'like', '%' . $request->input('s') . '%');
                    break;
            }
        }
        $list = $list->where('contracts.status', '>', 0)
            ->orderBy('contracts.id', 'desc')
            ->select('users.name', 'users.phone', 'contracts.*')
            ->paginate(20);
        $this->data['list'] = $list;
        $this->data['navText'] = __('Các hợp đồng với thành viên');
        return view('user.contract_list', $this->data);
    }

    public function contractInfo(Request $request, $id)
    {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->haveAccess($user->role, 'admin')) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $contract = DB::table('contracts')
            ->join('users', 'users.id', '=', 'contracts.user_id')
            ->where('contracts.id', $id)
            ->select('users.name', 'users.phone', 'contracts.*')
            ->first();
        if ($request->get('action')) {
            $notifM = new Notification();
            if ($request->get('action') == UserConstants::CONTRACT_APPROVED) {
                Contract::find($id)->update(['status' => UserConstants::CONTRACT_APPROVED]);
                User::find($contract->user_id)->update([
                    'is_signed' => UserConstants::CONTRACT_APPROVED,
                    'commission_rate' => $contract->commission,
                ]);
                $notifM->createNotif(NotifConstants::CONTRACT_APPROVED, $contract->user_id, [
                    'username' => $contract->name,
                ]);
                return redirect()->back()->with('notify', 'Đã duyệt hợp đồng và cập nhật hoa hồng mới');
            } elseif ($request->get('action') == UserConstants::CONTRACT_DELETED) {
                Contract::find($id)->update(['status' => UserConstants::CONTRACT_DELETED]);
                User::find($contract->user_id)->update(['is_signed' => UserConstants::CONTRACT_DELETED]);
                $notifM->createNotif(NotifConstants::CONTRACT_DELETED, $contract->user_id, [
                    'username' => $contract->name,
                ]);
                return redirect()->route('user.contract')->with('notify', 'Đã từ chối hợp đồng với ' . $contract->name);
            }
        }

        $this->data['contract']  = $contract;
        $this->data['hasBack'] = route('user.contract');
        $this->data['navText'] = __('Hợp đồng với ' . $contract->name);
        return view('user.contract_info', $this->data);
    }

    public function userNoProfile(Request $request)
    {
        $users = DB::table('users')->where('status', 1)
            ->whereNotIn('role', ['admin', 'mod']);
        if (!empty($request->input('s'))) {
            switch ($request->input('t')) {
                case "phone":
                    $users = $users->where('users.phone', $request->input('s'));
                    break;
                default:
                    $users = $users->where('users.name', 'like', '%' . $request->input('s') . '%');
                    break;
            }
        }
        $users = $users->where(function ($q) {
            $q->whereNull('image')
                ->orWhereNull('introduce')
                ->orWhereNull('full_content');
        })
            ->select(DB::raw("users.*, (SELECT notifications.created_at 
        FROM notifications WHERE notifications.user_id = users.id AND type = '" . NotifConstants::UPDATE_INFO_REMIND . "'  order by notifications.id desc limit 1) AS last_notif"))
            ->orderBy('users.id', 'desc')
            ->paginate(20);
        $this->data['list'] = $users;
        $this->data['navText'] = __('Nhắc nhở các thành viên chưa cập nhật thông tin');
        return view('user.no_profile', $this->data);
    }

    public function remindProfile($userId)
    {
        $notifServ = new Notification();
        $notifServ->createNotif(NotifConstants::UPDATE_INFO_REMIND, $userId, []);
        return redirect()->back()->with('notify', 'Đã gửi thông báo');
    }
}
