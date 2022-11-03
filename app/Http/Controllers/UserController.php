<?php

namespace App\Http\Controllers;

use App\Constants\ConfigConstants;
use App\Constants\NotifConstants;
use App\Constants\OrderConstants;
use App\Constants\UserConstants;
use App\Models\Configuration;
use App\Models\Contract;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Item;
use App\Models\UserDocument;
use App\Models\UserLocation;
use App\Services\FileServices;
use App\Services\SmsServices;
use App\Services\UserServices;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\I18nContent;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\VarDumper\Cloner\Data;
use Vanthao03596\HCVN\Models\District;
use Vanthao03596\HCVN\Models\Province;
use Vanthao03596\HCVN\Models\Ward;

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
        if (!$userService->haveAccess($user->role, 'user.mods')) {
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
        if (!$userService->haveAccess($user->role, 'user.members')) {
            return redirect('/')->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $userM = new User();
        if ($request->input('action') == 'clear') {
            return redirect()->route('user.members');
        }

        if ($request->input('action') == 'saleassign') {
            if ($request->hasFile('saleassign') && $request->file('saleassign')->isValid()) {
                $csvFile = $request->file('saleassign');

                $fileHandle = fopen($csvFile, 'r');
                $rows = [];
                $header = [];
                while (!feof($fileHandle)) {
                    if (empty($header)) {
                        $header = fgetcsv($fileHandle, 0, ';');
                    } else {
                        $csvRaw = fgetcsv($fileHandle, 0, ';');
                        $rowcsv = [];
                        foreach ($header as $k => $col) {
                            $rowcsv[] = isset($csvRaw[$k]) ? $csvRaw[$k] : "";
                        }
                        $rows[] = $rowcsv;
                    }
                }
                fclose($fileHandle);
                // dd($header, $rows);
                $countUpdate = 0;
                $countCreate = 0;
                foreach ($rows as $row) {
                    if (empty($row[1])) {
                        continue;
                    }
                    try {
                        $exists = User::where('phone', $row[1])->first();
                        if ($exists) {
                            if (!empty($row[2])) {
                                $data['user_id'] = $row[2];
                            } else if (!empty($row[3])) {
                                $data['sale_id'] = $row[3];
                            }
                            $countUpdate += User::where('phone', $row[1])->update($data);
                        } else {
                            // Log::debug($row);
                            User::create([
                                'name' => $row[0],
                                'phone' => $row[1],
                                'sale_id' => $row[3],
                                'is_registered' => 0,
                                'source' => isset($row[4]) ? $row[4] : '',
                                'role' => UserConstants::ROLE_MEMBER,
                                'password' => Hash::make($row[1]),
                                'status' => UserConstants::STATUS_INACTIVE,
                                'refcode' => $row[1],
                            ]);
                            $countCreate++;
                        }
                    } catch (\Exception $ex) {
                        Log::error($ex);
                    }
                }
                return redirect()->back()->with('notify', 'Cập nhật thành công ' . $countUpdate . ', Tạo mới thành công' . $countCreate . ' trên tổng số' . count($rows) . '. Chú ý nếu tạo user mới thì chỉ gán cho cột sale_id');
            }
        }

        if ($request->input('action') == 'file') {
            $members = $userM->searchMembers($request, true);
            if (!$members) {
                return redirect()->route('user.members');
            }
            $headers = [
                // "Content-Encoding" => "UTF-8",
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=anylearn_member_" . now() . ".csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            $callback = function () use ($members) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($file, array_keys($members[0]));
                foreach ($members as $row) {
                    mb_convert_encoding($row, 'UTF-16LE', 'UTF-8');
                    fputcsv($file, $row);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        } else {
            $members = $userM->searchMembers($request);
        }
        $this->data['isSale'] = false;
        if ($user->role == UserConstants::ROLE_SALE) {
            $this->data['isSale'] = true;
        }

        $this->data['members'] = $members;
        $this->data['navText'] = __('Quản lý Thành viên');
        return view('user.member_list', $this->data);
    }
    // public function WithDraw(Request $request)
    // {
    //     if ($request->input('withdraw')) {
    //         $input = $request->all();
    //         dd($input);
    //     }
    // }
    public function meEdit(Request $request)
    {
        $editUser = Auth::user();
        $userService = new UserServices();
        if ($request->input('save')) {
            $input = $request->all();
            $input['role'] = $editUser->role;
            $input['user_id'] = $editUser->user_id;
            $input['boost_score'] = $editUser->boost_score;
            $input['commission_rate'] = $editUser->commission_rate;
            $userM = new User();
            $rs = $userM->saveMember($request, $input);
            return redirect()->route('me.dashboard')->with('notify', $rs);
        }
        $friends = User::where('user_id', $editUser->id)->paginate();
        $editUser = $userService->userInfo($editUser->id);
        $this->data['friends'] = $friends;
        $this->data['user'] = $editUser;
        $this->data['type'] = 'member';
        return view(env('TEMPLATE', '') . 'me.user_edit', $this->data);
    }
    public function meHistory(Request $request)
    {
        $trans = new Transaction();
        $sum = Transaction::where('pay_method', '=', 'wallet_c')->where('status', 1)->where('user_id', auth()->user()->id)->sum('amount');
        $this->data['anyPoint'] = abs($sum);
        // $this->data['anyPoint']= $trans->pendingWalletC(auth()->user()->id);
        $this->data['WALLETM'] = $trans->history(auth()->user()->id, 'wallet_m');
        $this->data['WALLETC'] = $trans->history(auth()->user()->id, 'wallet_c');
        $this->data['navText'] = __('Giao dịch của tôi');
        return  view(env('TEMPLATE', '') . 'me.history', $this->data);
    }
    public function meChild(Request $request)
    {
        $id = Auth::user()->id;
        $childuser = User::where('user_id', $id)->where('is_child', 1)->get();
        $this->data['childuser'] = $childuser;
        $parent = Auth::user();
        if ($request->input('childedit')) {

            $input = $request->all();
            $id = $input['childid'];
            $userC = User::find($id);
            $courses = DB::table('order_details')
                ->join('items', 'order_details.item_id', '=', 'items.id')
                ->where('order_details.user_id', $id)
                ->orderBy('order_details.created_at', 'desc')->take(4)
                ->get();
            $this->data['courses'] = $courses;
            $this->data['hasBack'] = route('me.child');
            $this->data['userC'] = $userC;
            $this->data['navText'] = __('Quản lý tài khoản con');
            if ($request->input('save')) {
                $input = $request->all();
                $this->data['navText'] = __('Quản lý tài khoản con');
                $userC->name = $input['username'];
                $userC->dob = $input['dob'];
                $userC->sex = $input['sex'];
                $userC->introduce = $input['introduce'];
                $userC->save($input);
                $this->data['userC'] = $userC;
                return view(env('TEMPLATE', '') . 'me.editchild', $this->data);
            }
            if ($request->input('more')) {
                return redirect()->route('me.orders');
            }
            $this->data['navText'] = __('Quản lý tài khoản con');
            return view(env('TEMPLATE', '') . 'me.editchild', $this->data);

            // return redirect()->route('me.editchild')->with([ 'id' => $id ]);
        }

        if ($request->input('create')) {
            $input = $request->all();
            $userChild = new User();
            $userChild->createChild($parent, $input);
            return redirect()->route('me.child')->with('notify', 'Tạo tài khoản mới thành công');
        }
        $this->data['navText'] = __('Quản lý tài khoản con');
        return view(env('TEMPLATE', '') . 'me.child', $this->data);
    }
    // public function meChildEdit(Request $request)
    // {
    //     $id = session()->get('id');
    //     $userC = User::find($id);
    //     $this->data['userC'] = $userC;
    //     $this->data['navText'] = __('Quản lý tài khoản con');
    //     if($request->input('save')){
    //         $input=$request->all();
    //         $this->data['navText'] = __('Quản lý tài khoản con');
    //         $userC->name = $input['username'];
    //         $userC->dob = $input['dob'];
    //         $userC->sex = $input['sex'];
    //         $userC->introduce = $input['introduce'];
    //         $userC -> save($input);
    //         $this->data['userC'] = $userC;
    //         return redirect()->route('me.editchild')->with([ 'id' => $id ]);
    //     }
    //     return view(env('TEMPLATE', '') . 'me.editchild', $this->data);
    // }
    public function mePassword(Request $request)
    {
        $editUser = Auth::user();
        // $validator = Validator::make($request->all(), [

        // ]
        if ($request->input('save')) {
            $input = $request->all();
            $newpass = $request->input('newpassword');
            $oldpass = $request->input('password');
            $input['role'] = $editUser->role;
            $input['user_id'] = $editUser->user_id;
            $input['boost_score'] = $editUser->boost_score;
            $input['commission_rate'] = $editUser->commission_rate;
            $userM = new User();
            if (Hash::check($oldpass, $editUser->password)) {
                if ($request->input('newpassword') != $request->input('repassword')) {
                    return redirect()->back()->with('errormk', 'Mật khẩu không trùng khớp');
                } else {
                    $userM = new User();
                    $rs = $userM->changePassword($request, $input);
                    return redirect()->route('me.dashboard')->with('notify', $rs);
                }
            } else {
                //return redirect()->route('me.resetpassword')->with('notify', 'error');
                return redirect()->back()->with('error', 'Mật Khẩu không chính xác');
            }
        }
        $this->data['user'] = $editUser;
        $this->data['navText'] = __('Đổi Mật Khẩu');
        return view(env('TEMPLATE', '') . 'me.resetpassword', $this->data);
    }

    public function memberEdit(Request $request, $userId)
    {
        $userService = new UserServices();
        $user = Auth::user();

        if ($userId == 1 || !$userService->haveAccess($user->role, 'user.members')) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }

        if ($request->input('moneyFix')) {
            $editUser = User::find($userId);

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
        $userI18n = $userService->userInfo($userId);
        $this->data['user'] = $userI18n;
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
        if ($userId == 1 || !$userService->haveAccess($user->role, 'user.mods')) {
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
        if ($userId == 1 || !$userService->haveAccess($user->role, 'admin')) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $rs = User::find($userId)->update(['status' => DB::raw('1 - status')]);
        return redirect()->back()->with('notify', $rs);
    }

    public function contractList(Request $request)
    {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->haveAccess($user->role, 'user.contract')) {
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
        if ($request->input('ic') == 'on') {
            $list = $list->where('contracts.status', '>=', 0);
        } else {
            $list = $list->where('contracts.status', '>', 0);
        }
        $list = $list->orderBy('contracts.id', 'desc')
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
        if (!$userService->haveAccess($user->role, 'admin' || $user->role, 'fin')) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $contract = DB::table('contracts')
            ->join('users', 'users.id', '=', 'contracts.user_id')
            ->where('contracts.id', $id)
            ->select('users.name', 'users.phone', 'contracts.*')
            ->first();
        if ($request->get('action') != null) {
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
        $this->data['files'] = UserDocument::where('user_id', $contract->user_id)->get();
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

    public function locationList(Request $request)
    {
        $user = Auth::user();
        $this->data['locations'] = UserLocation::where('user_id', $user->id)->paginate();
        $this->data['navText'] = __('Quản lý địa điểm/chi nhánh');
        return view(env('TEMPLATE', '') . 'me.user_location_list', $this->data);
    }

    public function locationEdit(Request $request, $id)
    {
        $location = UserLocation::find($id);
        if ($request->get('save') == "save") {
            $input = $request->input();
            $input['user_id'] = Auth::user()->id;
            $userService = new UserServices();
            $geoCode = $userService->getUserLocationGeo($input['address'] . " " . $input['ward_path']);
            if ($geoCode !== false) {
                $input['longitude'] = $geoCode['longitude'];
                $input['latitude'] = $geoCode['latitude'];
            }
            // $input['user_id'] = Auth::user()->id;
            if (isset($input['is_head'])) {
                UserLocation::where('user_id', $input['user_id'])->update(['is_head' => 0]);
                $input['is_head'] =  1;
            } else {
                $input['is_head'] =  0;
            }

            try {
                $new = UserLocation::find($id)->update($input);
            } catch (Exception $e) {
                Log::error($e);
                return redirect()->back()->with('notify',  "Cập nhật chỉ thất bại");
            }

            return redirect()->route('location')->with('notify', "Cập nhật địa chỉ thành công");
        }
        $this->data['location'] = $location;
        $this->data['provinces'] = Province::orderby('name')->get();
        $this->data['districts'] = District::where('parent_code', $location->province_code)->orderBy('name')->get();
        $this->data['wards'] = Ward::where('parent_code', $location->district_code)->orderBy('name')->get();
        $this->data['navText'] = __('Chỉnh sửa địa điểm/chi nhánh');
        $this->data['hasBack'] = true;
        return view(env('TEMPLATE', '') . 'me.user_location_form', $this->data);
    }

    public function locationCreate(Request $request)
    {
        if ($request->get('save') == "save") {
            $input = $request->input();
            $input['user_id'] = Auth::user()->id;

            $userService = new UserServices();
            $geoCode = $userService->getUserLocationGeo($input['address'] . " " . $input['ward_path']);
            if ($geoCode !== false) {
                $input['longitude'] = $geoCode['longitude'];
                $input['latitude'] = $geoCode['latitude'];
            }

            if (isset($input['is_head'])) {
                UserLocation::where('user_id', $input['user_id'])->update(['is_head' => 0]);
                $input['is_head'] =  1;
            } else {
                $input['is_head'] =  0;
            }

            try {
                $new = UserLocation::create($input);
            } catch (Exception $e) {
                Log::error($e);
                return redirect()->back()->with('notify',  "Tạo địa chỉ thất bại");
            }

            return redirect()->route('location')->with('notify', "Tạo địa chỉ thành công");
        }
        $this->data['provinces'] = Province::orderby('name')->get();
        $this->data['navText'] = __('Thêm mới địa điểm/chi nhánh');

        $this->data['hasBack'] = true;
        return view(env('TEMPLATE', '') . 'me.user_location_form', $this->data);
    }

    public function notification(Request $request)
    {
        $user = Auth::user();
        $this->data['navText'] = __('Thông báo');
        $this->data['notifications'] = Notification::where('user_id', $user->id)->where('type', '!=', SmsServices::SMS)->orderby('id', 'desc')->paginate();
        return view(env('TEMPLATE', '') . 'me.notification', $this->data);
    }

    public function pendingOrders(Request $request)
    {
        $user = Auth::user();
        $data = DB::table('orders')
            ->where('orders.status', OrderConstants::STATUS_PAY_PENDING)
            ->where('orders.user_id', $user->id)
            ->select(
                'orders.*',
                DB::raw("(SELECT GROUP_CONCAT(items.title SEPARATOR ',' ) as classes FROM order_details AS os JOIN items ON items.id = os.item_id WHERE os.order_id = orders.id) as classes")
            )
            ->paginate();

        $this->data['orders'] = $data;
        $this->data['navText'] = __('Khoá học đang chờ bạn thanh toán');
        return view(env('TEMPLATE', '') . 'me.pending_orders', $this->data);
    }

    public function orders(Request $request)
    {
        $user = Auth::user();
        $this->data['navText'] = __('Khoá học của tôi');

        $input = $request->input('search');
        $item = Item::all()->where('title', 'LIKE', '%' . $input . '%');
        $inputselect = $request->input('myselect');
        $orderDetailM = new OrderDetail();
        $id = auth()->user()->id;
        $childuser = DB::table('users')->where('is_child', $id)->orWhere('id', $id)->get();
        $this->data['childuser'] = $childuser;
        $this->data['inputselect'] = $inputselect;
        $this->data['input'] = $request->input('search');

        if ($inputselect == 'all' || $inputselect == null) {
            $this->data['orders'] = $orderDetailM->searchall($user->id, $input);
        } elseif ($inputselect == $user->id) {
            $this->data['orders'] = $orderDetailM->searchparents($user->id, $input);
        } else {
            $this->data['orders'] = $orderDetailM->searchall($inputselect, $input);
        }
        if ($request->input('reset')) {
            return view(env('TEMPLATE', '') . 'me.user_orders', $this->data);
        }
        return view(env('TEMPLATE', '') . 'me.user_orders', $this->data);
    }

    public function contractSign($id)
    {
        $userServ = new UserServices();
        $user = Auth::user();
        $result = $userServ->signContract($user, $id);

        if ($result === true) {
            return redirect()->back()->with('notify', 'Đã ký hợp đồng, vui lòng chờ anyLEARN tiếp nhận và xử lí nhé.');
        } else {
            return redirect()->back()->withInput()->with('notify', $result);
        }
    }

    public function contract(Request $request)
    {
        $user = Auth::user();
        if ($request->get('save') == 'contract') {
            $inputs = $request->input();
            $userServ = new UserServices();
            $result = $userServ->saveContract($user, $inputs);

            if ($result === true) {
                return redirect()->back()->with('notify', 'Cập nhật thông tin doanh nghiêp thành công, vui lòng đọc lại hợp đồng và xác nhận KÝ.');
            } else {
                return redirect()->back()->withInput()->with('notify', $result);
            }
        }
        $contract = Contract::where('user_id', $user->id)
            ->orderby('id', 'desc')->first();
        if (!empty($contract)) {
            $configM = new Configuration();
            if ($contract->type == UserConstants::ROLE_TEACHER) {
                $key = ConfigConstants::CONTRACT_TEACHER;
                $template = $configM->get($key);
                $contract->template = Contract::makeContent($template, $user, $contract);
            } else {
                $key = ConfigConstants::CONTRACT_SCHOOL;
                $template = $configM->get($key);
                $contract->template = Contract::makeContent($template, $user, $contract);
            }
        }

        // dd($contract);
        $this->data['contract'] = $contract;
        $this->data['navText'] = __('Quản lý Hợp đồng');
        return view(env('TEMPLATE', '') . 'me.contract', $this->data);
    }

    public function certificate(Request $request)
    {
        $user = Auth::user();
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $fileService = new FileServices();
            $fileuploaded = $fileService->doUploadImage($request, 'file');
            if ($fileuploaded === false) {
                return redirect()->back()->with('notify', 'Tải lên chứng chỉ không thành công.');
            }
            $userDocM = new UserDocument();
            $userDocM->addDocWeb($fileuploaded, $user);
            return redirect()->back()->with('notify', 'Tải lên chứng chỉ thành công.');
        }

        $this->data['files'] = UserDocument::where('user_id', $user->id)->get();
        $this->data['navText'] = __('Quản lý Chứng chỉ');
        return view(env('TEMPLATE', '') . 'me.certificate', $this->data);
    }
    public function finance(Request $request)
    {
        if($request->input('withdraw')){
            $user = User::find(auth()->user()->id);
            $input = $request->all();
            $transv = new TransactionService();
            $anypoint = $input['anypoint'];
            $created = $transv->withdraw($anypoint);
            $trans = Transaction::find($created);
            $user->wallet_c -= $anypoint;
            $user->update();
        return Redirect::back()->with('bignotify', 'withdraw');
        }
        return view(env('TEMPLATE', '') . 'me.finance', $this->data);
    }
    public function removeCert(Request $request, $fileId)
    {
        $user = Auth::user();
        $file = UserDocument::find($fileId);
        if (!$file) {
            return response("Tài liệu không có", 404);
        }
        if ($file->user_id != $user->id) {
            return response("Bạn không có quyền", 400);
        }
        $fileService = new FileServices();
        $oldImageUrl = $file->data;
        $fileService->deleteUserOldImageOnS3($oldImageUrl);

        $rs = UserDocument::find($fileId)->delete();
        $docs = UserDocument::where('user_id', $user->id)->get();
        if (count($docs) == 0) {
            User::find($user->id)->update(['update_doc' => 0]);
        }
        return redirect()->back()->with('notify', 'Xoá chứng chỉ thành công.');
    }
}
