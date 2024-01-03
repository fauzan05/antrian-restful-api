<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    protected $table = "admin_settings";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'selected_video',
        'name_of_health_institute',
        'operational_hours_open',
        'operational_hours_close',
        'text_footer_display',
        'display_footer_color',
    ];
}
