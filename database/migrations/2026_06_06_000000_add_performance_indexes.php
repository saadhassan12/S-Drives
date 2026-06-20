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
        Schema::table('rides', function (Blueprint $table) {
            $table->index('user_id', 'rides_user_id_index');
            $table->index('driver_id', 'rides_driver_id_index');
            $table->index('status', 'rides_status_index');
            $table->index('vehicle_category_id', 'rides_vehicle_category_id_index');
            $table->index(['vehicle_category_id', 'status', 'time_out'], 'rides_vehicle_status_timeout_index');
            $table->index(['start_latitude', 'start_longitude'], 'rides_start_lat_lng_index');
            $table->index('created_at', 'rides_created_at_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'users_role_index');
            $table->index('last_login_at', 'users_last_login_at_index');
            $table->index(['latitude', 'longitude'], 'users_lat_lng_index');
        });

        Schema::table('bids', function (Blueprint $table) {
            $table->index(['ride_id', 'status'], 'bids_ride_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->dropIndex('rides_user_id_index');
            $table->dropIndex('rides_driver_id_index');
            $table->dropIndex('rides_status_index');
            $table->dropIndex('rides_vehicle_category_id_index');
            $table->dropIndex('rides_vehicle_status_timeout_index');
            $table->dropIndex('rides_start_lat_lng_index');
            $table->dropIndex('rides_created_at_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
            $table->dropIndex('users_last_login_at_index');
            $table->dropIndex('users_lat_lng_index');
        });

        Schema::table('bids', function (Blueprint $table) {
            $table->dropIndex('bids_ride_status_index');
        });
    }
};
