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
        if (Schema::hasTable('cancel_ride')) {
            return;
        }

        Schema::create('cancel_ride', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ride_id');
            $table->unsignedBigInteger('user_id'); 
            $table->string('reason')->nullable(); 
            $table->enum('canceled_by', ['driver', 'passenger']);
            $table->timestamps();
            $table->foreign('ride_id')->references('id')->on('rides')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancel_ride');
    }
};
