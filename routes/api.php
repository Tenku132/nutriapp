<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PaymentController; 

Route::post('/paymongo/payment-intent', [PaymentController::class, 'createPaymentIntent']);
Route::post('/paymongo/payment-method', [PaymentController::class, 'createPaymentMethod']);
Route::post('/paymongo/attach', [PaymentController::class, 'attachPaymentMethod']);
