<?php

use App\Http\Controllers\QueryDebugController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('debug.dashboard');
});

Route::prefix('debug')->name('debug.')->group(function () {
    Route::get('/', [QueryDebugController::class, 'dashboard'])->name('dashboard');
    Route::get('/method1', [QueryDebugController::class, 'method1'])->name('method1');
    Route::get('/method2', [QueryDebugController::class, 'method2'])->name('method2');
    Route::get('/method3', [QueryDebugController::class, 'method3'])->name('method3');
    Route::get('/method4', [QueryDebugController::class, 'method4'])->name('method4');
    Route::get('/method5', [QueryDebugController::class, 'method5'])->name('method5');
});