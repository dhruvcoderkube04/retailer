<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDetails extends Model
{
    protected $fillable = [
        'firstname',
        'lastname',
        'phone_number',
        'email',
        'address',
        'state',
        'city',
        'pincode'
    ];
}
