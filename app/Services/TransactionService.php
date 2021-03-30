<?php

namespace App\Services;

use App\Constants\ConfigConstants;
use App\Constants\NotifConstants;
use App\Constants\UserConstants;
use App\Constants\UserDocConstants;
use App\Models\Configuration;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
}
