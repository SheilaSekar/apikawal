<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Report\ReportAbuseController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Report\ReportPhotoController;
use App\Http\Controllers\Report\ReportStatusController;
use App\Http\Controllers\Report\ReportVerificationController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.landing')->name('home');
Route::view('/tentang', 'pages.about')->name('about');
Route::view('/dashboard', 'dashboard')->middleware(['auth', 'active'])->name('dashboard');
Route::view('/petugas', 'pages.officer')->middleware(['auth', 'role:petugas,admin'])->name('officer.index');
Route::view('/admin', 'pages.admin')->middleware(['auth', 'role:admin'])->name('admin.index');

Route::get('/laporan/{report:code}', [ReportController::class, 'show'])->name('reports.show');
Route::get('/foto/{photo}', [ReportPhotoController::class, 'show'])->name('reports.photos.show');
Route::post('/laporan/{report:code}/verifikasi', [ReportVerificationController::class, 'store'])->middleware(['auth', 'active', 'throttle:20,1'])->name('reports.verify');
Route::post('/laporan/{report:code}/status', [ReportStatusController::class, 'update'])->middleware(['auth', 'active', 'role:petugas,admin'])->name('reports.status');
Route::post('/laporan/{report:code}/penyalahgunaan', [ReportAbuseController::class, 'store'])->middleware(['auth', 'active', 'throttle:10,1'])->name('reports.abuse');
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/lapor', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/lapor/cek-duplikat', [ReportController::class, 'checkDuplicate'])->middleware('throttle:30,1')->name('reports.check-duplicate');
    Route::post('/lapor', [ReportController::class, 'store'])->middleware('throttle:create-report')->name('reports.store');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
