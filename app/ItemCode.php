<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemCode extends Model
{
    protected $table = 'item_codes';
    protected $fillable = ['code', 'item_id', 'user_id', 'order_detail_id'];
    
}
