<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RTOAddress extends Model
{
    use HasFactory;

    protected $table = 'rto_addresses';

    protected $fillable = [
        'first_name', 'last_name', 'mobile_number', 'pincode', 'address', 'state', 'city', 'retailer_id', 
    ];

    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id')->where('user_type', 3);
    }
}
