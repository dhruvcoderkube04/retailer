<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GstConfiguration extends Model
{
    protected $fillable = [
        'gst_mode',
        'gst',
        'cgst',
        'sgst',
        'igst',
        'status'
    ];
}
