<?php 

/**
 * List of digital partners that support the API
 * Config the partnerID with the corresponding class 
 */

use App\DigitalSupport\DigitalMonkey;

return [
    'monkey' => [
        'partnerID' => 222,
        'processor' => DigitalMonkey::class,
    ], 
];