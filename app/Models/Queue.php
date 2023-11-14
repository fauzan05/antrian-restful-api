<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Queue extends Model
{
    protected $table = "queues";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'status',
        'service_id'
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id','id');

    }

    public function counter(): HasManyThrough
    {
        return $this->hasManyThrough(
            Counter::class,
            Service::class,
            'id', // Foreign key di model Service
            'id', // Foreign key di model Counter
            'service_id', // Local key di model Queue
            'counter_id' // Local key di model Service
        );
    }

}