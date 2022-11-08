<?php

namespace App\Constants;

class UserConstants
{
    const ROLE_ADMIN = 'admin';
    const ROLE_MOD = 'mod';
    const ROLE_CONTENT = 'content';
    const ROLE_SALE = 'sale';
    const ROLE_FIN = 'fin';
    const ROLE_MEMBER = 'member';
    const ROLE_TEACHER = 'teacher';
    const ROLE_SCHOOL = 'school';
    const ROLE_SALE_CONTENT = 'sale_content';
    const ROLE_FIN_PARTNER = 'fin_partner';


    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const WALLET_M = 'wallet_m';
    const WALLET_C = 'wallet_c';
    const VOUCHER = 'voucher';

    const PP = 20;

    public static $memberRoles = [self::ROLE_MEMBER, self::ROLE_TEACHER, self::ROLE_SCHOOL];
    public static $saleRoles = [self::ROLE_SALE, self::ROLE_SALE_CONTENT];
    public static $modRoles = [self::ROLE_FIN_PARTNER, self::ROLE_MOD, self::ROLE_ADMIN, self::ROLE_SALE, self::ROLE_CONTENT, self::ROLE_FIN, self::ROLE_SALE_CONTENT];

    public static $statusText = [
        self::STATUS_ACTIVE => 'Hoạt động',
        self::STATUS_INACTIVE => 'Đã khóa'
    ];

    const CONTRACT_DELETED = 0;
    const CONTRACT_NEW = 1;
    const CONTRACT_SIGNED = 10;
    const CONTRACT_APPROVED = 99;

}
