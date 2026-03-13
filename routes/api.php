<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProductController;

Route::post('/purchase', [TransactionController::class, 'purchase']);
Route::post('/products', [ProductController::class, 'store']);