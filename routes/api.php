<?php

use App\Http\Controllers\Api\PrintController;
use App\Http\Controllers\Api\PrintJobApiController;
use Illuminate\Support\Facades\Route;

// Agent Print Bridge API (Stateless & Unprotected for LAN)
Route::prefix('print-jobs')->group(function () {
    Route::get('', [PrintJobApiController::class, 'index']); // Public listing for status
    Route::get('/stats', [PrintJobApiController::class, 'stats']);
    Route::post('/recover', [PrintJobApiController::class, 'recover']);
    Route::post('/claim', [PrintJobApiController::class, 'claim']);
    Route::post('/{id}/complete', [PrintJobApiController::class, 'complete']);
    Route::post('/{id}/failed', [PrintJobApiController::class, 'failed']);
});

Route::middleware('auth')->group(function () {
    // Printer registry
    Route::get('/printers',               [PrintController::class, 'printers']);
    Route::get('/printers/{printer}/ping', [PrintController::class, 'ping']);

    // Admin/UI Print job management
    Route::post('/print-jobs/{job}/cancel',      [PrintController::class, 'cancelJob']);

    // Audit history per item
    Route::get('/print-history/{variantId}',     [PrintController::class, 'history']);
});
