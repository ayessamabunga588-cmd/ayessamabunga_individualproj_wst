<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name',
    'username',
    'first_name',
    'last_name',
    'phone_number',
    'last_login_time',
    'address_id',
    'email',
    'password',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_time' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * One user can make many purchases. The ER diagram links them by email.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'email', 'email');
    }

    /**
     * The buyer's full name as defined by the ER diagram (first_name + last_name).
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(
            fn (): string => trim(($this->first_name ?? '').' '.($this->last_name ?? '')) ?: (string) $this->name
        );
    }
}
