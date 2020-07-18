<?php

namespace App\Constants;

class NotifConstants
{
    /** User notif */
    const NEW_USER = 'new_user';
    const NEW_FRIEND = 'new_friend';

    /** Course process */
    const COURSE_APPROVED = 'course_approved';
    const COURSE_REJECTED = 'course_rejected';
    const COURSE_REGISTERED = 'course_registered';
    const COURSE_JOINED = 'course_joined';
    const COURSE_HAS_REGISTERED = 'course_has_registered';
    const COURSE_HAS_NEW = 'course_has_new';
    const COURSE_HAS_CHANGED = 'course_has_changed';

    /** Transaction */
    const TRANS_DEPOSIT_SENT = 'trans_deposit_sent';
    const TRANS_DEPOSIT_APPROVED = 'trans_deposit_approved';
    const TRANS_DEPOSIT_REJECTED = 'trans_deposit_rejected';
    const TRANS_WITHRAW_APPROVED = 'trans_withdraw_approved';
    const TRANS_EXCHANGE_APPROVED = 'trans_exchange_approved';
    const TRANS_WITHRAW_REJECTED = 'trans_withdraw_rejected';
    const TRANS_DEPOSIT_REFUND = 'trans_deposit_return';
    const TRANS_COMMISSION_RECEIVED = 'trans_commission_received';
    const TRANS_FOUNDATION = 'trans_foundation';

    /** Reminder */
    const REMIND_CONFIRM = 'remind_confirm';
    const REMIND_COURSE_JOIN = 'remind_course_join';
    const REMIND_COURSE_GOING_JOIN = 'remind_course_going_join';
    const REMIND_COURSE_GOING = 'remind_course_going';
}
