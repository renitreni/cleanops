<?php

use App\Http\Controllers\Api\V2\ComplaintController;
use Illuminate\Support\Facades\Route;

Route::post('complaint-store', [ComplaintController::class, 'store']);
