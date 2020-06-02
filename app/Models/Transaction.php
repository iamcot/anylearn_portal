<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'type', 'amount', 'pay_method', 'pay_info', 'order_id', 'content', 'status',
    ];
}
