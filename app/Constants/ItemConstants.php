<?php

namespace App\Constants;

class ItemConstants
{
    const TYPE_COURSE = 'course';
    const TYPE_CLASS = 'class';
    const TYPE_PRODUCT = 'product';

    const COURSE_SYSTEM_USERID  = 1;
    const NEW_COURSE_SERIES = -1;

    public static $locationTypes = [
        'online' => 'Online',
        'offline' => 'Offline',
    ];
}