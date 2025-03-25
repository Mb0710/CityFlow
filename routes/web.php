<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return view('login');
});

Route::get('/map', function () {
    return view('map');
});
