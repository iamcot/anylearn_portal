<?php

namespace App\Services;

use App\Constants\ConfigConstants;
use App\Constants\UserConstants;
use App\Constants\UserDocConstants;
use App\Models\Configuration;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
