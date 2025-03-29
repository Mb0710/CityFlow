<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('login');
})->name('login');

Route::get('/map', function () {
    return view('maptest');
});

Route::get('/freetour', function () {
    return view('freetour');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::apiResource('user', AuthController::class);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/map', function () {
        return view('map');
    })->name('map');
});

Route::get('/check-login', function (Illuminate\Http\Request $request) {
    $login = $request->input('login');
    $exists = \App\Models\User::where('login', $login)->exists();

    return response()->json([
        'available' => !$exists
    ]);
});

Route::get('/check-email', function (Illuminate\Http\Request $request) {
    $login = $request->input('email');
    $exists = \App\Models\User::where('email', $login)->exists();

    return response()->json([
        'available' => !$exists
    ]);
});

