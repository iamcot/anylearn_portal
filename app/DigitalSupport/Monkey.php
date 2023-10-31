<?php namespace App\DigitalSupport;

use App\DataObjects\ServiceResponse;

class Monkey
{
    use CURLTrait;
    use CodeGenerationTrait;

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
        $cookie = 'website_beta_session=QB0yRNevMET7QlGPMyzOCjYnE0Kl1FhKN5xsEExo;'; 
        $domain = env('MONKEY_DOMAIN', 'https://monkey.edu.vn') . '/api/create-order-agent';

        $apiKey = env('MONKEY_API_KEY', 'UNKNOWN');
        $agentCode = env('MONKEY_AGENT_CODE', 'UNKNOWN');

        $header = ["api-key: $apiKey", "agent-code: $agentCode", "cookie: $cookie"];
        return $this->post($domain, json_encode($this->orderData), $header);
    }

    private function processReturnData($response) 
    {
        
        return $response->message;
    }

    public function processOrderRequest($productID, $promotionID, $transactionID) 
    {
        $response   = new ServiceResponse(false, []); 
        $validation = $this->validateOrderData($productID, $promotionID, $transactionID);

        if (true !== $validation) {
            $response->errorCode['message'] = $validation;
            return $response;
        }    

        $result = json_decode($this->createOrderRequest(), true);
        return $this->processReturnData($result);
    }

}