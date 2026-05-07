<?php

use App\Notifications\FirebasePushNotification;
use Illuminate\Support\Facades\Log;

if (!function_exists('send_firebase_notification')) {
    function send_firebase_notification($title, $body, $deviceToken)
    {
        try {
            $notification = new FirebasePushNotification($title, $body, $deviceToken);
            return $notification->toFirebase();
        } catch (\Exception $e) {
            Log::error('Firebase Notification Error: ' . $e->getMessage());
            return false;
        }
    }
}
