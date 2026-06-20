<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vehicle_categories')) {
            return;
        }

        Schema::create('vehicle_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_categories');
    }
};
