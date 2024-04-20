<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $table = "app_settings";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'selected_logo',
        'selected_video',
        'name_of_health_institute',
        'address_of_health_institute',
        'text_footer_display',
        'header_color',
        'text_header_color',
        'footer_color',
        'text_footer_color',
    ];
}
