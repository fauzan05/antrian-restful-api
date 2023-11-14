<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    protected $table = "users";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;
   
    protected $fillable = [
        'name',
        'username',
        'password',
    ];

    public function hasRole()
    {
        return $this->role;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public function counter(): HasOne
    {
        return $this->hasOne(Counter::class, 'user_id', 'id');
    }

    public function service(): HasManyThrough
    {
        return $this->hasManyThrough(
            Service::class, 
            Counter::class,
            'user_id',
            'counter_id',
            'id',
            'id'
        );
    }
}
