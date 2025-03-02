<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return '';
});

Route::get('/complaint-report', function () {
    return view('complaint-report');
});