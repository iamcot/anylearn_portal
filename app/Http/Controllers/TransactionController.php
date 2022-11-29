<?php

namespace App\Http\Controllers;

use App\Constants\ConfigConstants;
use App\Constants\FileConstants;
use App\Constants\NotifConstants;
use App\Constants\OrderConstants;
use App\Constants\UserConstants;
use App\DataObjects\ServiceResponse;
use App\Models\Configuration;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserBank;
use App\Models\Voucher;
use App\Models\VoucherGroup;
use App\Models\VoucherUsed;
use App\PaymentGateway\OnepayLocal;
use App\PaymentGateway\Processor;
use App\Services\FileServices;
use App\Services\TransactionService;
use App\Services\UserServices;
use Aws\Api\Parser\Crc32ValidatingParser;
use Exception;
use Hamcrest\Type\IsNumeric;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

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
            ->where('orders.status', OrderConstants::STATUS_PAY_PENDING)
            ->select(
                'orders.*',
                'users.name',
                'users.phone',
                DB::raw("(SELECT GROUP_CONCAT(items.title SEPARATOR ',' ) as classes FROM order_details AS os JOIN items ON items.id = os.item_id WHERE os.order_id = orders.id) as classes")
            )->orderby('orders.id', 'desc')
            ->paginate();
        $this->data['navText'] = __('Đơn hàng chờ xác nhận');
        return view('transaction.order_open', $this->data);
    }

    public function allOrder(Request $request)
    {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->haveAccess($user->role, 'order.all')) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        if ($request->input('action') == 'clear') {
            return redirect()->route('order.all');
        }
        $orders = DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.user_id');

        if (Auth::user()->role == UserConstants::ROLE_SALE) {
            $orders = $orders->where('orders.sale_id', $user->id);
        }

        if ($request->input('id_f') > 0) {
            if ($request->input('id_t') > 0) {
                $orders = $orders->where('orders.id', '>=', $request->input('id_f'))->where('orders.id', '<=', $request->input('id_t'));
            } else {
                $orders = $orders->where('orders.id', $request->input('id_f'));
            }
        }
        if ($request->input('name')) {
            $orders = $orders->where('users.name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->input('phone')) {
            $orders = $orders->where('users.phone', $request->input('phone'));
        }

        if ($request->input('status')) {
            $orders = $orders->where('orders.status', $request->input('status'));
        } else {
            $orders = $orders->where('orders.status', '!=', 'new');
        }
        if ($request->input('payment')) {
            $orders = $orders->where('orders.payment', $request->input('payment'));
        }
        if ($request->input('date')) {
            $orders = $orders->whereDate('orders.created_at', '>=', $request->input('date'));
        }

        if ($request->input('datet')) {
            $orders = $orders->whereDate('orders.created_at', '<=', $request->input('datet'));
        }
        $exOrder = new Order();
        if ($request->input('action') == 'file') {
            $order = $exOrder -> searchOrders($request, true);
            if (!$order) {
                return redirect()->route('transaction.order_all');
            }
            $headers = [
                // "Content-Encoding" => "UTF-8",
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=anylearn_order_" . now() . ".csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];
            $callback = function () use ($order) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($file, array_keys($order[0]));
                foreach ($order as $row) {
                    mb_convert_encoding($row, 'UTF-16LE', 'UTF-8');
                    fputcsv($file, $row);
                }
                fclose($file);
            };
            // dd($callback);
             return response()->stream($callback, 200, $headers);
            // return response()->download($callback, 200, $headers);
        }
        $this->data['orders'] = $orders->leftJoin('vouchers_used', 'vouchers_used.order_id', '=', 'orders.id')
            ->leftJoin('vouchers', 'vouchers_used.voucher_id', '=', 'vouchers.id')
            ->leftJoin('transactions', function ($query) {
                $query->on('transactions.order_id', '=', 'orders.id')
                    ->where('transactions.type', '=', ConfigConstants::TRANSACTION_EXCHANGE);
            })
            ->select(
                'orders.*',
                'users.name',
                'users.phone',
                'users.address',
                'vouchers.voucher',
                'vouchers.value AS voucher_value',
                'transactions.amount AS anypoint',
                DB::raw("(SELECT GROUP_CONCAT(items.title SEPARATOR ',' ) as classes FROM order_details AS os JOIN items ON items.id = os.item_id WHERE os.order_id = orders.id) as classes")
            )->orderby('orders.id', 'desc')
            ->paginate();
        $this->data['navText'] = __('Đơn hàng đã đặt');
        return view('transaction.order_all', $this->data);
    }

    public function approveOrder($orderId)
    {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->isMod($user->role)) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $order = Order::find($orderId);
        if ($order->status != OrderConstants::STATUS_PAY_PENDING) {
            return redirect()->back()->with('notify', 'Status đơn hàng không đúng');
        }
        $transService = new TransactionService();
        $transService->approveRegistrationAfterWebPayment($orderId, OrderConstants::PAYMENT_ATM);
        return redirect()->back()->with('notify', 'Đã xác nhận thành công.');
    }

    public function rejectOrder($orderId)
    {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->isMod($user->role)) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $order = Order::find($orderId);
        if ($order->status != OrderConstants::STATUS_PAY_PENDING) {
            return redirect()->back()->with('notify', 'Status đơn hàng không đúng');
        }
        $transService = new TransactionService();
        $transService->rejectRegistration($orderId);
        return redirect()->back()->with('notify', 'Thao tác thành công');
    }

    public function add2cart(Request $request)
    {
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
        if ($result === ConfigConstants::TRANSACTION_STATUS_PENDING) {
            if ($this->data['api_token']) {
                return redirect()->route('cart', ['api_token' => $this->data['api_token']])->with('notify', "Đăng ký khoá học thành công. Vui lòng tiếp tục để hoàn thành bước thanh toán.");
            }
            return redirect()->route('cart')->with('notify', "Đăng ký khoá học thành công. Vui lòng tiếp tục để hoàn thành bước thanh toán.");
        } else if (is_numeric($result)) {
            return redirect()->route('checkout.finish', ['order_id' => $result]);
        } else {
            if ($this->data['api_token']) {
                return redirect()->route('cart', ['api_token' => $this->data['api_token']])->with('notify', $result);
            } else {
                // dd($result);
                return redirect()->back()->with('notify', $result);
            }
        }
    }

    public function commission(Request $request)
    {
        $userService = new UserServices();
        $user = Auth::user();

        $transaction = Transaction::whereNotIn('type', [ConfigConstants::TRANSACTION_DEPOSIT, ConfigConstants::TRANSACTION_WITHDRAW])
        ->orderby('id', 'desc')
        ->with('user')
        ->with('order')
        ->whereHas('order',function($query){
            $query->where('status','delivered');
        });
        if ($request->input('action') == 'clear') {
            return redirect()->route('transaction.commission');
        }
        if ($request->input('id_f') > 0) {
            if ($request->input('id_t') > 0) {
                $transaction = $transaction->where('id', '>=', $request->input('id_f'))->where('id', '<=', $request->input('id_t'));
            } else {
                $transaction = $transaction->where('id', $request->input('id_f'));
            }
        }
        if ($request->input('type')) {
            $transaction = $transaction->where('type', $request->input('type'));
        }
        if ($request->input('name')) {
            $transaction = $transaction->whereHas('user',function($query) use ($request){
                $query->where('name','like','%' . $request->input('name') . '%');
            }
            );
        }
        if ($request->input('phone')) {
            $transaction = $transaction->whereHas('user',function($query) use ($request){
                $query->where('phone',$request->input('phone'));
            }
            );
        }
        if ($request->input('date')) {
            $transaction = $transaction->whereDate('created_at', '>=', $request->input('date'));

        }
        if ($request->input('datet')) {
            $transaction = $transaction->whereDate('created_at', '<=', $request->input('datet'));

        }
        if (!$userService->haveAccess($user->role, 'transaction.commission')) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        $this->data['transaction'] = $transaction->paginate();
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
        if ($request->get('_user')) {
            $user = $request->get('_user');
            $this->data['api_token'] = $user->api_token;
        } else {
            $user = Auth::user();
            $this->data['api_token'] = null;
        }
        $this->data['user'] = $user;

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
                ->join('i18n_contents','i18n_contents.content_id','=','od.item_id')
                ->join('users AS u2', 'u2.id', '=', 'od.user_id')
                ->leftJoin('items as i2', 'i2.id', '=', 'items.item_id')
                ->where('i18n_contents.tbl','items')
                ->where('i18n_contents.col','title')
                ->where('od.order_id', $openOrder->id)
                ->select('od.*','i18n_contents.i18n_content' ,'items.title', 'items.image', 'i2.title AS class_name', 'u2.name as childName', 'u2.id as childId')
                ->get();
                // dd($orderDetails);
            $this->data['order'] = $openOrder;
            $this->data['detail'] = $orderDetails;
            $pointUsed = Transaction::where('type', ConfigConstants::TRANSACTION_EXCHANGE)
                ->where('order_id', $openOrder->id)->first();
            if ($pointUsed) {
                $this->data['pointUsed'] = $pointUsed;
            } else {
                $voucherUsed = DB::table('vouchers_used')
                    ->join('vouchers', 'vouchers.id', '=', 'vouchers_used.voucher_id')
                    ->select('vouchers_used.id', 'vouchers.voucher')
                    ->where('order_id', $openOrder->id)->first();
                if ($voucherUsed) {
                    $this->data['voucherUsed'] = $voucherUsed;
                }
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
        $this->data['bonusRate'] = $configM->get(ConfigConstants::CONFIG_BONUS_RATE);
        if ($doc) {
            $this->data['term'] = $doc->value;
        }
        $saveBanks = UserBank::where('user_id', $user->id)->where('status', 1)->get();
        $this->data['saveBanks'] = [];
        foreach ($saveBanks as $bank) {
            $config = config('bankinfo.' . $bank->card_type);
            if (empty($config)) {
                $config = [
                    'name' => 'ATM',
                    'logo' => 'https://mtf.onepay.vn/paygate/assets/img/atm_logo.png',
                ];
            }
            $this->data['saveBanks'][] = [
                'id' => $bank->id,
                'tokenNum' => $bank->token_num,
                'tokenExp' => $bank->token_exp,
                'type' => $bank->card_type,
                'name' => $config['name'],
                'logo' => $config['logo'],
            ];
        }

        return view(env('TEMPLATE', '') . 'checkout.cart', $this->data);
    }

    public function exchangePoint(Request $request)
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
            return redirect()->back()->with('notify', 'Đơn hàng không hợp lệ.');
        }

        if ($request->get('cart_action') == 'exchangePoint') {
            $point = $request->get('payment_point');

            $configM = new Configuration();
            $bonusRate = $configM->get(ConfigConstants::CONFIG_BONUS_RATE);
            $pointRequired = $order->amount / $bonusRate;
            $pointRequired = $pointRequired > 1 ? $pointRequired : 1;
            if (!$point || $point < 0 || $pointRequired < $point) {
                return redirect()->back()->with('notify', 'Số anyPoint không phù hợp.');
            }
            try {
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => ConfigConstants::TRANSACTION_EXCHANGE,
                    'amount' => $point,
                    'pay_method' => UserConstants::WALLET_C,
                    'pay_info' => '',
                    'content' => 'Đổi ' . $point . ' cho đơn #' . $order->id,
                    'status' => ConfigConstants::TRANSACTION_STATUS_DONE,
                    'order_id' => $order->id
                ]);
                User::find($user->id)->update([
                    'wallet_c' => ($user->wallet_c - $point)
                ]);
            } catch (Exception $ex) {
                return redirect()->back()->with('notify', $ex->getMessage());
            }

            $transService = new TransactionService();
            $res = $transService->recalculateOrderAmountWithAnyPoint($orderId, $point, $bonusRate);
            if (!$res) {
                return redirect()->back()->with('notify', 'Có lỗi khi sử dụng anyPoint. Vui vòng thử lại hoặc liên hệ bộ phận hỗ trợ.');
            }

            return redirect()->back()->with('notify', 'Sử dụng anyPoint thành công.');
        } else if ($request->get('cart_action') == 'remove_point') {
            $tnx = Transaction::find($request->get('point_used_id'));
            User::find($user->id)->update([
                'wallet_c' => ($user->wallet_c + $tnx->amount)
            ]);
            $tnx->delete();
            $transService = new TransactionService();
            $res = $transService->recalculateOrderAmount($orderId);
            return redirect()->back()->with('notify', 'Đã huỷ đổi điểm.');
        }
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
                $dbVoucher = $voucherM->getVoucherData($user->id, $voucher);
                $voucherM->useVoucherPayment($user->id, $orderId, $dbVoucher);
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
        } else if ($request->get('cart_action') == 'remove_voucher') {
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
        $payment = $request->get('payment');
        $orderId = $request->input('order_id');
        $saveCard = $request->get('save_card') == 'on' ? true : false;
        $tokenNum = false;
        $tokenExp = false;
        $transService = new TransactionService();

        $res = $transService->verifyVoucherInOrderBeforePayment($orderId);
        if (!$res) {
            return redirect()->back()->with('notify', 'Mã voucher trong đơn hàng không còn hợp lệ .');
        }

        if ($payment == 'free') {
            $transService = new TransactionService();
            $transService->approveRegistrationAfterWebPayment($orderId, OrderConstants::PAYMENT_FREE);
            return redirect()->route('checkout.finish', ['order_id' => $orderId]);
        }

        if ($payment == 'atm') {
            $transService->paymentPending($orderId);
            return redirect()->route('checkout.paymenthelp', ['order_id' => $orderId]);
        }
        if ($payment != 'onepaylocal') {
            $existsBank = UserBank::where('id', $payment)->where('user_id', $user->id)->first();
            if (!$existsBank) {
                return redirect()->back()->with('notify', 'Phương thức thanh toán không tồn tại');
            }
            $payment = 'onepaylocal';
            $saveCard = false;
            $tokenNum = $existsBank->token_num;
            $tokenExp = $existsBank->token_exp;
        }
        $processor = Processor::getProcessor($payment);
        if ($processor === null) {
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
            'save_card' => $saveCard,
            'token_num' => $tokenNum,
            'token_exp' => $tokenExp,
            'user_id' => $user->id,
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
        return view(env('TEMPLATE', '') . 'checkout.finish', $this->data);
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
            $transService->approveRegistrationAfterWebPayment($orderId, OrderConstants::PAYMENT_ONEPAY);

            if (!empty($result['newTokenNum'])) {
                $newToken = $result['newTokenNum'];
                $newTokenExp = !empty($result['newTokenExp']) ? $result['newTokenExp'] : '';
                $newCardType = !empty($result['newCardType']) ? $result['newCardType'] : '';
                $newCardUid = !empty($result['newCardUid']) ? $result['newCardUid'] : '';
                $exists = UserBank::where('card_uid', $newCardUid)->where('user_id', $user->id)->count();
                if ($exists == 0) {
                    UserBank::create([
                        'user_id' => $user->id,
                        'token_num' => $newToken,
                        'token_exp' => $newTokenExp,
                        'card_type' => $newCardType,
                        'card_uid' => $newCardUid,
                    ]);
                } else {
                    UserBank::where('card_uid', $newCardUid)->where('user_id', $user->id)->update([
                        'token_num' => $newToken,
                        'token_exp' => $newTokenExp,
                    ]);
                }
            }
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

        $this->data['banks'] = config('bank');
        $this->data['orderAmount'] = $order->amount;
        $this->data['orderId'] = $order->id;
        return view(env('TEMPLATE', '') . 'checkout.paymenthelp', $this->data);
    }

    public function notify(Request $request, $payment)
    {
        $processor = Processor::getProcessor($payment);
        if ($processor == null) {
            return;
        }
        try {
            Log::info("[NOTIFY RESULT]:", ['data' => $request->fullUrl()]);
            $query = $request->getQueryString();

            $result = $processor->processFeedbackData($query);
            if ($result['status'] == 1) {
                $orderId = $result['orderId'];
                $transService = new TransactionService();
                $rs = $transService->approveRegistrationAfterWebPayment($orderId, OrderConstants::PAYMENT_ONEPAY);
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
            $query = $request->getQueryString();
            $url = $processor->processReturnData($query);
            if (!empty($url)) {
                return redirect($url);
            }
        } catch (\Exception $e) {
            echo $e;
        }
        return redirect(env('CALLBACK_SERVER'));
    }

    public function finExpenditures(Request $request)
    {
        $userService = new UserServices();
        $user = Auth::user();
        if (!$userService->isMod($user->role)) {
            return redirect()->back()->with('notify', __('Bạn không có quyền cho thao tác này'));
        }
        if ($request->get('action') == 'saveFinExpend') {
            $expend = $request->get('expend');
            $obj = [
                'user_id' => $user->id,
                'content' => $expend['title'],
                'type' => $expend['type'],
                'amount' => $expend['amount'],
                'ref_user_id' => $expend['ref_user_id'],
                'pay_method' => $expend['pay_method'],
                'pay_info' => $expend['comment'],
                'created_at' => $expend['date'],
                'status' => 1,
            ];
            if ($request->get('expendid') == "") {
                Transaction::create($obj);
            } else {
                Transaction::find($request->get('expendid'))->update($obj);
            }
            return redirect()->back()->with(['notify' => 1]);
        }
        $this->data['mods'] = User::whereIn('role', UserConstants::$modRoles)->get();

        $this->data['transaction'] = Transaction::whereIn('type', [
            ConfigConstants::TRANSACTION_FIN_ASSETS,
            ConfigConstants::TRANSACTION_FIN_FIXED_FEE,
            ConfigConstants::TRANSACTION_FIN_MARKETING,
            ConfigConstants::TRANSACTION_FIN_OTHERS,
            ConfigConstants::TRANSACTION_FIN_SALARY,
            ConfigConstants::TRANSACTION_FIN_VARIABLE_FEE,
        ])
            ->orderby('id', 'desc')
            ->with('refUser')
            ->paginate(20);
        $this->data['navText'] = __('Quản lý Chi tiền');
        return view('transaction.expenditures', $this->data);
    }

    public function finSaleReport(Request $request)
    {
        $from = $request->get('from') ? date('Y-m-d 00:00:00', strtotime($request->get('from'))) : date('Y-m-d 00:00:00', strtotime("-30 days"));
        $to = $request->get('to') ? date('Y-m-d 23:59:59', strtotime($request->get('to'))) : date('Y-m-d H:i:s');
        $partner = $request->get('partner');
        $transService = new TransactionService();
        $this->data['grossRevenue'] = $transService->grossRevenue($from, $to, $partner);
        $this->data['netRevenue'] = $transService->netRevenue($from, $to, $partner);
        $this->data['grossProfit'] = $transService->grossProfit($from, $to, $partner);
        $this->data['netProfit'] = $transService->netProfit($from, $to, $partner);
        $this->data['transaction'] = Transaction::where('transactions.created_at', '>', $from)
            ->where('transactions.created_at', '<', $to)
            ->where('transactions.status', '', ConfigConstants::TRANSACTION_STATUS_DONE)
            ->where('transactions.content', '!=', 'Thanh toán trực tuyến');
        if ($partner) {
            $this->data['transaction'] = $this->data['transaction']
                ->join('order_details', 'order_details.id', '=', 'transactions.order_id')
                ->join('items', 'items.id', '=', 'order_details.item_id')
                ->where('items.user_id', $partner);
        }
        $this->data['transaction'] = $this->data['transaction']
            ->select('transactions.*')
            ->orderby('transactions.id', 'desc')
            ->paginate();
        $this->data['partners'] = User::whereIn('role', [UserConstants::ROLE_SCHOOL, UserConstants::ROLE_TEACHER])
            ->orderby('first_name')
            ->get();
        $this->data['navText'] = __('Báo cáo doanh thu');
        return view('transaction.salereport', $this->data);
    }
}
