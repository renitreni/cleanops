<?php

use App\Http\Controllers\ComplaintReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return '';
});

Route::get('/complaint-report', [ComplaintReportController::class, 'index'])->name('complaint-report');
Route::post('/complaint-report/store', [ComplaintReportController::class, 'store'])->name('complaint-report.store');
