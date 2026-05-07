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
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ride_id'); // Foreign key for the ride
            $table->unsignedBigInteger('driver_id'); // Foreign key for the driver
            $table->decimal('amount', 10, 2)->nullable(); // Bid amount
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending'); // Bid status
            $table->timestamps();
            // Foreign key constraints
            $table->foreign('ride_id')->references('id')->on('rides')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bid');
    }
};
