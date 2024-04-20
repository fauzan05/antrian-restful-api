<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('selected_logo')->nullable();
            $table->string('selected_video')->nullable();
            $table->string('name_of_health_institute')->nullable();
            $table->string('address_of_health_institute')->nullable();
            $table->string('text_footer_display')->nullable();
            $table->string('header_color')->nullable();
            $table->string('text_header_color')->nullable();
            $table->string('footer_color')->nullable();
            $table->string('text_footer_color')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
