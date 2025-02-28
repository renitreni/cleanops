<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to('https://aljouf-baladiya.site/');
});





Route::get('/complaint-report', function () {
    return view('complaint-report');

});