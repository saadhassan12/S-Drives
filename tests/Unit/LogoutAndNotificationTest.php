<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class LogoutAndNotificationTest extends TestCase
{
    public function test_default_device_token_is_invalid(): void
    {
        $this->assertFalse(is_valid_device_token(null));
        $this->assertFalse(is_valid_device_token(''));
        $this->assertFalse(is_valid_device_token('default_token'));
    }

    public function test_real_device_token_is_valid(): void
    {
        $this->assertTrue(is_valid_device_token('fcm-real-token-123'));
    }

    public function test_logged_in_driver_can_receive_ride_notifications(): void
    {
        $driver = new User([
            'role' => 'driver',
            'last_login_at' => 1,
            'device_token' => 'fcm-real-token-123',
        ]);

        $this->assertTrue(can_driver_receive_ride_notifications($driver));
    }

    public function test_logged_out_driver_cannot_receive_ride_notifications(): void
    {
        $driver = new User([
            'role' => 'driver',
            'last_login_at' => 0,
            'device_token' => 'fcm-real-token-123',
        ]);

        $this->assertFalse(can_driver_receive_ride_notifications($driver));
    }

    public function test_driver_with_default_token_cannot_receive_ride_notifications(): void
    {
        $driver = new User([
            'role' => 'driver',
            'last_login_at' => 1,
            'device_token' => 'default_token',
        ]);

        $this->assertFalse(can_driver_receive_ride_notifications($driver));
    }

    public function test_passenger_cannot_receive_driver_ride_notifications(): void
    {
        $passenger = new User([
            'role' => 'passenger',
            'last_login_at' => 1,
            'device_token' => 'fcm-real-token-123',
        ]);

        $this->assertFalse(can_driver_receive_ride_notifications($passenger));
    }

    public function test_send_firebase_notification_skips_invalid_tokens(): void
    {
        $this->assertFalse(send_firebase_notification('Title', 'Body', 'default_token'));
        $this->assertFalse(send_firebase_notification('Title', 'Body', null));
    }

    public function test_send_driver_ride_notification_blocks_logged_out_driver(): void
    {
        $driver = new User([
            'role' => 'driver',
            'last_login_at' => 0,
            'device_token' => 'fcm-real-token-123',
        ]);

        $this->assertFalse(send_driver_ride_notification($driver, 'New Ride Available', 'Test'));
    }

    public function test_push_is_skipped_when_app_is_open_in_foreground(): void
    {
        $user = new User([
            'role' => 'driver',
            'last_login_at' => 1,
            'device_token' => 'fcm-real-token-123',
            'is_online' => true,
            'is_app_foreground' => true,
        ]);

        $this->assertFalse(should_send_push_notification($user));
        $this->assertFalse(send_user_push_notification($user, 'New Ride Available', 'Test'));
    }

    public function test_push_is_sent_when_app_is_in_background(): void
    {
        $user = new User([
            'role' => 'driver',
            'last_login_at' => 1,
            'device_token' => 'fcm-real-token-123',
            'is_online' => true,
            'is_app_foreground' => false,
        ]);

        $this->assertTrue(should_send_push_notification($user));
    }

    public function test_push_is_sent_when_app_is_offline(): void
    {
        $user = new User([
            'role' => 'driver',
            'last_login_at' => 1,
            'device_token' => 'fcm-real-token-123',
            'is_online' => false,
            'is_app_foreground' => false,
        ]);

        $this->assertTrue(should_send_push_notification($user));
    }

    public function test_push_is_sent_when_online_but_not_in_foreground(): void
    {
        $user = new User([
            'is_online' => true,
            'is_app_foreground' => false,
        ]);

        $this->assertTrue(should_send_push_notification($user));
    }

    public function test_push_is_sent_when_foreground_flag_stale(): void
    {
        $user = new User([
            'is_app_foreground' => true,
            'last_seen_at' => now()->subSeconds(120),
        ]);

        $this->assertFalse(is_user_actively_in_app($user));
        $this->assertTrue(should_send_push_notification($user));
    }

    public function test_push_is_skipped_when_foreground_and_recently_active(): void
    {
        $user = new User([
            'is_app_foreground' => true,
            'last_seen_at' => now()->subSeconds(5),
        ]);

        $this->assertTrue(is_user_actively_in_app($user));
        $this->assertFalse(should_send_push_notification($user));
    }
}
