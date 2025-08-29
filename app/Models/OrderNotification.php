<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderNotification extends Model
{
    protected $table = 'notifications';
    protected $fillable = [
        'user_id',
        'order_id',
        'type',
        'message',
        'is_read',
    ];
}
