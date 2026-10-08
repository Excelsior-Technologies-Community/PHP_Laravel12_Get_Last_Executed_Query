<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QueryDebugController;

Route::get('/', function () {
    return redirect()->route('debug.dashboard');
});

Route::prefix('debug')->name('debug.')->group(function () {
    Route::get('/', [QueryDebugController::class, 'dashboard'])->name('dashboard');

    // 5 Execution Methods
    Route::get('/method1', [QueryDebugController::class, 'method1'])->name('method1');
    Route::get('/method2', [QueryDebugController::class, 'method2'])->name('method2');
    Route::get('/method3', [QueryDebugController::class, 'method3'])->name('method3');
    Route::get('/method4', [QueryDebugController::class, 'method4'])->name('method4');
    Route::get('/method5', [QueryDebugController::class, 'method5'])->name('method5');

    // 1. Live Interactive SQL Sandbox & EXPLAIN Analyzer
    Route::get('/sandbox', [QueryDebugController::class, 'sandboxIndex'])->name('sandbox');
    Route::post('/sandbox/execute', [QueryDebugController::class, 'sandboxExecute'])->name('sandbox.execute');

    // 2. N+1 Query Detector
    Route::get('/n1-detector', [QueryDebugController::class, 'nPlusOneDemo'])->name('n1');

    // 3. Query Execution Benchmark Studio
    Route::get('/benchmark', [QueryDebugController::class, 'benchmarkIndex'])->name('benchmark');
    Route::post('/benchmark/run', [QueryDebugController::class, 'benchmarkRun'])->name('benchmark.run');

    // History & Exporters
    Route::get('/history', [QueryDebugController::class, 'history'])->name('history');
    Route::get('/history/export', [QueryDebugController::class, 'exportCSV'])->name('history.export');
    Route::get('/history/export-pdf', [QueryDebugController::class, 'exportPdf'])->name('history.export.pdf');
    Route::get('/api/json', [QueryDebugController::class, 'jsonApi'])->name('api.json');

    Route::delete('/history/{history}', [QueryDebugController::class, 'destroy'])->name('history.destroy');
    Route::delete('/history', [QueryDebugController::class, 'clear'])->name('history.clear');
});