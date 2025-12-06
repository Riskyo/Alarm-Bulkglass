<?php

use App\Http\Controllers\AlarmController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\PdfController; // ⬅ WAJIB ditambahkan
use Illuminate\Support\Facades\Route;

// =========================
// Publik (Guest & Admin bisa akses index/pencarian)
// =========================
Route::get('/', [AlarmController::class, 'index'])->name('alarms.index');

// =========================
// PDF LIST – boleh diakses semua user
// =========================
Route::get('/pdf', [PdfController::class, 'index'])->name('pdf.index');

// =========================
// Route auth login / register
// =========================
require __DIR__.'/auth.php';

// =========================
// Admin area (harus login + role admin)
// =========================
Route::middleware(['auth', 'can:isAdmin'])->group(function () {

    // CRUD alarm kecuali index & show
    Route::resource('alarms', AlarmController::class)->except(['index', 'show']);

    Route::resource('machine-types', \App\Http\Controllers\MachineTypeController::class);

    // nested: tambah action baru pada alarm tertentu
    Route::post('alarms/{alarm}/actions', [ActionController::class, 'store'])
         ->name('actions.store');

    // nested: tambah sensor baru pada action tertentu
    Route::post('actions/{action}/sensors', [SensorController::class, 'store'])
         ->name('sensors.store');

    // =========================
    // CRUD PDF (hanya admin)
    // =========================
    Route::get('/pdf/create', [PdfController::class, 'create'])->name('pdf.create');
    Route::post('/pdf', [PdfController::class, 'store'])->name('pdf.store');
    Route::get('/pdf/{pdf}/edit', [PdfController::class, 'edit'])->name('pdf.edit');
    Route::put('/pdf/{pdf}', [PdfController::class, 'update'])->name('pdf.update');
    Route::delete('/pdf/{pdf}', [PdfController::class, 'destroy'])->name('pdf.destroy');
});
