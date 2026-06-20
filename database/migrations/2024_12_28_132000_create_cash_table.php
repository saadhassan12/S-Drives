<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cash')) {
            return;
        }

        Schema::create('cash', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->dateTime('created_at', 6)->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash');
    }
};
