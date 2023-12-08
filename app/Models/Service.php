<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;
    protected $table = "services";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'name',
        'initial',
        'description',
        'counter_id',
        'role'
    ];

    public function counter(): HasMany
    {
        return $this->hasMany(Counter::class, 'service_id', 'id');
    }

    public function queueRegistration(): HasMany
    {       
        return $this->hasMany(Queue::class, 'registration_id', 'id');
    }
    public function queuePoly(): HasMany
    {       
        return $this->hasMany(Queue::class, 'poly_id', 'id');
    }
}
