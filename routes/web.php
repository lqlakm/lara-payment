<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'order.form');

Route::prefix('order')->name('order.')->group(function () {
    Route::post('validate', [PaymentController::class, 'validateOrder'])->name('validate');
    
    Route::post('create', [PaymentController::class, 'createOrder'])->name('create');
    
    Route::post('process', [PaymentController::class, 'processOrder'])->name('process');
    
    Route::get('complete', [PaymentController::class, 'showComplete'])->name('complete');
});

