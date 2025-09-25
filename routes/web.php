<?php

use App\Http\Controllers\AlarmController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\SensorController;
use Illuminate\Support\Facades\Route;

// =========================
// Publik (Guest & Admin bisa akses index/pencarian)
// =========================
Route::get('/', [AlarmController::class, 'index'])->name('alarms.index');

// kalau memang tidak ada detail satu alarm khusus, baris show ini cukup dikomentari saja
// Route::get('/alarms/{alarm}', [AlarmController::class, 'show'])->name('alarms.show');

require __DIR__.'/auth.php';

// =========================
// Admin area (harus login + role admin)
// =========================
Route::middleware(['auth', 'can:isAdmin'])->group(function () {

    // CRUD alarm kecuali index & show
    Route::resource('alarms', AlarmController::class)->except(['index', 'show']);

    // nested: tambah action baru pada alarm tertentu
    Route::post('alarms/{alarm}/actions', [ActionController::class, 'store'])
         ->name('actions.store');

    // nested: tambah sensor baru pada action tertentu
    Route::post('actions/{action}/sensors', [SensorController::class, 'store'])
         ->name('sensors.store');
});
