<?php namespace App\PaymentGateway;

class Processor {
    static $processor = null;

    public static function getProcessor($payment) {
        if (null != self::$processor) {
            return self::$processor;
        }
        switch($payment) {
            case Momo::NAME:
                self::$processor = new Momo();
                break;
            case OnepayLocal::NAME:
                self::$processor =  new OnepayLocal();
                break;
            default:
                break;
        } 
        return self::$processor;
    }
    
}