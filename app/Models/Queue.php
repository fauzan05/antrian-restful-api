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
        'registration_status',
        'poly_status',
        'counter_registration_id',
        'counter_poly_id'
    ];

    public function serviceRegistration(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'registration_service_id','id');
    }
    public function servicePoly(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'poly_service_id','id');
    }
}