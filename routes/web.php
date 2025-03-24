<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/map', function () {
    return view('map');
});

Route::get('/freetour', function () {
    return view('freetour');
});
