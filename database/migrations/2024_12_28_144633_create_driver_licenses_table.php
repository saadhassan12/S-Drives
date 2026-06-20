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
        if (Schema::hasTable('driver_licenses')) {
            return;
        }

        Schema::create('driver_licenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('license_no');
            $table->string('expiration_date');
            $table->string('licenses_front_pic')->nullable();
            $table->string('licenses_back_pic')->nullable();
            $table->string('selfie_with_licenses')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_licenses');
    }
};
