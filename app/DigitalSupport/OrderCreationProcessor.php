<?php namespace App\DigitalSupport;

use Exception;

class OrderCreationProcessor 
{
    private static $processor = null;
    
    public static function getInstance($partnerID)
    {
        if (null != self::$processor ) {
            return self::$processor;
        }

        $digitalPartnerAPIs = config('digital_partner_apis');
        if (!array_key_exists($partnerID, $digitalPartnerAPIs) ||
            !class_exists($digitalPartnerAPIs[$partnerID])
        ) { 
            throw new Exception('The partner does not support API');
        }

        $instance = new $digitalPartnerAPIs[$partnerID];
        if (!$instance instanceof DigitalPartnerInterface) {
            throw new Exception('The partner must implement DigitalPartnerInterface');
        }

        self::$processor = $instance;
        return self::$processor;   
    }
    
}