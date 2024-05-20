<?php

namespace App\PaymentGateway;

use App\DataObjects\ServiceResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class Vnpay implements PaymentInterface
{
    const NAME = 'vnpay';
    const SUCCESS_CODE = "00";
    private $amount;
    private $orderId;
    private $ip;
    private $saveToken;
    private $existsToken;
    private $existsTokenExp;
    private $userId;

    public function validate($input)
    {
        $amount = isset($input['amount']) ? $input['amount'] : 0;
        $orderId = isset($input['orderid']) ? $input['orderid'] : "";
        $ip = isset($input['ip']) ? $input['ip'] : "";
        if (empty($orderId) && !App::environment('production')) {
            $orderId = time();
        }
        if ($amount <= 0 || empty($orderId) || empty($ip)) {
            return 'REQUIRE_AMOUNT_OR_ORDERID_IP';
        }

        $this->amount = $amount * 100; //special rule of vnpay
        $this->orderId = $orderId;
        $this->ip = $ip;

        $this->saveToken = isset($input['save_card']) ? $input['save_card'] : false;
        $this->existsToken = isset($input['token_num']) ? $input['token_num'] : false;
        $this->existsTokenExp = isset($input['token_exp']) ? $input['token_exp'] : false;

        if ($this->saveToken || $this->existsToken) {
            $this->userId = isset($input['user_id']) ? $input['user_id'] : false;
            if ($this->userId === false) {
                return 'REQUIRED_USER_INFO_TO_USE_CARD';
            }
        }
        return true;
    }

    public function processPayment()
    {
        if (empty($this->amount) || empty($this->orderId) || empty($this->ip)) {
            $response = new ServiceResponse(false, 'NOT_VALID_INPUT');
        }
        $response = $this->getPaymentPage();
        if (!$response instanceof ServiceResponse) {
            $response = new ServiceResponse(false, 'NOT_VALID_RESPONSE');
            return $response;
        }

        if (empty($response->data)) {
            $response->status = false;
            $response->errorCode = 'NOT_VALID_DATA';
        }

        return $response;
    }

    public function processReturnData($str)
    {

        $data = $this->processFeedbackData($str);

        return $this->buildUrl($data);
    }

    public function prepareNotifyResponse($response, $feedbackResult)
    {
        $responseCode = $feedbackResult['status'] ? 1 : 0;
        $data = "responsecode=$responseCode&desc=confirm-success";
        return $data;
    }

    public function processFeedbackData($str)
    {
        $response = [];
        foreach (explode('&', $str) as $couple) {
            list($key, $val) = explode('=', $couple);
            $response[$key] = $val;
        }
        $data = [
            'status' => 0,
            'message' => '',
        ];

        if (empty($response)) {
            $data['message'] = 'NO_RESPONE';
            return $data;
        }

        $data = array_merge($data, [
            'orderId' => isset($response['vnp_OrderInfo']) ? $response['vnp_OrderInfo'] : '',
            'amount' => isset($response['vnp_Amount']) ? $response['vnp_Amount'] : '',
            'transId' => isset($response['vnp_TxnRef']) ? $response['vnp_TxnRef'] : '',
            'payType' => 'web',
            'payment' => self::NAME,
        ]);

        // if (isset($response['vpc_TokenNum'])) {
        //     $data['newTokenNum'] = $response['vpc_TokenNum'];
        //     $data['newTokenExp'] = isset($response['vpc_TokenExp']) ? $response['vpc_TokenExp'] : '';
        //     $data['newCardType'] = isset($response['vpc_Card']) ? $response['vpc_Card'] : '';
        //     $data['newCardUid'] = isset($response['vpc_CardUid']) ? $response['vpc_CardUid'] : '';
        // }

        if (!$this->checkHash($response)) {
            $data['message'] = 'INVALID_HASH';
            return $data;
        }

        if (isset($response['vnp_TransactionStatus']) && $response['vnp_TransactionStatus'] == self::SUCCESS_CODE) {
            $data['status'] = 1;
        } else {
            $data['message'] = $this->getTransactionStatus($response['vnp_TransactionStatus']);
        }
        return $data;
    }

    private function buildUrl($data)
    {
        $flatdata = [];
        foreach ($data as $key => $value) {
            $flatdata[] = urlencode($key) . '=' . urlencode($value);
        }
        return env('CALLBACK_SERVER_VNPAY') . '?' . implode("&", $flatdata);
    }

    public function checkHash($input)
    {
        if (
            strlen(env('PAYMENT_VNPAY_SECRET')) > 0
            && $input["vnp_TransactionStatus"] != "07" // nghi ngo gian lan
            && $input["vnp_TransactionStatus"] != "No Value Returned"
        ) {
            $hash = isset($input['vnp_SecureHash']) ? $input['vnp_SecureHash'] : '';
            ksort($input);
            $stringHashData = "";

            foreach ($input as $key => $value) {
                if ($key != "vnp_SecureHash" && (strlen($value) > 0) && ((substr($key, 0, 4) == "vpc_") || (substr($key, 0, 5) == "user_"))) {
                    $stringHashData .= $key . "=" . $value . "&";
                }
            }
            $stringHashData = rtrim($stringHashData, "&");
            $stringHashData = urldecode($stringHashData);
            Log::debug($stringHashData);
            $checkedHash = strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*', env('PAYMENT_VNPAY_SECRET'))));
            if (hash_equals($hash, $checkedHash)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get payment page of VNPAY
     *
     * @return ServiceResponse $response
     */
    private function getPaymentPage()
    {
        try {
            $result = $this->createPaymentRequest();
            $signature = $result['signature'];
            $url = $result['result'];
            return new ServiceResponse(true, 0, $url);
        } catch (\Exception $e) {
            return new ServiceResponse(false, 'EXCEPTION', $e);
        }
        return new ServiceResponse(false, "NOT_VALID_RESPONSE");
    }

    /**
     * create payment request query
     * 
     * @return array()
     */
    private function createPaymentRequest()
    {
        $returnUrl = env('APP_URL') . '/payment-return/vnpay';
        $notifyurl = env('APP_URL') . '/payment-notify/vnpay';
        $data =  [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => env('PAYMENT_VNPAY_ACCESSCODE'),
            'vnp_Amount' => strval($this->amount),
            // 'vnp_BankCode' => '',
            'vnp_CreateDate' => date('yyyyMMddHHmmss'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $this->ip,
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => strval($this->orderId),
            'vnp_OrderType' => '',
            'vnp_ReturnUrl' => $returnUrl,
            'vnp_ExpireDate' => date('yyyyMMddHHmmss', strtotime("+30 minutes")),
            'vnp_TxnRef' => $this->orderId . time() . '',
        ];
        // if ($this->existsToken) {
        //     $data['vpc_TokenNum'] = $this->existsToken;
        //     $data['vpc_TokenExp'] = $this->existsTokenExp;
        //     $data['vpc_Customer_Id'] = $this->userId;
        // } else if ($this->saveToken) {
        //     $data['vpc_CreateToken'] = 'true';
        //     $data['vpc_Customer_Id'] = $this->userId;
        // }

        $flatdata = [];
        $hashRawData = [];
        ksort($data);
        foreach ($data as $key => $value) {
            $flatdata[] = urlencode($key) . '=' . urlencode($value);
            if ((strlen($value) > 0) && ((substr($key, 0, 4) == "vnp_") || (substr($key, 0, 5) == "user_"))) {
                $hashRawData[] = $key . "=" . $value;
            }
        }
        $query = implode("&", $flatdata);
        $hashRaw = implode("&", $hashRawData);

        $signature =  strtoupper(hash_hmac('sha256', $hashRaw, pack('H*', env('PAYMENT_VNPAY_SECRET'))));

        $query = $this->getServer() . '?' . $query . '&vnp_SecureHash=' . $signature;
        return [
            'result' => $query,
            'signature' => $signature,
        ];
    }

    private function getServer()
    {
        return env('PAYMENT_VNPAY_SERVER', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
    }

    private function getTransactionStatus($responseCode)
    {

        switch ($responseCode) {
            case "00":
                $result = "Giao dịch thành công - Successful Transaction";
                break;
            case "01":
                $result = "Giao dịch chưa hoàn tất - Transaction Pending";
                break;
            case "02":
                $result = "Giao dịch bị lỗi - Transaction failed";
                break;
            case "04":
                $result = "Khách hàng đã bị trừ tiền tại Ngân hàng nhưng GD chưa thành công ở VNPAY";
                break;
            case "05":
                $result = "VNPAY đang xử lí";
                break;
            case "06":
                $result = "VNPAY đã gửi yêu cầu sang ngân hàng";
                break;
            case "07":
                $result = "Giao dịch bị nghi ngờ gian lận";
                break;
            case "09":
                $result = "Giao dịch hoàn bị từ chối";
                break;
        }
        return $result;
    }

    private function getResponseStatus($responseCode)
    {

        switch ($responseCode) {
            case "00":
                $result = "Giao dịch thành công - Successful Transaction";
                break;
            case "24":
                $result = "Giao dịch không thành công - Khách huỷ.";
                break;
            case "99":
                $result = "Giao dịch không thành công - Lỗi khác.";
                break;
            default:
                $result = "Giao dịch chưa hoàn tất";
                break;
        }
        return $result;
    }
}
