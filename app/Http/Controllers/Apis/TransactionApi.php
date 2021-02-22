<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ConfigConstants;
use App\Constants\ItemConstants;
use App\Constants\NotifConstants;
use App\Constants\OrderConstants;
use App\Constants\UserConstants;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Item;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Voucher;
use App\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionApi extends Controller
{
    public function saveDeposit(Request $request)
    {
        $user = $request->get('_user');

        $paymentInput = $request->get('pay_method');
        $amountInput = $request->get('amount');
        $refAmount = 0;
        $status = 0;

        $usedVoucher = '';
        if ($paymentInput == 'voucher') {
            try {
                $voucherM = new Voucher();
                $amount = $voucherM->useVoucher($user->id, $amountInput);
                $usedVoucher = " bằng voucher " . $amountInput;
                $status = 1;
            } catch (\Exception $e) {
                return response($e->getMessage(), 400);
            }
        } else {
            $amount = $amountInput;
        }

        $rs = Transaction::create([
            'user_id' => $user->id,
            'type' => ConfigConstants::TRANSACTION_DEPOSIT,
            'amount' => $amount,
            'pay_method' => $paymentInput,
            'ref_amount' => $refAmount,
            'content' => 'Nạp tiền vào tài khoản' . $usedVoucher,
            'status' => $status,
        ]);
        $notifServ = new Notification();
        $notifServ->createNotif(NotifConstants::TRANS_DEPOSIT_SENT, $user->id, [
            'amount' => number_format($rs->amount, 0, ',', '.'),
        ]);

        return response()->json(['result' => $rs != null ? true : false]);
    }

    public function saveExchange(Request $request)
    {
        $user = $request->get('_user');

        $point = $request->get('amount');
        if ($point > $user->wallet_c) {
            return response('Không đủ điểm', 400);
        }
        $configM = new Configuration();
        $rate = $configM->get(ConfigConstants::CONFIG_BONUS_RATE);
        $walletM = $point * $rate;
        $rs = User::find($user->id)->update([
            'wallet_c' => $user->wallet_c - $point,
            'wallet_m' => $user->wallet_m + $walletM,
        ]);
        $rs = Transaction::create([
            'user_id' => $user->id,
            'type' => ConfigConstants::TRANSACTION_EXCHANGE,
            'amount' => $walletM,
            'pay_method' => 'wallet_c',
            'ref_amount' => -$point,
            'content' => "Đổi $point điểm sang tài khoản.",
            'status' => 1,
        ]);
        return response()->json(['result' => $rs ? true : false]);
    }

    public function saveWithdraw(Request $request)
    {
        $user = $request->get('_user');

        $point = $request->get('amount');
        if ($point > $user->wallet_c) {
            return response('Không đủ điểm', 400);
        }
        $bankInfoStr = $request->get('pay_info');
        $bankInfo = json_decode($bankInfoStr, true);
        if (empty($bankInfo)) {
            return response('Không có thông tin ngân hàng', 400);
        }

        $configM = new Configuration();
        $rate = $configM->get(ConfigConstants::CONFIG_BONUS_RATE);
        $walletM = $point * $rate;
        $rs = User::find($user->id)->update([
            'wallet_c' => $user->wallet_c - $point,
        ]);
        $rs = Transaction::create([
            'user_id' => $user->id,
            'type' => ConfigConstants::TRANSACTION_WITHDRAW,
            'amount' => -$point,
            'pay_method' => 'wallet_c',
            'pay_info' => $bankInfoStr,
            'ref_amount' => $walletM,
            'content' => "Rút $point điểm về ngân hàng.",
            'status' => 0,
        ]);
        $notifServ = new Notification();
        $notifServ->createNotif(NotifConstants::TRANS_WITHRAW_APPROVED, $user->id, [
            'amount' => number_format($walletM, 0, ',', '.'),
            'point' => number_format($point, 0, ',', '.'),
        ]);
        return response()->json(['result' => $rs ? true : false]);
    }

    public function history(Request $request)
    {
        $user = $request->get('_user');

        $transM =  new Transaction();
        $walletM = $transM->history($user->id, UserConstants::WALLET_M);
        $walletC = $transM->history($user->id, UserConstants::WALLET_C);
        return response()->json([
            UserConstants::WALLET_M => $walletM,
            UserConstants::WALLET_C => $walletC,
        ]);
    }

    public function placeOrderOneItem(Request $request, $itemId)
    {
        $user = $request->get('_user');

        $item = Item::find($itemId);
        if (!$item) {
            return response('Trang không tồn tại', 404);
        }
        $alreadyRegister = DB::table('order_details as od')
            ->join('orders', 'orders.id', '=', 'od.order_id')
            ->where('orders.status', OrderConstants::STATUS_DELIVERED)
            ->where('od.user_id', $user->id)
            ->where('od.item_id', $itemId)
            ->count();
        if ($alreadyRegister > 0) {
            return response('Bạn đã đăng ký khóa học này', 400);
        }
        $voucher = $request->get('voucher', '');

        $result = DB::transaction(function () use ($user, $item, $voucher) {
            $notifServ = new Notification();
            $newOrder = null;
            $status = OrderConstants::STATUS_DELIVERED;
            $amount = $item->price;
            if (!empty($voucher)) {
                try {
                    $voucherM = new Voucher();
                    $usedVoucher = $voucherM->useVoucherClass($user->id, $item->id, $voucher);
                    //save order
                    $newOrder = Order::create([
                        'user_id' => $user->id,
                        'amount' => $item->price,
                        'quantity' => 1,
                        'status' => $status,
                        'payment' => UserConstants::VOUCHER,
                    ]);
                } catch (\Exception $e) {
                    // dd($e);
                    DB::rollback();
                    return $e->getMessage();
                }
            } else {
                if ($user->wallet_m < $amount) {
                    return "Không đủ tiền";
                }
                $status = $user->wallet_m >= $item->price ? OrderConstants::STATUS_DELIVERED : OrderConstants::STATUS_NEW;

                //save order
                $newOrder = Order::create([
                    'user_id' => $user->id,
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
                    'content' => 'Thanh toán khóa học: ' . $item->title,
                    'status' => ConfigConstants::TRANSACTION_STATUS_DONE,
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

            $author = User::find($item->user_id);

            //cal commission direct, update direct user wallet M + wallet C, save transaction log
            $configM = new Configuration();
            $configs = $configM->gets([ConfigConstants::CONFIG_BONUS_RATE, ConfigConstants::CONFIG_DISCOUNT, ConfigConstants::CONFIG_COMMISSION, ConfigConstants::CONFIG_FRIEND_TREE, ConfigConstants::CONFIG_COMMISSION_FOUNDATION]);
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
                'content' => 'Nhận điểm từ khóa học đã mua: ' . $item->title,
                'status' => ConfigConstants::TRANSACTION_STATUS_DONE,
            ]);
            $notifServ->createNotif(NotifConstants::TRANS_COMMISSION_RECEIVED, $user->id, [
                'username' => $user->name,
                'amount' => number_format($directCommission, 0, ',', '.'),
            ]);

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

            //save commission indirect + transaction log + update wallet C indrect user

            $indirectCommission = $userService->calcCommission($amount, $commissionRate, $configs[ConfigConstants::CONFIG_COMMISSION], $configs[ConfigConstants::CONFIG_BONUS_RATE]);

            $currentUserId = $user->user_id;
            for ($i = 1; $i < $configs[ConfigConstants::CONFIG_FRIEND_TREE]; $i++) {
                $refUser = User::find($currentUserId);
                if ($refUser) {
                    User::find($refUser->id)->update([
                        'wallet_c' => DB::raw('wallet_c + ' . $indirectCommission),
                    ]);
                    Transaction::create([
                        'user_id' => $refUser->id,
                        'type' => ConfigConstants::TRANSACTION_COMMISSION,
                        'amount' => $indirectCommission,
                        'pay_method' => UserConstants::WALLET_C,
                        'pay_info' => '',
                        'content' => 'Nhận điểm từ ' . $user->name . ' mua khóa học: ' . $item->title,
                        'status' => ConfigConstants::TRANSACTION_STATUS_DONE,
                        'ref_user_id' => $user->id,
                        'ref_amount' => $amount,
                    ]);
                    $notifServ->createNotif(NotifConstants::TRANS_COMMISSION_RECEIVED, $refUser->id, [
                        'username' => $refUser->name,
                        'amount' => number_format($indirectCommission, 0, ',', '.'),
                    ]);
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
                    'content' => 'Nhận quỹ từ ' . $user->name . ' mua khóa học: ' . $item->title,
                    'status' => ConfigConstants::TRANSACTION_STATUS_DONE,
                    'ref_user_id' => $user->id,
                    'ref_amount' => $amount,
                ]);
            }


            DB::commit();
            $notifServ->createNotif(NotifConstants::COURSE_REGISTERED, $user->id, [
                'course' => $item->title,
            ]);
            $notifServ->createNotif(NotifConstants::TRANS_FOUNDATION, $user->id, [
                'amount' => number_format($foundation, 0, ',', '.'),
                'course' => $item->title,
            ]);
            $notifServ->createNotif(NotifConstants::COURSE_HAS_REGISTERED, $author->id, [
                'username' => $author->name,
                'course' => $item->title,
            ]);
            return true;
        });
        if ($result === true) {
            return response()->json(['result' => true]);
        }
        return response($result, 400);
    }
}
