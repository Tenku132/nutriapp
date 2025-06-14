<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Auth\Notifications\ResetPassword;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'phone',
    'business_name',
    'bio',
    'payout_method',
    'payout_account',
    'profile_photo',
    'business_photo',
    'payout_method_secondary',
    'payout_account_secondary',
    'is_banned',
    'is_permanently_banned',
    'banned_until',
    'ban_reason',
    'street',
    'barangay',
    'city',
    'province',
    'zip',
];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_banned' => 'boolean',
        'is_permanently_banned' => 'boolean',
        'banned_until' => 'datetime',
    ];
}

public function getBusinessPhotoUrlAttribute()
{
    return $this->business_photo
        ? asset('storage/business_photos/' . $this->business_photo)
        : asset('img/avatar.jpg');
}


    /**
     * Relationship: A user (farmer) can have many products.
     */
        public function orders()
    {
        return $this->hasMany(\App\Models\Order::class, 'buyer_id');
    }
public function products()
{
    return $this->hasMany(Product::class, 'farmer_id'); 
}


    /**
     * Override for sending password reset depending on role.
     */
    public function sendPasswordResetNotification($token)
    {
        if ($this->role === 'admin') {
            $this->notify(new AdminResetPasswordNotification($token));
        } else {
            $this->notify(new ResetPassword($token));
        }
    }
    public function ratings()
{
    return $this->hasMany(\App\Models\Rating::class);
}
public function followers()
{
    return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id');
}

public function following()
{
    return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id');
}

public function isCurrentlyBanned(): bool
{
    if (!$this->is_banned) {
        return false;
    }

    if ($this->is_permanently_banned) {
        return true;
    }

    return $this->banned_until && now()->lt($this->banned_until);
}

}
