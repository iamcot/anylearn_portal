<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'quantity', 'amount', 'status', 'delivery_name', 'delivery_address', 'delivery_phone',
        'payment', 'sale_id'
    ];
}
