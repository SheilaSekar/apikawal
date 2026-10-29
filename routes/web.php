<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Report\ReportAbuseController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Report\ReportPhotoController;
use App\Http\Controllers\Report\ReportStatusController;
use App\Http\Controllers\Report\ReportVerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'landing'])->name('home');
Route::get('/peta', [PublicPageController::class, 'map'])->name('map');
Route::get('/peta/data', [PublicPageController::class, 'mapData'])->name('map.data');
Route::get('/analitik', [PublicPageController::class, 'analytics'])->name('analytics');
Route::get('/api-docs', [PublicPageController::class, 'apiDocs'])->name('api-docs');
Route::get('/tentang', [PublicPageController::class, 'about'])->name('about');
Route::get('/dashboard', [PublicPageController::class, 'dashboard'])->middleware(['auth', 'active'])->name('dashboard');
Route::get('/petugas', [PublicPageController::class, 'officer'])->middleware(['auth', 'role:petugas,admin'])->name('officer.index');
Route::post('/notifikasi/{notification}/baca', [PublicPageController::class, 'readNotification'])->middleware(['auth', 'active'])->name('notifications.read');
Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::patch('/reports/{report}/moderasi', [AdminController::class, 'moderate'])->name('moderation.approve');
    Route::patch('/flags/{flag}', [AdminController::class, 'resolveFlag'])->name('flags.resolve');
    Route::put('/bobot', [AdminController::class, 'updateWeights'])->name('weights.update');
    Route::post('/bobot/reset', [AdminController::class, 'resetWeights'])->name('weights.reset');
    Route::post('/hotspots', [AdminController::class, 'importHotspots'])->name('hotspots.import');
    Route::post('/grid/recompute', [AdminController::class, 'recompute'])->name('grid.recompute');
    Route::get('/ekspor', [AdminController::class, 'export'])->name('export');
});

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
