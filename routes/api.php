<?php

use App\Http\Controllers\Auth\AuthenticatedController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CompanyController;
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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('companies', CompanyController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names([
            'index' => 'api.companies.index',
            'show' => 'api.companies.show',
            'store' => 'api.companies.store',
            'update' => 'api.companies.update',
            'destroy' => 'api.companies.destroy',
        ]);

    Route::resource('employees', \App\Http\Controllers\EmployeeController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names([
            'index' => 'api.employees.index',
            'show' => 'api.employees.show',
            'store' => 'api.employees.store',
            'update' => 'api.employees.update',
            'destroy' => 'api.employees.destroy',
        ]);
});