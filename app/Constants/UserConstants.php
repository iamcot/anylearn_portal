<?php

namespace App\Constants;

class UserConstants
{
    const ROLE_ADMIN = 'admin';
    const ROLE_MOD = 'mod';
    const ROLE_MEMBER = 'member';
    const ROLE_TEACHER = 'teacher';
    const ROLE_SCHOOL = 'school';

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const WALLET_M = 'wallet_m';
    const WALLET_C = 'wallet_c';

    const PP = 20;

    public static $memberRoles = [self::ROLE_MEMBER, self::ROLE_TEACHER, self::ROLE_SCHOOL];
    public static $modRoles = [self::ROLE_MOD, self::ROLE_ADMIN];

    public static $statusText = [
        self::STATUS_ACTIVE => 'Hoạt động',
        self::STATUS_INACTIVE => 'Đã khóa'
    ];

    const CONTRACT_DELETED = 0;
    const CONTRACT_NEW = 1;
    const CONTRACT_SIGNED = 10;
    const CONTRACT_APPROVED = 99;
}
