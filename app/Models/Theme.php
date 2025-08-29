<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = [
        'theme_name', 'theme_image', 'theme_type', 'status'
    ];
}
