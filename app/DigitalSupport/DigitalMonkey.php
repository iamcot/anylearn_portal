<?php namespace App\DigitalSupport;

use App\DataObjects\ServiceResponse;

class DigitalMonkey implements DigitalPartnerInterface
{
    use CURLTrait;

    const FAIL = 'fail';

    public function createOrderRequest($orderData) 
    {
        $domain = env('MONKEY_DOMAIN', 'https://monkey.edu.vn') . '/api/create-order-agent';
        $apiKey = env('MONKEY_API_KEY', 'UNKNOWN'); 
        $agentCode = env('MONKEY_AGENT_CODE', 'UNKNOWN');

        $header = ["api-key: $apiKey", "agent-code: $agentCode"];
        return $this->post($domain, json_encode($orderData), $header);
    }

    public function processReturnData($returnData) 
    {
        // if (!isset($returnData['status']) || $returnData['status'] == self::FAIL) {
        //     return new ServiceResponse(false, $returnData['message'] ?? 'INVALID_RESPONSE'); 
        // }
        $returnData = [
            'message' => 'Tao don thanh cong!',
            'data' => [
                'account' => 'acctest',
                'password' => 'acc123',
                'order_id' => 'order1',
            ],
        ];
        return new ServiceResponse(true, $returnData['message'], $returnData['data']);
    }

    public function validateOrderData($productID, $promotionID, $transactionID)
    {
        if (empty($productID) || empty($promotionID) || empty($transactionID)) {
            return false;
        }

        return [
            'product_id' => $productID, 
            'promotion_id' => $promotionID,
            'transaction_id' => $transactionID,
        ];
    }

    public function createOrderFromAgent($productID, $promotionID, $transactionID) 
    {
        $orderData = $this->validateOrderData($productID, $promotionID, $transactionID);
        if (false === $orderData) {
            return new ServiceResponse(false, 'INVALID_ORDER_DATA');
        }
        $returnData = json_decode($this->createOrderRequest($orderData), true);
        return $this->processReturnData($returnData);
    }

}