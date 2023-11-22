<?php namespace App\DigitalSupport;

class CodeGenerationHelper
{
    public static function getCode($key, $length = 16) 
    {
        $randomString = str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
        return $key ? $key . 'Al' . substr($randomString, 0, $length) : $key;
    }
}

