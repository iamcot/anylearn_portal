<?php namespace App\DigitalSupport;

class CodeGenerator 
{ 
    // private static $stopKey = 'ALT';
    // private static $originKey = 'ANYLEARN';
    private static $usedChars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';


    public static function generateRandomCode($length = 8)
    {
        return substr(str_shuffle(static::$usedChars), 0, $length); 
    }

    // public static function generateTransactionCode($key, $length = 16) 
    // {  
    //     $code = static::$originKey . $key . $stopKey;
    //     return base64_encode($code . static::generateRandomCode($length));
    // }

    // public static function getKeyFromGeneratedCode($code) {
    //     $code = base64_decode($code);
    //     return substr(stristr($code, $stopKey, true), strlen(static::$originKey));
    // }
}
