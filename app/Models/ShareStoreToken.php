<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareStoreToken extends Model
{
    protected $fillable = ['token', 'expires_at', 'status'];
}
