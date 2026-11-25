<?php

use App\Http\Controllers\PriceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PriceController::class, 'index'])->name('calculator.index');
Route::post('/calculate', [PriceController::class, 'calculate'])->name('calculator.calculate');
