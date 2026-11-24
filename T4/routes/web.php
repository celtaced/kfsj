<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DestinoController;

Route::get('/', [DestinoController::class, 'index'])->name('destinos.index');
Route::get('/destinos/{id}', [DestinoController::class, 'show'])->name('destinos.show');
Route::get('/contacto', [DestinoController::class, 'formulario'])->name('contacto.formulario');
Route::post('/contacto', [DestinoController::class, 'enviarContacto'])->name('contacto.enviar');
