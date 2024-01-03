<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalHours extends Model
{
    protected $table = "operational_hours";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'days',
        'open',
        'close',
        'is_active'
    ];

    // protected $casts = [
    //     'open' => 'hh:mm',
    //     'close' => 'hh:mm'
    // ];
}
