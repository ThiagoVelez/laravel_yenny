<?php

use App\Http\Controllers\PersonaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// El prefijo '/api' se añade automáticamente
Route::post('/personas', [PersonaController::class, 'store']);

