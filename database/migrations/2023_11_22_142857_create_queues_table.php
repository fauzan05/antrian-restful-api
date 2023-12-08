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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->nullable(false);
            $table->string('poly_number')->nullable(false);
            $table->unsignedBigInteger('registration_service_id')->nullable(false); // id layanan pendaftaran
            $table->unsignedBigInteger('poly_service_id')->nullable(false); // id layanan poli
            $table->unsignedBigInteger('counter_registration_id')->nullable(); // id loket pendaftaran
            $table->unsignedBigInteger('counter_poly_id')->nullable(); // id loket poli
            $table->enum('registration_status', ['called','waiting','skipped'])->default('waiting')->nullable(false);
            $table->enum('poly_status', ['called','waiting','skipped'])->default('waiting')->nullable(false);
            $table->timestamps();
            $table->foreign('registration_service_id')->references('id')->on('services');
            $table->foreign('poly_service_id')->references('id')->on('services');
            $table->foreign('counter_registration_id')->references('id')->on('counters');
            $table->foreign('counter_poly_id')->references('id')->on('counters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
