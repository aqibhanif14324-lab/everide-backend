<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $fillable = [
        'role',
        'name',
        'email',
        'password',
        'avatar_url',
        'phone',
        'bio',
        'notification_prefs',
    ];

    protected $attributes = [
        'role' => UserRole::USER->value,
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
            'role' => UserRole::class,
            'notification_prefs' => 'array',
        ];
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function shops()
    {
        return $this->hasMany(Shop::class, 'owner_id');
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isSeller(): bool
    {
        return $this->role === UserRole::SELLER || $this->isAdmin();
    }

    public function isBuyer(): bool
    {
        return in_array($this->role, [UserRole::USER, UserRole::SELLER, UserRole::ADMIN], true);
    }

    public function promoteToSeller(): void
    {
        if (! $this->isSeller()) {
            $this->forceFill([
                'role' => UserRole::SELLER,
            ])->save();
        }
    }
}
