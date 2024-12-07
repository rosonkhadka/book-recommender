<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware('api')
    ->group(function(): void{
       Route::controller(AuthController::class)
           ->prefix('auth')
           ->group(function (): void {
               Route::post('/login', 'login');
           });
       Route::controller(UserController::class)
       ->group(function(): void {
           Route::get('/users', 'index');
           Route::post('/users', 'store');
       });
    });

