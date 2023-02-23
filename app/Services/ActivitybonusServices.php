<?php

namespace App\Services;

use App\Constants\ActivitybonusConstants;
use App\Constants\ConfigConstants;
use App\Constants\UserConstants;
use App\Models\Activitybonus;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ActivitybonusServices
{
    public function updateWalletC($userid, $key, $content, $itemid)
    {
        $user = User::find($userid);
        $activitybonus = new Activitybonus();
        if ($itemid != null) {
            $check = Transaction::where('user_id', $user->id)->where('pay_info', $key)->where('ref_id',$itemid)->first();
            if ($check == null) {
                $user->update([
                    'wallet_c' => $user->wallet_c + $activitybonus->get($key)
                ]);
                $result = Transaction::create([
                    'user_id' => $user->id,
                    'type' => ConfigConstants::TRANSACTION_COMMISSION,
                    'amount' => $activitybonus->get($key) ? $activitybonus->get($key) : 0,
                    'pay_method' => UserConstants::WALLET_C,
                    'pay_info' => $key,
                    'content' => $content,
                    'status' => ConfigConstants::TRANSACTION_STATUS_DONE,
                    'order_id' => null,
                    'ref_id' => $itemid
                ]);
                return $result;
            } else {
                return null;
            }
        } else {
            $check = Transaction::where('user_id', $user->id)->where('pay_info', $key)->first();
            if ($check == null) {
                $user->update([
                    'wallet_c' => $user->wallet_c + $activitybonus->get($key)
                ]);
                $result = Transaction::create([
                    'user_id' => $user->id,
                    'type' => ConfigConstants::TRANSACTION_COMMISSION,
                    'amount' => $activitybonus->get($key),
                    'pay_method' => UserConstants::WALLET_C,
                    'pay_info' => $key,
                    'content' => $content,
                    'status' => ConfigConstants::TRANSACTION_STATUS_DONE,
                    'order_id' => null,
                    'ref_id' => null
                ]);
                return $result;
            } else {
                return null;
            }
        }
        return null;
    }
}
