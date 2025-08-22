<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailerWebsiteEnquiry extends Model
{

    protected $table = 'retailer_website_enquiry';

    protected $fillable = [
        'retailer_id',
        'firstname',
        'lastname',
        'phone_number',
        'email',
        'subject',
        'message',
        'subscribe',
    ];
}
