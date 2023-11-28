<?php namespace App\DigitalSupport;

interface DigitalPartnerInterface 
{
    public function createOrderRequest($orderData);

    public function processReturnData($returnData);

    public function validateOrderData($productID, $promotionID, $transactionID);

    public function createOrderFromAgent($productID, $promotionID, $transactionID);
}