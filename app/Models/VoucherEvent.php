<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherEvent extends Model
{
    const TYPE_CLASS = 'class';
    const TYPE_REGISTER = 'register';
    const TYPE_PROMOTE = 'promote';

    protected $table = 'voucher_events';

    protected $fillable = [
        'type', 'status', 'title', 'trigger', 'targets', 'qtt', 'notif_template', 'email_template'
    ];
}
