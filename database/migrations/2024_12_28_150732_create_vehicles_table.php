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
        if (Schema::hasTable('vehicles')) {
            return;
        }

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('vehicle_category_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('ac');
            $table->string('engine');
            $table->string('manufacture_year');
            $table->string('manufacture_model');
            $table->string('manufacture_company');
            $table->string('courier_servies');
            $table->string('registration_number');
            $table->string('vehicle_front_pic')->nullable();
            $table->string('vehicle_back_pic')->nullable();
            $table->string('vehicle_dashboard')->nullable();
            $table->string('vehicle_certificate_front')->nullable();
            $table->string('vehicle_certificate_back')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
