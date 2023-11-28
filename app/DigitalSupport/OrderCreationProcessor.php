<?php namespace App\DigitalSupport;

use Exception;

class OrderCreationProcessor
{
    private static $instance = null;

    private static function createProcessor($partnerID) 
    {
        $digitalPartners = config('activation_apis'); 
        foreach ($digitalPartners as $dp) {
            if ($partnerID == $dp['partnerID'] && class_exists($dp['processor'])) {
                $processor = new $dp['processor'];
                if ($processor instanceof DigitalPartnerInterface) {
                    return $processor;
                }
                throw new Exception('The processor must implement DigitalPartnerInterface');   
            }
        }
        return null;
    }
    
    public static function getInstance($partnerID)
    {
        if (null !== self::$instance) {
            return self::$instance;
        }
        self::$instance = self::createProcessor($partnerID);
        if (null === self::$instance) { 
            throw new Exception('There are no processors for partnerID: ' . $partnerID);
        }
        return self::$instance;  
    }

    public static function createOrderFromAgent($orderData, $partnerID = 0) 
    {
        return self::getInstance($partnerID)->createOrderFromAgent($orderData); 
    }

}