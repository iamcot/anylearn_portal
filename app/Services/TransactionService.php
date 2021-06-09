<?php

namespace App\Services;

use App\Constants\ConfigConstants;
use App\Constants\NotifConstants;
use App\Constants\OrderConstants;
use App\Constants\UserConstants;
use App\Constants\UserDocConstants;
use App\Models\Configuration;
use App\Models\Item;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherEvent;
use App\Models\VoucherEventLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    public function statusOperation($id, $oldStatus)
    {
        if ($oldStatus == ConfigConstants::TRANSACTION_STATUS_PENDING) {
            return '<a class="btn btn-sm btn-success" href="' . route('transaction.status.touch', ['id' => $id, 'status' => ConfigConstants::TRANSACTION_STATUS_DONE]) . '"><i class="fas fa-lock"></i> Duyệt</a>
            <a class="btn btn-sm btn-danger" href="' . route('transaction.status.touch', ['id' => $id, 'status' => ConfigConstants::TRANSACTION_STATUS_REJECT]) . '"><i class="fas fa-lock"></i> Từ chối</a>
            ';
        } else {
            return $this->statusText($oldStatus);
        }
    }

    public function approveWalletcTransaction($id)
    {
        // update transaction
        $trans = Transaction::find($id);

        $trans->update([
            'status' => ConfigConstants::TRANSACTION_STATUS_DONE,
        ]);
        // update c wallet
        User::find($trans->user_id)->update([
            'wallet_c' => DB::raw('wallet_c + ' . $trans->amount),
        ]);
        // send notif 
        $notifServ = new Notification();
        $notifServ->createNotif(NotifConstants::TRANSACTIONN_UPDATE, $trans->user_id, [
            'content' => $trans->content
        ]);
        return true;
    }

    private function statusText($status)
    {
        switch ($status) {
            case ConfigConstants::TRANSACTION_STATUS_PENDING:
                return 'ĐANG CHỜ';
            case ConfigConstants::TRANSACTION_STATUS_DONE:
                return 'ĐÃ DUYỆT';
            case ConfigConstants::TRANSACTION_STATUS_REJECT:
                return 'ĐÃ TỪ CHỐI';
            default:
                return '';
        }
    }

    public function placeOrderOneItem(Request $request, $user, $itemId, $allowNoMoney = false)
    {
        $childUser = $request->get('child', '');

        $item = Item::find($itemId);
        if (!$item) {
            return 'Trang không tồn tại';
        }
        $alreadyRegister = DB::table('order_details as od')
            ->join('orders', 'orders.id', '=', 'od.order_id')
            ->whereIn('orders.status', [OrderConstants::STATUS_DELIVERED, OrderConstants::STATUS_NEW])
            ->where('orders.user_id', ($childUser > 0 ? $childUser : $user->id))
            ->where('od.item_id', $itemId)
            ->count();
        if ($alreadyRegister > 0) {
            return 'Bạn đã đăng ký khóa học này';
        }
        $voucher = $request->get('voucher', '');

        $result = DB::transaction(function () use ($user, $item, $voucher, $childUser, $allowNoMoney) {
            $notifServ = new Notification();
            $newOrder = null;
            $status = OrderConstants::STATUS_DELIVERED;
            $amount = $item->price;
            $childUserDB = $childUser > 0 ? User::find($childUser) : null;
            if (!empty($voucher)) {
                try {
                    $voucherM = new Voucher();
                    $usedVoucher = $voucherM->useVoucherClass($user->id, $item->id, $voucher);
                    $newOrder = Order::create([
                        'user_id' => $childUser > 0 ? $childUser : $user->id,
                        'amount' => $item->price,
                        'quantity' => 1,
                        'status' => $status,
                        'payment' => UserConstants::VOUCHER,
                    ]);
                } catch (\Exception $e) {
                    DB::rollback();
                    return $e->getMessage();
                }
            } else {
                if ($user->wallet_m < $amount && !$allowNoMoney) {
                    return "Không đủ tiền";
                }
              
                $status = $user->wallet_m >= $amount ? OrderConstants::STATUS_DELIVERED : OrderConstants::STATUS_NEW;
                $transStatus = $user->wallet_m >= $amount ? ConfigConstants::TRANSACTION_STATUS_DONE : ConfigConstants::TRANSACTION_STATUS_PENDING;

                $newOrder = Order::create([
                    'user_id' => $childUser > 0 ? $childUser : $user->id,
                    'amount' => $amount,
                    'quantity' => 1,
                    'status' => $status,
                    'payment' => UserConstants::WALLET_M,
                ]);
                User::find($user->id)->update([
                    'wallet_m' => DB::raw('wallet_m - ' . $amount),
                ]);
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => ConfigConstants::TRANSACTION_ORDER,
                    'amount' => (0 - $amount),
                    'pay_method' => UserConstants::WALLET_M,
                    'pay_info' => '',
                    'content' => 'Thanh toán khóa học: ' . $item->title . ($childUserDB != null ? ' [' . $childUserDB->name . ']' : ''),
                    'status' => $transStatus,
                    'order_id' => $newOrder->id
                ]);
            }
            if ($newOrder == null) {
                return "Không tạo được đơn hàng mới.";
            }

            //save order details
            $orderDetail = OrderDetail::create([
                'order_id' => $newOrder->id,
                'user_id' => $user->id,
                'item_id' => $item->id,
                'unit_price' => $item->price,
                'paid_price' => $item->price,
                'status' => $status,
            ]);

            // voucher event
            $voucherEvent = new VoucherEventLog();
            $voucherEvent->useEvent(VoucherEvent::TYPE_CLASS, $user->id, $item->id);

            $author = User::find($item->user_id);

            //cal commission direct, update direct user wallet M + wallet C, save transaction log
            $configM = new Configuration();
            $configs = $configM->gets([
                ConfigConstants::CONFIG_BONUS_RATE, 
                ConfigConstants::CONFIG_DISCOUNT, 
                ConfigConstants::CONFIG_COMMISSION, 
                ConfigConstants::CONFIG_FRIEND_TREE, 
                ConfigConstants::CONFIG_COMMISSION_FOUNDATION
            ]);
            if ($item->companyCommission != null) {
                $overrideConfigs = json_decode($item->company_commission, true);
                foreach($overrideConfigs as $key => $value) {
                    if ($value != null) {
                        $configs[$key] = $value;
                    }
                }
            }
            $userService = new UserServices();

            if ($item->commission_rate == -1) {
                $commissionRate = 0;
            } else {
                $commissionRate = $item->commission_rate > 0 ? $item->commission_rate : $author->commission_rate;
            }

            $directCommission = $userService->calcCommission($amount, $commissionRate, $configs[ConfigConstants::CONFIG_DISCOUNT], $configs[ConfigConstants::CONFIG_BONUS_RATE]);

            User::find($user->id)->update([
                'wallet_c' => DB::raw('wallet_c + ' . $directCommission),
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'type' => ConfigConstants::TRANSACTION_COMMISSION,
                'amount' => $directCommission,
                'pay_method' => UserConstants::WALLET_C,
                'pay_info' => '',
                'content' => 'Nhận điểm từ khóa học đã mua: ' . $item->title . ($childUserDB != null ? ' [' . $childUserDB->name . ']' : ''),
                'status' => ConfigConstants::TRANSACTION_STATUS_PENDING,
                'order_id' => $orderDetail->id
            ]);
            // $notifServ->createNotif(NotifConstants::TRANS_COMMISSION_RECEIVED, $user->id, [
            //     'username' => $user->name,
            //     'amount' => number_format($directCommission, 0, ',', '.'),
            // ]);

            //pay author 
            $authorCommission = floor($amount * $commissionRate / $configs[ConfigConstants::CONFIG_BONUS_RATE]);
            // User::find($item->user_id)->update([
            //     'wallet_c' => DB::raw('wallet_c + ' . $authorCommission),
            // ]);

            Transaction::create([
                'user_id' => $author->id,
                'type' => ConfigConstants::TRANSACTION_COMMISSION,
                'amount' => $authorCommission,
                'pay_method' => UserConstants::WALLET_C,
                'pay_info' => '',
                'content' => 'Nhận điểm từ bán khóa học: ' . $item->title,
                'status' => ConfigConstants::TRANSACTION_STATUS_PENDING,
                'order_id' => $orderDetail->id, //TODO user order detail instead order id to know item
            ]);

            //save commission indirect + transaction log in PENDING status

            $indirectCommission = $userService->calcCommission($amount, $commissionRate, $configs[ConfigConstants::CONFIG_COMMISSION], $configs[ConfigConstants::CONFIG_BONUS_RATE]);

            $currentUserId = $user->user_id;
            for ($i = 1; $i < $configs[ConfigConstants::CONFIG_FRIEND_TREE]; $i++) {
                $refUser = User::find($currentUserId);
                if ($refUser) {
                    // User::find($refUser->id)->update([
                    //     'wallet_c' => DB::raw('wallet_c + ' . $indirectCommission),
                    // ]);
                    Transaction::create([
                        'user_id' => $refUser->id,
                        'type' => ConfigConstants::TRANSACTION_COMMISSION,
                        'amount' => $indirectCommission,
                        'pay_method' => UserConstants::WALLET_C,
                        'pay_info' => '',
                        'content' => 'Nhận điểm từ ' . $user->name . ' mua khóa học: ' . $item->title . ($childUserDB != null ? ' [' . $childUserDB->name . ']' : ''),
                        'status' => ConfigConstants::TRANSACTION_STATUS_PENDING,
                        'ref_user_id' => $user->id,
                        'ref_amount' => $amount,
                        'order_id' => $orderDetail->id
                    ]);
                    // $notifServ->createNotif(NotifConstants::TRANS_COMMISSION_RECEIVED, $refUser->id, [
                    //     'username' => $refUser->name,
                    //     'amount' => number_format($indirectCommission, 0, ',', '.'),
                    // ]);
                    $currentUserId = $refUser->user_id;
                } else {
                    break;
                }
            }
            //foundation 
            $foundation = 0;
            if (!$item->is_test) {
                $foundation = $userService->calcCommission($amount, $commissionRate, $configs[ConfigConstants::CONFIG_COMMISSION_FOUNDATION], 1);
                Transaction::create([
                    'user_id' => 0,
                    'type' => ConfigConstants::TRANSACTION_FOUNDATION,
                    'amount' => $foundation,
                    'pay_method' => UserConstants::WALLET_M,
                    'pay_info' => '',
                    'content' => 'Nhận quỹ từ ' . $user->name . ' mua khóa học: ' . $item->title . ($childUserDB != null ? ' [' . $childUserDB->name . ']' : ''),
                    'status' => ConfigConstants::TRANSACTION_STATUS_PENDING,
                    'ref_user_id' => $user->id,
                    'ref_amount' => $amount,
                    'order_id' => $orderDetail->id
                ]);
            }


            DB::commit();
            $notifServ->createNotif(NotifConstants::COURSE_REGISTERED, $user->id, [
                'course' => $item->title,
            ]);
            // $notifServ->createNotif(NotifConstants::TRANS_FOUNDATION, $user->id, [
            //     'amount' => number_format($foundation, 0, ',', '.'),
            //     'course' => $item->title,
            // ]);
            $notifServ->createNotif(NotifConstants::COURSE_HAS_REGISTERED, $author->id, [
                'username' => $author->name,
                'course' => $item->title,
            ]);
            return $transStatus == ConfigConstants::TRANSACTION_STATUS_DONE ? true : ConfigConstants::TRANSACTION_STATUS_PENDING;
        });
        return $result;
    }

    public function approveRegistrationAfterDeposit($userId) {
        
        $children = User::where('user_id', $userId)
        ->where('is_child', 1)
        ->get();
        $ids = [$userId];
        if (count($children) > 0) {
            foreach($children as $child) {
                $ids[] = $child->id;
            }
        }
        Log::debug("Approve register for Ids ", ["ids" => $ids]);

        $allUserNewOrders = Order::whereIn('user_id', $ids)
        ->where('status', OrderConstants::STATUS_NEW)
        ->get();
        if (count($allUserNewOrders) > 0) {
            $notifServ = new Notification();

            foreach($allUserNewOrders as $order) {
                $userDB = User::find($userId);
                Log::debug("User", ["userId" => $userDB->id, "wallet_m" => $userDB->wallet_m]);
                if ($userDB->wallet_m >= $order->amount) {
                    $userDB->update([
                        'wallet_m' => DB::raw('wallet_m - ' . $order->amount)
                    ]);
                    OrderDetail::where('order_id', $order->id)->update([
                        'status' => OrderConstants::STATUS_DELIVERED
                    ]);
                    Order::find($order->id)->update([
                        'status' => OrderConstants::STATUS_DELIVERED
                    ]);
                    Transaction::where('type', ConfigConstants::TRANSACTION_ORDER)
                    ->where('order_id', $order->id)
                    ->update([
                        'status' => ConfigConstants::TRANSACTION_STATUS_DONE,
                    ]);
                    Log::debug("Update all transaction & orders", ["orderId" => $order->id]);
                    $notifServ->createNotif(NotifConstants::COURSE_REGISTER_APPROVE, $userId, []);
                }
            }
        }
    }
}
