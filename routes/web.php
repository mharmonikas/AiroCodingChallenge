<?php

use App\Http\Controllers\QuotationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QuotationController::class, 'index'])->name('quotation.form');
