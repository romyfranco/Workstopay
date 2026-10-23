<?php

use App\Http\Controllers\FrontendAccessController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendAccessController::class, 'showLogin'])->name('frontend.access');
Route::post('/acceso', [FrontendAccessController::class, 'login'])->name('frontend.login');
Route::get('/vehiculos', [FrontendAccessController::class, 'index'])->name('frontend.vehicle-works');
Route::get('/electric-avenue', [FrontendAccessController::class, 'index'])->name('frontend.electric-avenue');
Route::get('/vehiculos/{vehicleWork}/fotos/{photo}', [FrontendAccessController::class, 'photo'])->whereNumber('photo')->name('frontend.vehicle-works.photo');
Route::post('/vehiculos/{vehicleWork}/pago-enviado', [FrontendAccessController::class, 'markPaymentSent'])->name('frontend.vehicle-works.payment-sent');
Route::post('/salir', [FrontendAccessController::class, 'logout'])->name('frontend.logout');
