<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DentistController;
use App\Http\Controllers\AppointmentController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [AppointmentController::class, 'index'])
        ->name('dashboard');

    // Appointments
    Route::post('/appointments', [AppointmentController::class, 'store'])
        ->name('appointments.store');

    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])
        ->name('appointments.update');

    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])
        ->name('appointments.destroy');

    // Dentists
    Route::get('/dentists', [DentistController::class, 'index'])->name('dentists.index');
    Route::post('/dentists', [DentistController::class, 'store'])->name('dentists.store');
    Route::put('/dentists/{dentist}', [DentistController::class, 'update'])->name('dentists.update');
    Route::delete('/dentists/{dentist}', [DentistController::class, 'destroy'])->name('dentists.destroy');
});
