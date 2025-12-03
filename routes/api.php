<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StokController;
use App\Http\Controllers\Api\SPHController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\KwitansiController;
use App\Http\Controllers\Api\SuratJalanController;
use App\Http\Controllers\Api\PembelianController;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admins', [AuthController::class, 'listAllUsers']);
    Route::put('/admins/{id}/approve', [AuthController::class, 'approveAdmin']);
    Route::delete('/admins/{id}/reject', [AuthController::class, 'rejectAdmin']);
    Route::post('/logout', [AuthController::class, 'logout']);


    // ==========================
    // Stok
    // ==========================
    Route::get('/stoks/summary', [StokController::class, 'summary']);
    Route::get('/stoks/weekly-summary', [StokController::class, 'weeklySummary']);
    Route::get('/stoks', [StokController::class, 'index']);
    Route::post('/stoks', [StokController::class, 'store']);
    Route::get('/stoks/{id}', [StokController::class, 'show']);
    Route::put('/stoks/{id}', [StokController::class, 'update']);
    Route::delete('/stoks/{id}', [StokController::class, 'destroy']);


    // ==========================
    // Pembelian
    // ==========================
    Route::get('/pembelians', [PembelianController::class, 'index']);        
    Route::post('/pembelians', [PembelianController::class, 'store']);      
    Route::get('/pembelians/{id}', [PembelianController::class, 'show']);    
    Route::put('/pembelians/{id}', [PembelianController::class, 'update']);  
    Route::delete('/pembelians/{id}', [PembelianController::class, 'destroy']); 

    // ==========================
    // Dokumen (SPH, Invoice, Kwitansi, Surat Jalan)
    // ==========================
    Route::apiResource('surat-penawaran', SPHController::class);
    Route::apiResource('invoice', InvoiceController::class);
    Route::apiResource('kwitansi', KwitansiController::class);
    Route::apiResource('surat-jalan', SuratJalanController::class);
    
});
