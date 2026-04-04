<?php

use App\Http\Controllers\FrontendAccessController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendAccessController::class, 'showLogin'])->name('frontend.access');
Route::post('/acceso', [FrontendAccessController::class, 'login'])->name('frontend.login');
Route::get('/vehiculos', [FrontendAccessController::class, 'index'])->name('frontend.vehicle-works');
Route::post('/salir', [FrontendAccessController::class, 'logout'])->name('frontend.logout');
