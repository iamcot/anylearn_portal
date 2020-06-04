<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ConfigConstants;
use App\Constants\ItemConstants;
use App\Constants\OrderConstants;
use App\Constants\UserConstants;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Transaction;
use App\Models\User;
use App\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionApi extends Controller
{
    public function saveDeposit(Request $request)
    {
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }

        $rs = Transaction::create([
            'user_id' => $user->id,
            'type' => ConfigConstants::TRANSACTION_DEPOSIT,
            'amount' => $request->get('amount'),
            'pay_method' => $request->get('pay_method'),
            'pay_info' => '',
            'content' => 'Nạp tiền vào ví'
        ]);

        return response()->json(['result' => $rs != null ? true : false]);
    }

    public function history(Request $request)
    {
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }
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
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }
        $item = Item::find($itemId);
        if (!$item) {
            return response('Trang không tồn tại', 404);
        }
        try {
            DB::transaction(function () use ($user, $item) {
                $status = $user->wallet_m >= $item->price ? OrderConstants::STATUS_DELIVERED : OrderConstants::STATUS_NEW;
                $amount = $item->price;
                //save order
                $newOrder = Order::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'quantity' => 1,
                    'status' => $status,
                    'payment' => UserConstants::WALLET_M,
                ]);
                //save order details
                OrderDetail::create([
                    'order_id' => $newOrder->id,
                    'user_id' => $user->id,
                    'item_id' => $item->id,
                    'unit_price' => $item->price,
                    'paid_price' => $item->price,
                    'status' => $status,
                ]);

                //if no walletM, break
                if ($user->wallet_m < $amount) {
                    DB::commit();
                    return;
                    // return response()->json(['result' => true]);
                }
                $author = User::find($item->user_id);

                //cal commission direct, update direct user wallet M + wallet C, save transaction log
                $configM = new Configuration();
                $configs = $configM->gets([ConfigConstants::CONFIG_BONUS_RATE, ConfigConstants::CONFIG_DISCOUNT, ConfigConstants::CONFIG_COMMISSION, ConfigConstants::CONFIG_FRIEND_TREE, ConfigConstants::CONFIG_COMMISSION_FOUNDATION]);
                $userService = new UserServices();

                $directCommission = $userService->calcCommission($amount, $author->commission_rate, $configs[ConfigConstants::CONFIG_DISCOUNT], $configs[ConfigConstants::CONFIG_BONUS_RATE]);

                User::find($user->id)->update([
                    'wallet_m' => DB::raw('wallet_m - ' . $amount),
                    'wallet_c' => DB::raw('wallet_c + ' . $directCommission),
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
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => ConfigConstants::TRANSACTION_COMMISSION,
                    'amount' => $directCommission,
                    'pay_method' => UserConstants::WALLET_C,
                    'pay_info' => '',
                    'content' => 'Nhận điểm từ mua khóa học: ' . $item->title,
                    'status' => ConfigConstants::TRANSACTION_STATUS_DONE,
                ]);

                //pay author 
                // $authorCommission = $amount * $author->commission_rate / $configs[ConfigConstants::CONFIG_BONUS_RATE];
                // User::find($item->user_id)->update([
                //     'wallet_c' => DB::raw('wallet_c + ' . $authorCommission),
                // ]);

                // Transaction::create([
                //     'user_id' => $author->id,
                //     'type' => ConfigConstants::TRANSACTION_COMMISSION,
                //     'amount' => $authorCommission,
                //     'pay_method' => UserConstants::WALLET_C,
                //     'pay_info' => '',
                //     'content' => 'Nhận điểm từ bán khóa học: ' . $item->title,
                //     'status' => ConfigConstants::TRANSACTION_STATUS_DONE,
                // ]);

                //save commission indirect + transaction log + update wallet C indrect user

                $indirectCommission = $userService->calcCommission($amount, $author->commission_rate, $configs[ConfigConstants::CONFIG_COMMISSION], $configs[ConfigConstants::CONFIG_BONUS_RATE]);

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
                        $currentUserId = $refUser->user_id;
                    } else {
                        break;
                    }
                }
                //foundation 
                $foundation = $userService->calcCommission($amount, $author->commission_rate, $configs[ConfigConstants::CONFIG_COMMISSION_FOUNDATION], 1);
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

                DB::commit();
            });
        } catch (\Exception $e) {
            Log::error($e);
            return response("Không thể thực hiện được đơn hàng" . $e, 500);
        }
        return response()->json(['result' => true]);
    }
}
