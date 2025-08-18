<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'phone_number',
        'status',
        'user_type',
        'last_login',
        'profile',
        'source',
        'ip_address',
        'email_verified',
        'login_attempt',
        'locked_until',
        'is_all_wholesaler_visible',
        'is_delete'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'locked_until' => 'datetime',
        ];
    }

    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'wholesaler_id');
    }

    public function wholesalerCategories()
    {
        return $this->hasMany(WholesalerCategory::class, 'wholesaler_id', 'id'); //add subCategories
    }
}
