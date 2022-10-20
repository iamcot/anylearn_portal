<?php

namespace App\Services;

use App\Constants\ConfigConstants;
use App\Constants\ItemConstants;
use App\Constants\NotifConstants;
use App\Constants\OrderConstants;
use App\Constants\UserConstants;
use App\Constants\UserDocConstants;
use App\Models\Ask;
use App\Models\Configuration;
use App\Models\Contract;
use App\Models\Item;
use App\Models\ItemUserAction;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Participation;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Geocoder\Laravel\Facades\Geocoder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserServices
{
    private $roles = [
        UserConstants::ROLE_ADMIN => [],
        UserConstants::ROLE_MOD => [],
        UserConstants::ROLE_SALE => [
            'class',
            'user.members',
            // 'order.all'
        ],
        UserConstants::ROLE_CONTENT => [
            'class',
            'article',
            'config.guide',
            'helpcenter'
        ],
        UserConstants::ROLE_SALE_CONTENT => [
            'class',
            'user.members',
            // 'order.all',
            'class',
            'article',
            'config.guide',
            'helpcenter',
            'user.contract',
            'user.contract.info',
        ],
        UserConstants::ROLE_FIN => [
            'fin.expenditures',
            'transaction',
            'voucher',
            'order.open',
            'order.all',
            'user.contract',
            'user.contract.info',
            'user.members',
            'transaction.commission',
            'useractions',
            'user.mods'
        ],
    ];

    private $blocked = [
        UserConstants::ROLE_MOD => [
            'admin',
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
            return '<a class="btn btn-sm btn-success" href="' . route('user.status.touch', ['userId' => $userId]) . '"><i class="fas fa-unlock"></i> Mở</a>';
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

    public function calcCommission($price, $bonusSeller, $bonusRate, $exchangeRate)
    {
        $netValue = $price * (1 - $bonusSeller);
        return  round($netValue * $bonusRate / $exchangeRate);
    }

    public function createMoneyFix($user, $input)
    {
        try {
            $notifServ = new Notification();
            $configM = new Configuration();
            $configs = $configM->gets([ConfigConstants::CONFIG_BONUS_RATE]);

            DB::beginTransaction();
            $obj = [
                'type' => $input['type'],
                'amount' => $input['amount'],
                'user_id' => $user->id,
                'content' => $input['content'],
                'status' => 1,
            ];
            if ($input['type'] == ConfigConstants::TRANSACTION_DEPOSIT_REFUND) {
                if ($user->wallet_m < $input['amount']) {
                    return 'Tiền không đủ';
                }
                User::find($user->id)->update([
                    'wallet_m' => ($user->wallet_m - $input['amount'])
                ]);
                $notifServ->createNotif(NotifConstants::TRANS_DEPOSIT_REFUND, $user->id, [
                    'username' => $user->name,
                    'amount' => number_format($input['amount'], 0, ',', '.'),
                ]);
                $obj['amount'] = $obj['amount'] > 0 ? $obj['amount'] * -1 : $obj['amount'];
            } elseif ($input['type'] == ConfigConstants::TRANSACTION_COMMISSION_ADD) {
                User::find($user->id)->update([
                    'wallet_c' => ($user->wallet_c + $input['amount'])
                ]);
                $notifServ->createNotif(NotifConstants::TRANS_COMMISSION_RECEIVED, $user->id, [
                    'username' => $user->name,
                    'amount' => number_format($input['amount'], 0, ',', '.'),
                ]);
            } elseif ($input['type'] == ConfigConstants::TRANSACTION_WITHDRAW) {
                if ($user->wallet_c < $input['amount']) {
                    return 'Điểm không đủ';
                }
                User::find($user->id)->update([
                    'wallet_c' => ($user->wallet_c - $input['amount'])
                ]);
                $obj['amount'] = $obj['amount'] > 0 ? $obj['amount'] * -1 : $obj['amount'];
                $notifServ->createNotif(NotifConstants::TRANS_WITHRAW_APPROVED, $user->id, [
                    'amount' => number_format($input['amount'] * $configs[ConfigConstants::CONFIG_BONUS_RATE], 0, ',', '.'),
                ]);
            }
            $trans = Transaction::create($obj);
            DB::commit();
            return true;
        } catch (\Exception $ex) {
            DB::rollback();
        }
        return 'Giao dịch thất bại';
    }

    public function allFriends($userId)
    {
        $configM = new Configuration();
        $maxLevel = $configM->get(ConfigConstants::CONFIG_FRIEND_TREE);
        $data = [];
        $userIds = [$userId];
        for ($i = 1; $i <= $maxLevel; $i++) {
            $db = DB::table('users')
                ->whereIn('user_id', $userIds)
                ->get();
            $userIds = [];
            if (count($db) > 0) {
                foreach ($db as $friend) {
                    $data[] = [
                        'id' => $friend->id,
                        'name' => $friend->name,
                        'image' => $friend->image,
                    ];
                    $userIds[] = $friend->id;
                }
            }
            if (empty($userIds)) {
                break;
            }
        }
        return $data;
    }

    public function contractStatus($status)
    {
        switch ($status) {
            case UserConstants::CONTRACT_NEW:
                return "Mới tạo";
            case UserConstants::CONTRACT_SIGNED:
                return "Thành viên ký";
            case UserConstants::CONTRACT_APPROVED:
                return "Công ty xác nhận";
            case UserConstants::CONTRACT_DELETED:
                return "Đã Hủy";
            default:
                return "N/A";
        }
    }

    public function getUserLocationGeo($address)
    {
        try {
            $code = Geocoder::geocode($address)->get();
            if (empty($code[0])) {
                return false;
            }

            $rs = $code[0];
            // dd($rs);
            $data = [
                'longitude' => $rs->getCoordinates()->getLongitude(),
                'latitude' => $rs->getCoordinates()->getLatitude()
            ];
            return $data;
        } catch (Exception $e) {
            Log::error($e);
        }
        return false;
    }

    public function countItemInCart($userId)
    {
        $openOrder = Order::where('user_id', $userId)
            ->where('status', OrderConstants::STATUS_NEW)
            ->orderby('id', 'desc')
            ->first();
        if (!$openOrder) {
            return 0;
        }
        return OrderDetail::where('order_id', $openOrder->id)->count();
    }

    public function timeAgo($datetime, $full = false)
    {
        $now = new \DateTime;
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'năm',
            'm' => 'tháng',
            'w' => 'tuần',
            'd' => 'ngày',
            'h' => 'giờ',
            'i' => 'phút',
            's' => 'giây',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v;
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' trước' : 'vừa mới';
    }

    public function saveContract($user, $contract)
    {
        $contract['user_id'] = $user->id;
        $contract['type'] = $user->role;
        $contract['status'] = UserConstants::CONTRACT_NEW;


        $result = DB::transaction(function () use ($user, $contract) {
            try {
                Contract::where('user_id', $user->id)
                    ->where('status', '!=', UserConstants::CONTRACT_APPROVED)
                    ->update([
                        'status' => UserConstants::CONTRACT_DELETED,
                    ]);
                $newContract = Contract::create($contract);
                if ($newContract) {
                    $dataUpdate = [
                        "email" => $contract['email'],
                        "address" => $contract['address'],
                    ];
                    if ($user->role == UserConstants::ROLE_TEACHER) {
                        $dataUpdate['dob'] = $contract['dob'];
                    } else {
                        $dataUpdate['title'] = $contract['ref'];
                    }
                    User::find($user->id)->update($dataUpdate);
                }
                return true;
            } catch (Exception $e) {
                DB::rollback();
                Log::error($e);
                return "Có lỗi xảy ra khi tạo hợp đồng mới, vui lòng thử lại.";
            }
        });
        return $result;
    }

    public function signContract($user, $contractId)
    {
        $result = DB::transaction(function () use ($user, $contractId) {
            try {
                Contract::find($contractId)->update([
                    'status' => UserConstants::CONTRACT_SIGNED,
                ]);
                User::find($user->id)->update([
                    "is_signed" => 0,
                ]);
                return true;
            } catch (Exception $e) {
                DB::rollback();
                Log::error($e);
                return "Có lỗi xảy ra khi ký hợp đồng, vui lòng thử lại.";
            }
        });
        return $result;
    }

    public function contractStatusText($status)
    {
        switch ($status) {
            case UserConstants::CONTRACT_NEW:
                return 'Mới tạo';
            case UserConstants::CONTRACT_SIGNED:
                return 'Bạn đã ký';
            case UserConstants::CONTRACT_APPROVED:
                return 'Công ty đã duyệt';
            case UserConstants::CONTRACT_DELETED:
                return 'Bị từ chối';
            default:
                return '';
        }
    }

    public function contractColor($status)
    {
        switch ($status) {
            case UserConstants::CONTRACT_NEW:
                return 'warning';
            case UserConstants::CONTRACT_SIGNED:
                return 'warning';
            case UserConstants::CONTRACT_APPROVED:
                return 'success';
            case UserConstants::CONTRACT_DELETED:
                return 'danger';
            default:
                return '';
        }
    }

    public function deleteAccount($phone)
    {
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            throw new Exception("User không đúng");
        }
        //@TODO correct order detail of children
        OrderDetail::where('user_id', $user->id)->where('status', OrderConstants::STATUS_NEW)->delete();
        Order::where('user_id', $user->id)->where('status', OrderConstants::STATUS_NEW)->delete();
        Transaction::where('user_id', $user->id)->where('status', ConfigConstants::TRANSACTION_STATUS_PENDING)->delete();
        Item::where('user_id', $user->id)->update([
            'status' => ItemConstants::STATUS_INACTIVE,
            'user_status' => ItemConstants::STATUS_INACTIVE
        ]);
        ItemUserAction::where('user_id', $user->id)->delete();
        Ask::where('user_id', $user->id)->update(['status' => 0]);
        User::find($user->id)->update([
            'phone' => 'DEL-' . $user->phone . '-' . now(),
            'name' => 'DEL-' . $user->name,
            'status' => UserConstants::STATUS_INACTIVE,
            'email' => null,
            'api_token' => null,
            'notif_token' => null,
            'refcode' => now(),
            '3rd_token' => null,
        ]);
        return true;
    }

    public function orderStats($userId)
    {
        $data['gmv'] = DB::table('orders')
            ->where('orders.user_id', $userId)
            ->where('status', OrderConstants::STATUS_DELIVERED)
            ->sum('amount');

        $data['registered'] = DB::table('orders')
            ->join('order_details AS od', 'od.order_id', '=', 'orders.id')
            ->where('orders.user_id', $userId)
            ->count('od.id');

        $data['complete'] = Participation::where('participant_user_id', $userId)
            ->groupby('item_id')
            ->count();

        $data['pending'] = DB::table('orders')
            ->where('orders.user_id', $userId)
            ->whereIn('status', [OrderConstants::STATUS_PAY_PENDING, OrderConstants::STATUS_NEW])
            ->sum('amount');

        $data['anyPoint'] = Transaction::where('user_id', $userId)
            ->where('type', ConfigConstants::TRANSACTION_EXCHANGE)
            ->where('status', ConfigConstants::TRANSACTION_STATUS_DONE)
            ->sum('amount');

        $data['voucher'] = DB::table('vouchers_used')
            ->join('vouchers', 'vouchers.id', '=', 'vouchers_used.voucher_id')
            ->where('vouchers_used.user_id', $userId)
            ->sum('vouchers.value');
        return $data;
    }
}
