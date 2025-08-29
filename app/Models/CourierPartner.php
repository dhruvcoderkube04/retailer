<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourierPartner extends Model
{
    protected $fillable = ['name','code','url','api_key','is_active'];
}
