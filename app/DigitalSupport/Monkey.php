<?php namespace App\DigitalSupport;

use App\DataObjects\ServiceResponse;

class Monkey
{
    use CURLTrait;
    use CodeGenerationTrait;

    const FAIL = 'fail';

    private $orderData;

    private function validateOrderData($productID, $promotionID, $transactionID)
    {
        if (empty($productID) || empty($promotionID) || empty($transactionID)) {
            return 'INVALID_ORDER_DATA';
        }

        $this->orderData = [
            'product_id' => $productID, 
            'promotion_id' => $promotionID,
            'transaction_id' => $transactionID,
        ];

        return true;
    }

    private function createOrderRequest()  
    {
        $domain = env('MONKEY_DOMAIN', 'https://monkey.edu.vn') . '/api/create-order-agent';
        $apiKey = env('MONKEY_API_KEY', 'UNKNOWN'); 
        $agentCode = env('MONKEY_AGENT_CODE', 'UNKNOWN');

        $header = ["api-key: $apiKey", "agent-code: $agentCode"];
        return $this->post($domain, json_encode($this->orderData), $header);
    }

    private function processReturnData($returnData) 
    {
        if (!isset($returnData['status']) || $returnData['status'] == self::FAIL) {
            return new ServiceResponse(false, $returnData['message'] ?? 'INVALID_RESPONSE'); 
        }
        return new ServiceResponse(true, $returnData['message'], $returnData['data']);
    }

    public function processOrderRequest($productID, $promotionID, $transactionID) 
    {
        $validation = $this->validateOrderData($productID, $promotionID, $transactionID);
        if (true !== $validation) {
            return new ServiceResponse(false, $validation);
        }    

        $result = json_decode($this->createOrderRequest(), true);
        return $this->processReturnData($result);
    }

}