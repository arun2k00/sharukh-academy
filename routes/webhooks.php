<?php

use App\Http\Controllers\Webhooks\PhonePeWebhookController;
use App\Http\Controllers\Webhooks\RazorpayWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('razorpay', RazorpayWebhookController::class)
    ->middleware('throttle:webhook-razorpay')
    ->name('razorpay');

Route::post('phonepe', PhonePeWebhookController::class)
    ->middleware('throttle:webhook-phonepe')
    ->name('phonepe');
