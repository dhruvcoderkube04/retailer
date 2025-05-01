<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PickAddress extends Model
{
    use HasFactory;

    protected $table = 'pickup_addresses';

    protected $fillable = [
        'first_name', 'last_name', 'mobile_number','courier_partner_id','pincode', 'address', 'state', 'city', 'retailer_id',
    ];



    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id')->where('user_type', 3);
    }
}
