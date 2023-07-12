<?php namespace App\PaymentGateway;

use App\DataObjects\ServiceResponse;
use Illuminate\Support\Facades\App;

class Momo implements PaymentInterface {
    const NAME = 'momo';
    const SUCCESS_CODE = "0";

    private $amount;
    private $orderId;

    public function validate($input) {
        $amount = isset($input['amount']) ? $input['amount'] : 0;
        $orderId = isset($input['orderid']) ? $input['orderid'] : "";
        if (empty($orderId) && !App::environment('live')) {
            $orderId = time();
        }
        if ($amount < 1000 || empty($orderId)) {
            return 'REQUIRE_AMOUNT_OR_ORDERID';
        }

        $this->amount = $amount;
        $this->orderId = $orderId;
        return true;
    }

    /**
     * 
     * @return ServiceResponse $response
     */
    public function processPayment() {
        if (empty($this->amount) || empty($this->orderId)) {
            $response = new ServiceResponse(false, 'NOT_VALID_INPUT');
        }
        $response = $this->getPaymentPage($this->amount, $this->orderId);
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

    /**
     * 
     * @return string
     */
    public function processReturnData($response) {
        parse_str($response, $arrResponse);
        $data = $this->processFeedbackData($arrResponse);

        return $this->buildUrl($data);
    }

    public function processFeedbackData($response) {
        $data = [
            'status' => 0,
            'message' => '',
        ];

        if (empty($response)) {
            $data['message'] = 'NO_RESPONE';
            return $data;
        }

        $data = array_merge($data,[
            'orderId' => isset($response['orderId']) ? $response['orderId'] : '',
            'amount' => isset($response['amount']) ? $response['amount'] : '',
            'transId' => isset($response['transId']) ? $response['transId'] : '',
            'payType' => isset($response['payType']) ? $response['payType'] : '',
            'payment' => self::NAME,
        ]);

        if (isset($response['errorCode']) && $response['errorCode'] == self::SUCCESS_CODE) {
            $data['status'] = 1;
        } else {
            //$data['message'] = isset($response['message']) ? $response['message'] : '';
            $data['message'] = 'Thanh toán không thành công!';

        }

        return $data;
    }

    public function prepareNotifyResponse($response, $feedbackResult) {
        $data = [
            'status' => self::SUCCESS_CODE,
            'message' => 'Order confirmed',
            'data' => [
                'billId' => isset($response['orderId']) ? $response['orderId'] : '',
                'momoTransId' => isset($response['transId']) ? $response['transId'] : '',
                'amount' => isset($response['amount']) ? $response['amount'] : '',
            ],
        ];
        $signRaw = "status=0&message=Order confirmed&amount=" . $data['data']['amount'] . "&billId=" 
        . $data['data']['billId'] . "&momoTransId=" . $data['data']['momoTransId'];
        $data['signature'] =  hash_hmac('sha256', $signRaw, env('PAYMENT_MOMO_SECRET'));
        return $data;
    }

    private function buildUrl($data) {
        $flatdata = [];
        foreach($data as $key => $value) {
            $flatdata[] = urlencode($key) . '=' . urlencode($value);
         }
        return env('CALLBACK_SERVER') . '/momo?' . implode("&", $flatdata);
    }

     /**
      * Get QR page of Momo
      *
      * @return ServiceResponse $response
      */
    private function getPaymentPage($amount, $orderid, $extras = []) {
        try {
            $result = $this->createPaymentRequest($amount, $orderid);
            $signature = $result['signature'];
            $data = json_decode($result['result'],true);
            if (!isset($data['errorCode'])) {
                return new ServiceResponse(false, "NOT_VALID_RESPONSE");
            }
            if ($data['errorCode'] == 0 && isset($data['payUrl'])) {
                return new ServiceResponse(true, 0, $data['payUrl']);
            } else {
                return new ServiceResponse(false, $data['errorCode'], $data);
            }
        } catch (\Exception $e) {
            return new ServiceResponse(false, 'EXCEPTION', $e);
        }
        return new ServiceResponse(false, "NOT_VALID_RESPONSE");
    }

    /**
     * create payment request send to momo
     * 
     * @return array()
     */
    private function createPaymentRequest($amount, $orderid) {
        $domain = $this->getServer() . '/gw_payment/transactionProcessor';
        $partnerCode = env('PAYMENT_MOMO_PARTNER', 'MOMO9JIR20230710');
        $accessKey = env('PAYMENT_MOMO_ACCESS', 'LlFWon5lIJZE5YgW');
        $serectkey = env('PAYMENT_MOMO_SECRET', 'SJPsTqt63TwgVAEQHkQQ8w52Wg3AnSLQ');
        $orderInfo = 'Vui lòng thanh toán đơn hàng của bạn';
        $returnUrl = env('APP_URL') . '/payment-return/momo'; //'/api/payment/return/momo';
        $notifyurl = env('APP_URL') . '/api/payment/notify/momo';
        $requestId = time()."";
        $requestType = "captureMoMoWallet";
        $extraData = '';

        $signRaw = "partnerCode=".$partnerCode."&accessKey=".$accessKey
        ."&requestId=".$requestId."&amount=".$amount."&orderId=".$orderid
        ."&orderInfo=".$orderInfo."&returnUrl=".$returnUrl."&notifyUrl="
        .$notifyurl."&extraData=".$extraData;

        $signature =  hash_hmac('sha256', $signRaw, env('PAYMENT_MOMO_SECRET', 'SJPsTqt63TwgVAEQHkQQ8w52Wg3AnSLQ'));

        $data =  [
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'amount' => strval($amount),
            'orderId' => strval($orderid),
            'orderInfo' => $orderInfo,
            'returnUrl' => $returnUrl,
            'notifyUrl' => $notifyurl,
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        $result = CurlHelper::Post($domain, json_encode($data));
    
        return [
            'result' => $result,
            'signature' => $signature,
        ];
    }

    private function getServer() {
        if (App::environment('prod')) {
            return env('PAYMENT_MOMO_SERVER', '');
        } else return 'https://test-payment.momo.vn';
    }
}

    /**
     * Momo test user: 0961800390
     * Momo test pass & otp: 000000
     */
