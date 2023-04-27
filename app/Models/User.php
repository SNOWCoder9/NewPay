<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public static function getUser($telegram_id)
    {
        return self::query()->where('telegram_id', $telegram_id)->first();
    }

    public function tron(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Tron::class);
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function contract(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function settlement() :\Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Settlement::class);
    }

    public function hasContract()
    {
        return $this->contract()
            ->where('status', 1)
            ->get();
    }

    public function hasContractToken($token = null)
    {
        return $this->contract()
            ->where('token', $token)
            ->where('status', 1)
            ->first();
    }
}
