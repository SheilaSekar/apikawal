<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.landing')->name('home');
Route::view('/tentang', 'pages.about')->name('about');
Route::view('/dashboard', 'dashboard')->middleware(['auth', 'active'])->name('dashboard');
Route::view('/petugas', 'pages.officer')->middleware(['auth', 'role:petugas,admin'])->name('officer.index');
Route::view('/admin', 'pages.admin')->middleware(['auth', 'role:admin'])->name('admin.index');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
