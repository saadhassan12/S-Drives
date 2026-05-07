<?php

use App\Http\Controllers\Web\pageController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::controller(pageController::class)->group(function () {
    Route::get('/Contact', 'contact')->name('contact');
    Route::get('/', 'home')->name('home');
    Route::get('/privacy', 'privacy')->name('privacy');
    Route::get('/deletaccount', 'deletaccount')->name('deletaccount');
    Route::get('/terms', 'terms')->name('terms');
    Route::get('/support', 'support')->name('support');

    Route::post('/delete-account', 'delete')->name('delete.account');

    Route::get('/share/ride/{rideId}', 'show');


});

Route::get('/driver-location/{driver_id}', function ($driver_id) {
    $driver = User::find($driver_id);
    return response()->json([
        'latitude' => $driver->latitude,
        'longitude' => $driver->longitude,
    ]);
});