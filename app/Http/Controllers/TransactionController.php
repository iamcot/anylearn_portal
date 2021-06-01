<?php

namespace App\Http\Controllers;

use App\Constants\ConfigConstants;
use App\Constants\FileConstants;
use App\Constants\NotifConstants;
use App\Models\Configuration;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FileServices;
use App\Services\TransactionService;
use App\Services\UserServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function transaction(Request $request)
    {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->isMod($user->role)) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $this->data['transaction'] = Transaction::whereIn('type', [ConfigConstants::TRANSACTION_DEPOSIT, ConfigConstants::TRANSACTION_WITHDRAW])
            ->orderby('id', 'desc')
            ->with('user')
            ->paginate(20);
        $this->data['navText'] = __('Quản lý Giao dịch');
        return view('transaction.list', $this->data);
    }

    public function add2cart(Request $request) {
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('notify', __('Bạn cần đăng nhập để làm thao tác này.'));
        }
        $transService = new TransactionService();
        $result = $transService->placeOrderOneItem($request, $user, $request->get('class'), true);
        if ($result === true) {
            return redirect()->back()->with('notify',"Bạn đã đăng ký khoá học thành công!");
        } else if ($result === ConfigConstants::TRANSACTION_STATUS_PENDING) {
            return redirect()->route('checkout.paymenthelp')->with('notify', "Bạn đã đăng ký khoá học thành công. Vui lòng hoàn tất nạp tiền theo hướng dẫn.");
        } else {
            return redirect()->back()->with('notify', $result);
        }
        
    }

    public function commission(Request $request)
    {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->isMod($user->role)) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $this->data['transaction'] = Transaction::whereNotIn('type', [ConfigConstants::TRANSACTION_DEPOSIT, ConfigConstants::TRANSACTION_WITHDRAW])
            ->orderby('id', 'desc')
            ->with('user')
            ->paginate(20);
        $this->data['navText'] = __('Lịch sử nhận hoa hồng');
        return view('transaction.commission', $this->data);
    }

    public function status(Request $request, $id, $status)
    {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->isMod($user->role)) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return redirect()->back()->with('notify', 'Giao dịch không tồn tại');
        }
        $transService = new TransactionService();
        if ($status == ConfigConstants::TRANSACTION_STATUS_DONE) {
            if ($transaction->status != ConfigConstants::TRANSACTION_STATUS_PENDING) {
                return redirect()->back()->with('notify', 'Thao tác không hợp lệ');
            }
            Transaction::find($id)->update([
                'status' => $status
            ]);
            
            if ($transaction->type == ConfigConstants::TRANSACTION_DEPOSIT) {
                User::find($transaction->user_id)->update([
                    'wallet_m' => DB::raw('wallet_m + ' . $transaction->amount),
                ]);
                $transService->approveRegistrationAfterDeposit($transaction->user_id);
            }

            $notifServ = new Notification();
            $notifServ->createNotif(NotifConstants::TRANS_DEPOSIT_APPROVED, $transaction->user_id, []);
        } elseif ($status == ConfigConstants::TRANSACTION_STATUS_REJECT) {
            if ($transaction->status != ConfigConstants::TRANSACTION_STATUS_PENDING) {
                return redirect()->back()->with('notify', 'Thao tác không hợp lệ');
            }
            Transaction::find($id)->update([
                'status' => $status
            ]);
            $notifServ = new Notification();
            $notifServ->createNotif(NotifConstants::TRANS_DEPOSIT_REJECTED, $transaction->user_id, []);
        }
        return redirect()->back()->with('notify', 'Cập nhật thành công');
    }
}
