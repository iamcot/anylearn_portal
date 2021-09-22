<?php

namespace App\Http\Controllers;

use App\Constants\ConfigConstants;
use App\Constants\FileConstants;
use App\Constants\NotifConstants;
use App\Constants\OrderConstants;
use App\DataObjects\ServiceResponse;
use App\Models\Configuration;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherGroup;
use App\Models\VoucherUsed;
use App\PaymentGateway\OnepayLocal;
use App\PaymentGateway\Processor;
use App\Services\FileServices;
use App\Services\TransactionService;
use App\Services\UserServices;
use Exception;
use Hamcrest\Type\IsNumeric;
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

    public function orderOpen(Request $request)
    {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->isMod($user->role)) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $this->data['orders'] = DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->where('orders.status', OrderConstants::STATUS_NEW)
            ->select('orders.*', 'users.name', 'users.phone')
            ->paginate();
        $this->data['navText'] = __('Đơn hàng chờ xác nhận');
        return view('transaction.order_open', $this->data);
    }

    public function approveOrder($orderId)
    {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->isMod($user->role)) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $order = Order::find($orderId);
        if ($order->status != OrderConstants::STATUS_NEW) {
            return redirect()->back()->with('notify', 'Status đơn hàng không đúng');
        }
        $transService = new TransactionService();
        $transService->approveRegistrationAfterWebPayment($orderId);
        return redirect()->back()->with('notify', 'Đã xác nhận thành công.');
    }

    public function add2cart(Request $request)
    {
        Log::debug("add2cart");
        Log::debug($request->fullUrl());
        if ($request->get('_user')) {
            $user = $request->get('_user');
            $this->data['api_token'] = $user->api_token;
        } else {
            $user = Auth::user();
            $this->data['api_token'] = null;
        }
        $this->detectUserAgent($request);

        if (!$user) {
            return redirect()->back()->with('notify', __('Bạn cần đăng nhập để làm thao tác này.'));
        }
        $transService = new TransactionService();
        $result = $transService->placeOrderOneItem($request, $user, $request->get('class'), true);
        if (is_numeric($result)) {
            return redirect()->route('checkout.finish', ['order_id' => $result]);
            // return redirect()->back()->with('notify', "Bạn đã đăng ký khoá học thành công!");
        } else if ($result === ConfigConstants::TRANSACTION_STATUS_PENDING) {
            if ($this->data['api_token']) {
                return redirect()->route('cart', ['api_token' => $this->data['api_token']])->with('notify', "Đăng ký khoá học thành công. Vui lòng tiếp tục để hoàn thành bước thanh toán.");
            }
            return redirect()->route('cart')->with('notify', "Đăng ký khoá học thành công. Vui lòng tiếp tục để hoàn thành bước thanh toán.");
        } else {
            if ($this->data['api_token']) {
                return redirect()->route('cart', ['api_token' => $this->data['api_token']])->with('notify', $result);
            } else {
                return redirect()->back()->with('notify', $result);
            }
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

    public function remove2cart(Request $request, $orderDetailsId)
    {
        $user = $request->get('_user') ?? Auth::user();

        $orderDetail = OrderDetail::find($orderDetailsId);
        if (!$orderDetail) {
            return redirect()->back()->with('notify', 'Dữ liệu không đúng');
        }
        $order = Order::find($orderDetail->order_id);
        if ($user->id != $order->user_id) {
            return redirect()->back()->with('notify', 'Dữ liệu không đúng');
        }
        $transService = new TransactionService();
        $result = $transService->remove2Cart($orderDetail, $order, $user);
        if ($result) {
            return redirect()->back()->with('notify', 'Cập nhật thành công');
        }
        return redirect()->back()->with('notify', 'Có lỗi xảy ra, vui lòng thử lại.');
    }

    public function cart(Request $request)
    {
        Log::debug("Cart request");
        Log::debug($request->fullUrl());
        if ($request->get('_user')) {
            $user = $request->get('_user');
            $this->data['api_token'] = $user->api_token;
        } else {
            $user = Auth::user();
            $this->data['api_token'] = null;
        }

        $this->detectUserAgent($request);

        if (!$user) {
            return redirect()->back()->with('notify', __('Bạn cần đăng nhập để làm thao tác này.'));
        }
        $openOrder = Order::where('status', OrderConstants::STATUS_NEW)
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->first();
        if ($openOrder) {
            $orderDetails = DB::table('order_details AS od')
                ->join('items', 'items.id', '=', 'od.item_id')
                ->leftJoin('items as i2', 'i2.id', '=', 'items.item_id')
                ->where('od.order_id', $openOrder->id)
                ->select('od.*', 'items.title', 'items.image', 'i2.title AS class_name')
                ->get();
            $this->data['order'] = $openOrder;
            $this->data['detail'] = $orderDetails;
            $voucherUsed = DB::table('vouchers_used')
                ->join('vouchers', 'vouchers.id', '=', 'vouchers_used.voucher_id')
                ->select('vouchers_used.id', 'vouchers.voucher')
                ->where('order_id', $openOrder->id)->first();
            if ($voucherUsed) {
                $this->data['voucherUsed'] = $voucherUsed;
            }
        } else {
            $this->data['order'] = null;
            $this->data['detail'] = null;
        }

        $this->data['term'] = "Chính sách thanh toán.";
        $paymentConfigs = config('payment');
        $envPayments = explode(",", env('PAYMENT_METHOD'));
        $this->data['payments'] = [];
        foreach ($envPayments as $k) {
            if (!empty($paymentConfigs[$k])) {
                $this->data['payments'][$k] = $paymentConfigs[$k];
            }
        }
        $configM = new Configuration();
        $doc = $configM->getDoc(ConfigConstants::GUIDE_PAYMENT_TERM);
        if ($doc) {
            $this->data['term'] = $doc->value;
        }
        return view('checkout.cart', $this->data);
    }

    public function applyVoucher(Request $request)
    {
        if ($request->get('_user')) {
            $user = $request->get('_user');
            $this->data['api_token'] = $user->api_token;
        } else {
            $user = Auth::user();
            $this->data['api_token'] = null;
        }
        $orderId = $request->get('order_id');
        $order = Order::find($orderId);
        if (empty($order)) {
            return redirect()->back()->with('notify', 'Đơn hàng hoặc mã khuyến mãi không hợp lệ.');
        }
        $voucher = $request->get('payment_voucher');
        if ($request->get('cart_action') == 'apply_voucher') {
            
            $voucherM = new Voucher();
            try {
                $voucherM->useVoucherPayment($user->id, $voucher, $orderId);
            } catch (Exception $ex) {
                return redirect()->back()->with('notify', $ex->getMessage());
            }
    
            $voucherDB = Voucher::where('voucher', $voucher)->first();
    
            $transService = new TransactionService();
            $res = $transService->recalculateOrderAmountWithVoucher($orderId, $voucherDB->value);
            if (!$res) {
                return redirect()->back()->with('notify', 'Mã khuyến mãi không hợp lệ.');
            }
    
            return redirect()->back()->with('notify', 'Áp dụng voucher thành công.');
        } else if ($request->get('cart_action') == 'remove_voucher'){
            VoucherUsed::find($request->get('voucher_userd_id'))->delete();
            $transService = new TransactionService();
            $res = $transService->recalculateOrderAmount($orderId);
            return redirect()->back()->with('notify', 'Đã huỷ voucher.');
        }
        
    }

    public function payment(Request $request)
    {
        if ($request->get('_user')) {
            $user = $request->get('_user');
            $this->data['api_token'] = $user->api_token;
        } else {
            $user = Auth::user();
            $this->data['api_token'] = null;
        }

        if ($request->get('payment') == 'atm') {
            return redirect()->route('checkout.paymenthelp', ['order_id' => $request->input('order_id')]);
        }
        $payment = $request->get('payment');
        $processor = Processor::getProcessor($payment);

        if (!$processor) {
            return redirect()->back()->with('notify', 'Phương thức thanh toán không hợp lệ');
        }
        $openOrder = Order::where('status', OrderConstants::STATUS_NEW)
            ->where('user_id', $user->id)
            ->first();
        if (!$openOrder) {
            return redirect()->back()->with('notify', __('Bạn không có đơn hàng nào, hãy thử tìm một khoá học và đăng ký trước nhé.'));
        }

        $input = [
            'amount' => $openOrder->amount,
            'orderid' => $openOrder->id,
            'ip' => $request->ip(),
        ];

        $validate = $processor->validate($input);
        if (true !== $validate) {
            return redirect()->back()->with('notify', 'Phương thức thanh toán không hợp lệ');
        }

        try {
            $response = $processor->processPayment();
        } catch (\Exception $e) {
            $response = new ServiceResponse(false, 'EXCEPTION', $e);
        }
        if ($response->status) {
            Log::info('Payment Redirect', ['url' => $response->data]);
            return redirect()->to($response->data);
        } else {
            Log::error('Make payment error,', ['data' => $response]);
            return redirect()->back()->with('notify', 'Có lỗi khi thanh toán, vui lòng thử lại');
        }
    }

    public function finish(Request $request)
    {
        $this->detectUserAgent($request);

        $orderId = $request->get('order_id');
        if (!$orderId) {
            return redirect('/')->with('notify', 'Yêu cầu không hợp lệ');
        }
        $order = Order::find($orderId);
        if (!$order) {
            return redirect('/')->with('notify', __('Bạn không có đơn hàng nào, hãy thử tìm một khoá học và đăng ký trước nhé.'));
        }

        if (!$this->data['isApp']) {
            $user = Auth::user();
            if ($order->user_id != $user->id) {
                return redirect("/");
            }
        }
        $orderDetails = DB::table('order_details AS od')
            ->join('items', 'items.id', '=', 'od.item_id')
            ->leftJoin('items as i2', 'i2.id', '=', 'items.item_id')
            ->where('od.order_id', $order->id)
            ->select('od.*', 'items.title', 'items.image', 'i2.title AS class_name')
            ->get();

        $this->data['order'] = $order;
        $this->data['detail'] = $orderDetails;
        return view('checkout.finish', $this->data);
    }

    public function paymentResult(Request $request)
    {
        $result = $request->all();
        Log::info('Payment Result, ', ['data' => $request->fullUrl()]);

        $orderId = $result['orderId'];
        $order = Order::find($orderId);
        $user = User::find($order->user_id);

        if (!isset($result['status'])) {
            return redirect()->route('cart', ['api_token' => $user->api_token]);
        }
        if ($result['status'] == 1) {
            $orderId = $result['orderId'];
            $transService = new TransactionService();
            $transService->approveRegistrationAfterWebPayment($orderId);
            return redirect()->route('checkout.finish', ['order_id' => $orderId]);
        }

        $this->detectUserAgent($request);
        if ($this->data['isApp']) {
            return redirect()->route('cart', ['api_token' => $user->api_token])->with('notify', isset($result['message']) ? $result['message'] : 'Có lỗi xảy ra. vui lòng thử lại!');
        }
        return redirect()->route('cart')->with('notify', isset($result['message']) ? $result['message'] : 'Có lỗi xảy ra. vui lòng thử lại!');
    }

    public function paymentHelp(Request $request)
    {
        $this->detectUserAgent($request);
        $orderId = $request->get('order_id');
        $order = Order::find($orderId);
        if (!$this->data['isApp']) {
            $user = Auth::user();
            if ($order->user_id != $user->id) {
                return redirect("/");
            }
        }

        $this->data['bank'] = config('bank');
        $this->data['orderAmount'] = $order->amount;
        $this->data['orderId'] = $order->id;
        return view('checkout.paymenthelp', $this->data);
    }

    public function notify(Request $request, $payment)
    {
        $processor = Processor::getProcessor($payment);
        if ($processor == null) {
            return;
        }
        try {
            Log::info("[NOTIFY RESULT]:", ['data' => $request->fullUrl()]);
            $result = $processor->processFeedbackData($request->all());
            if ($result['status'] == 1) {
                $orderId = $result['orderId'];
                $transService = new TransactionService();
                $rs = $transService->approveRegistrationAfterWebPayment($orderId);
                Log::info("[NOTIFY PAYMENT RESULT]:", ['order' => $result['orderId'], 'result' => $rs]);
            }
            $data = $processor->prepareNotifyResponse($request->all(), $result);
            if (is_array($data)) {
                return response()->json($data);
            } else {
                Log::info("[NOTIFY PAYMENT NO DATA]:", ['request' => $request]);
                echo $data;
            }
        } catch (\Exception $e) {
            Log::error("[NOTIFY PAYMENT ERROR]:", ['e' => $e->getMessage()]);
            echo $e;
        }
    }

    public function return(Request $request, $payment)
    {
        Log::info("[RETURN RESULT]:", ['data' => $request->fullUrl()]);
        $processor = Processor::getProcessor($payment);
        if ($processor == null) {
            return redirect(env('CALLBACK_SERVER'));
        }

        try {
            $url = $processor->processReturnData($request->all());
            if (!empty($url)) {
                return redirect($url);
            }
        } catch (\Exception $e) {
            echo $e;
        }
        return redirect(env('CALLBACK_SERVER'));
    }
}
