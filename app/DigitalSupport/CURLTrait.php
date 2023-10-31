<?php namespace App\DigitalSupport;

trait CURLTrait 
{
    private function post($url, $params, $metadata = []) 
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
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5); 

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    } 
}