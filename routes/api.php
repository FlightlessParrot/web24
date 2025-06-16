<?php

use App\Http\Controllers\Auth\AuthenticatedController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->name('api.register');
Route::post('/login', [AuthenticatedController::class, 'store'])
    ->name('api.login');
Route::middleware(['auth:sanctum'])->post('/logout', [AuthenticatedController::class, 'destroy'])
        ->name('api.logout');

