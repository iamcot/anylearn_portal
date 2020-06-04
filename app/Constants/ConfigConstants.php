<?php

namespace App\Constants;

class ConfigConstants
{
    const GUIDE_TOC = 'guide_toc';
    const GUIDE_MEMBER  = 'guide_member';
    const GUIDE_TEACHER = 'guide_teacher';
    const GUIDE_SCHOOL = 'guide_school';

    const CONFIG_FRIEND_TREE = 'friend_tree';
    const CONFIG_NUM_COURSE = 'num_course';
    const CONFIG_NUM_TEACHER = 'num_teacher';
    const CONFIG_NUM_SCHOOL = 'num_school';
    const CONFIG_FEE_TEACHER = 'fee_teacher';
    const CONFIG_FEE_SCHOOL = 'fee_school';
    const CONFIG_COMMISSION = 'commission';
    const CONFIG_DISCOUNT = 'discount';
    const CONFIG_BONUS_RATE = 'bonus_rate';
    const CONFIG_COMMISSION_FOUNDATION = 'foundation';
    const CONFIG_COMMISSION_COMPANY = 'company';
    const CONFIG_TEACHER_BANNER = 'teacher_banner';
    const CONFIG_SCHOOL_BANNER = 'school_banner';

    const TYPE_GUIDE = 'guide';
    const TYPE_CONFIG = 'config';

    const TRANSACTION_DEPOSIT = 'deposit';
    const TRANSACTION_WITHDRAW = 'withdraw';
    const TRANSACTION_EXCHANGE = 'exchange';
    const TRANSACTION_COMMISSION = 'commission';
    const TRANSACTION_ORDER = 'order';

    const TRANSACTION_STATUS_PENDING = 0;
    const TRANSACTION_STATUS_DONE = 1;
    const TRANSACTION_STATUS_REJECT = 99;


    public static $guideTitle = [
        self::GUIDE_TOC => 'TOC - Điều khoản',
        self::GUIDE_MEMBER => 'HDSD cho Thành viên',
        self::GUIDE_TEACHER => 'HDSD cho Giảng viên',
        self::GUIDE_SCHOOL => 'HDSD cho Trung tâm',
    ];
}
