<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GoogleController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

Route::middleware(['role:admin'])->group(function () {
    // Full system control
});

Route::middleware(['role:department'])->group(function () {
    // Manage news & events
});

Route::middleware(['role:staff'])->group(function () {
    // View only
});
