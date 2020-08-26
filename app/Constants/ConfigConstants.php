<?php

namespace App\Constants;

class ConfigConstants
{
    const GUIDE_TOC = 'guide_toc';
    const GUIDE_MEMBER  = 'guide_member';
    const GUIDE_TEACHER = 'guide_teacher';
    const GUIDE_SCHOOL = 'guide_school';
    const GUIDE_ABOUT = 'guide_about';
    const GUIDE_TOC_TEACHER = 'guide_toc_teacher';
    const GUIDE_TOC_SCHOOL = 'guide_toc_school';
    const GUIDE_PRIVACY = 'guide_privacy';
    const CONTRACT_TEACHER = 'contract_teacher';
    const CONTRACT_SCHOOL = 'contract_school';

    const CONFIG_FRIEND_TREE = 'friend_tree';
    const CONFIG_NUM_COURSE = 'num_course';
    const CONFIG_NUM_TEACHER = 'num_teacher';
    const CONFIG_NUM_SCHOOL = 'num_school';
    const CONFIG_FEE_TEACHER = 'fee_teacher';
    const CONFIG_FEE_SCHOOL = 'fee_school';
    const CONFIG_FEE_MEMBER = 'fee_member';
    const CONFIG_COMMISSION = 'commission';
    const CONFIG_DISCOUNT = 'discount';
    const CONFIG_BONUS_RATE = 'bonus_rate';
    const CONFIG_NUM_CONFIRM_GOT_BONUS = 'num_confirm';
    const CONFIG_COMMISSION_FOUNDATION = 'bonus_foundation';
    const CONFIG_COMMISSION_COMPANY = 'bonus_company';
    const CONFIG_COMMISSION_AUTHOR = 'bonus_author';
    const CONFIG_TEACHER_BANNER = 'teacher_banner';
    const CONFIG_SCHOOL_BANNER = 'school_banner';

    const CONFIG_HOME_POPUP = 'home_popup';

    const TYPE_GUIDE = 'guide';
    const TYPE_CONFIG = 'config';

    const TRANSACTION_DEPOSIT = 'deposit';
    const TRANSACTION_WITHDRAW = 'withdraw';
    const TRANSACTION_EXCHANGE = 'exchange';
    const TRANSACTION_COMMISSION = 'commission';
    const TRANSACTION_ORDER = 'order';
    const TRANSACTION_FOUNDATION = 'foundation';
    const TRANSACTION_DEPOSIT_REFUND = 'deposit_refund';
    const TRANSACTION_COMMISSION_ADD = 'commission_add';

    const TRANSACTION_STATUS_PENDING = 0;
    const TRANSACTION_STATUS_DONE = 1;
    const TRANSACTION_STATUS_REJECT = 99;


    public static $guideTitle = [
        self::GUIDE_TOC => 'TOC - Điều khoản',
        self::GUIDE_MEMBER => 'HDSD cho Thành viên',
        self::GUIDE_TEACHER => 'HDSD cho Giảng viên',
        self::GUIDE_SCHOOL => 'HDSD cho Trung tâm',
        self::GUIDE_ABOUT => 'Giới thiệu',
        self::GUIDE_TOC_SCHOOL => 'Chính sách cho trung tâm',
        self::GUIDE_TOC_TEACHER => 'Chính sách cho giảng viên',
        self::GUIDE_PRIVACY => 'Chính sách bảo mật',
        self::CONTRACT_TEACHER => 'Mẫu HĐ Giảng Viên',
        self::CONTRACT_SCHOOL => 'Mẫu HĐ Trường Học',
    ];
}
