<?php

namespace App\Models;

use App\Constants\ConfigConstants;
use App\Constants\UserConstants;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'type', 'amount', 'pay_method', 'pay_info', 'order_id', 'content', 'status',
        'ref_user_id', 'ref_amount'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function pendingWalletM($userId)
    {
        return Transaction::where('user_id', $userId)
            ->where('type', ConfigConstants::TRANSACTION_DEPOSIT)
            ->where('status', ConfigConstants::TRANSACTION_STATUS_PENDING)
            ->sum('amount');
    }

    public function pendingWalletC($userId)
    {
        return Transaction::where('user_id', $userId)
            ->where('type', ConfigConstants::TRANSACTION_WITHDRAW)
            ->where('status', ConfigConstants::TRANSACTION_STATUS_PENDING)
            ->sum('amount');
    }

    public function history($userId, $wallet)
    {
        $query = Transaction::where('user_id', $userId);
        if ($wallet == UserConstants::WALLET_M) {
            $query = $query->whereIn('type', [ConfigConstants::TRANSACTION_ORDER, ConfigConstants::TRANSACTION_DEPOSIT, ConfigConstants::TRANSACTION_WITHDRAW]);
        } else {
            $query = $query->whereIn('type', [ConfigConstants::TRANSACTION_EXCHANGE, ConfigConstants::TRANSACTION_WITHDRAW, ConfigConstants::TRANSACTION_COMMISSION]);
        }
        return $query->orderby('id', 'desc')
            ->get();
    }

}
