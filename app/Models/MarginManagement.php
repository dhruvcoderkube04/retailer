<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarginManagement extends Model
{
    protected $fillable = [
        'margin_name',
        'status',
        'flat_percentage',
        'type',
        'default'
    ];
}
