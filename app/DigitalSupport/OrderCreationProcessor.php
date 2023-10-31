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

    public static function post($url, $params, $metadata = []) 
    {
        $header = [
            'Content-Length: ' . strlen($params),
            'Content-Type: application/json',
        ];
        
        $curl = curl_init($url);
        $header = !empty($metadata) ? array_merge($header, $metadata) : $header;

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);      
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5); 

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    } 
}