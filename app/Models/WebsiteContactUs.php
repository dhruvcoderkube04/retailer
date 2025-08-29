<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebsiteContactUs extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'phone',
        'email',
        'address',
        'facebook_link',
        'twitter_link',
        'linkedin_link',
        'instagram_link',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
