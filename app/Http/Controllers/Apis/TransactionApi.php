<?php

namespace App\Http\Controllers\Apis;

use App\Constants\ConfigConstants;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionApi extends Controller
{
    public function saveDeposit(Request $request, int $amount)
    {
        $user = $this->isAuthedApi($request);
        if (!($user instanceof User)) {
            return $user;
        }

        $rs = Transaction::create([
            'user_id' => $user->id,
            'type' => ConfigConstants::TRANSACTION_DEPOSIT,
            'amount' => $amount,
            'pay_method' => 'atm',
            'pay_info' => '',
            'content' => 'Nạp tiền vào ví'
        ]);

        return response()->json($rs);
    }
}
