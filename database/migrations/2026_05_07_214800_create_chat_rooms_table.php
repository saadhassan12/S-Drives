<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chat_rooms')) {
            return;
        }

        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ride_id')->unique();
            $table->unsignedBigInteger('passenger_id');
            $table->unsignedBigInteger('driver_id');
            $table->enum('status', ['pending', 'active', 'closed'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index(['passenger_id', 'driver_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
};
