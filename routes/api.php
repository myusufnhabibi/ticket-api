<?php

use App\Http\Controllers\AduanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('/aduan', [AduanController::class, 'store'])->name('aduan');
    Route::get('/aduan', [AduanController::class, 'index'])->name('aduan');
    Route::get('/aduan/{code}', [AduanController::class, 'show'])->name('aduan');
    Route::post('/aduan-balasan/{code}', [AduanController::class, 'storeBalasan']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});
