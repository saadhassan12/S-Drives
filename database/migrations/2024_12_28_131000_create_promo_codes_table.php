<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('promo_codes')) {
            return;
        }

        Schema::create('promo_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 50)->unique();
            $table->enum('type', ['percent', 'fixed']);
            $table->decimal('value', 10, 2);
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->decimal('min_fare', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('vehicle_type_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
