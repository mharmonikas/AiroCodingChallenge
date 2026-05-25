<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuotationController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/quotation', [QuotationController::class, 'store'])
    ->middleware(['auth:api', 'json.only']);
