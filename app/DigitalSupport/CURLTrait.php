<?php namespace App\DigitalSupport;

trait CURLTrait 
{
    private function post($url, $params, $metadata = []) 
    {
        $header = [
            'Content-Length: ' . strlen($params),
            'Content-Type: application/json',
        ];
        
        $ch = curl_init($url);
        $header = !empty($metadata) ? array_merge($header, $metadata) : $header;

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); 

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    } 
}