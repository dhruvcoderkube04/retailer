<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareLink extends Model
{
    protected $fillable = ['wholesaler_id', 'retailer_id', 'token_id', 'status'];

    public function token()
    {
        return $this->belongsTo(ShareStoreToken::class, 'token_id');
    }
}
