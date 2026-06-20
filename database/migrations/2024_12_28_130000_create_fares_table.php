<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fares')) {
            return;
        }

        Schema::create('fares', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('vehicle_category_id')->nullable();
            $table->decimal('fare_per_km', 8, 2)->nullable();
            $table->decimal('minimun_rate', 8, 2);
            $table->string('pro_code_rate');
            $table->integer('pro_code_minimun_rate');
            $table->decimal('gst', 8, 2)->nullable();
            $table->decimal('ride_tax', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fares');
    }
};
