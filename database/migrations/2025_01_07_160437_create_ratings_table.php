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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ride_id'); // Ride associated with the rating
            $table->unsignedBigInteger('rated_by'); // ID of the person giving the rating (driver/customer)
            $table->unsignedBigInteger('rated_to'); // ID of the person being rated (driver/customer)
            $table->integer('rating'); // Rating score (e.g., 1-5)
            $table->text('comment')->nullable(); // Optional feedback
            $table->timestamps();
        
            $table->foreign('ride_id')->references('id')->on('rides')->onDelete('cascade');
            $table->foreign('rated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('rated_to')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
