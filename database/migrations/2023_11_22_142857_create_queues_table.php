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
            $table->string('number')->nullable(false);
            $table->unsignedBigInteger('service_id')->nullable(false);
            $table->unsignedBigInteger('counter_id')->nullable();
            $table->enum('status', ['called','waiting', 'skipped'])->default('waiting')->nullable(false);
            $table->timestamps();
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('counter_id')->references('id')->on('counters');
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
