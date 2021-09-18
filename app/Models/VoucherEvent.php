<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherEvent extends Model
{
    const TYPE_CLASS = 'class';
    const TYPE_REGISTER = 'register';

    protected $table = 'voucher_events';

    protected $fillable = [
        'type', 'status', 'title', 'trigger', 'targets', 'qtt', 'notif_template', 'email_template'
    ];
}
