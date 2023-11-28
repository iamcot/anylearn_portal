<?php 

/**
 * List of digital partners that support the API
 * Config the partnerID with the corresponding processor 
 */

use App\DigitalSupport\DigitalMonkey;

return [
    'monkey' => [
        'partnerID' => 222,
        'processor' => DigitalMonkey::class,
    ], 
];