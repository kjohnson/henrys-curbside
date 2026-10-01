<?php

use App\Http\Controllers\AvailabilityCheckContactController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RegistrationController::class, 'create'])->name('home');

Route::post('/register', [RegistrationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('registrations.store');

// Second step: email and phone. Only reachable through the signed, one-hour URL issued
// when the availability check is created.
Route::middleware('signed')->controller(AvailabilityCheckContactController::class)->group(function () {
    Route::get('/availability-checks/{availabilityCheck}/contact', 'edit')->name('availability-checks.contact.edit');
    Route::put('/availability-checks/{availabilityCheck}/contact', 'update')->name('availability-checks.contact.update');
});
