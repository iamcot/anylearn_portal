<?php namespace App\DigitalSupport;

class OrderCreationProcessor 
{
    static $processor = null;
    
    public static function getProcessor($partner)
    {
        if (null != self::$processor ) {
            return self::$processor;
        }
        
        if (class_exists($partner)) {
           self::$processor = new $partner; 
        }

        return self::$processor;    
    }
    
}