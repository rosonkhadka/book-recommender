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
               Route::post('/google/login', 'googleLogin');
               Route::post('/logout', 'logout');
               Route::get('/refresh', 'refresh');
               Route::post('/forgot-password', 'forgotPassword');
               Route::post('/reset-password', 'resetPassword');
               Route::post('/email/verify', 'verifyEmail');
               Route::get('/email/verification-notification', 'resendVerificationEmail');
           });
       Route::controller(UserController::class)
       ->group(function(): void {
           Route::get('/users', 'index');
           Route::get('/users/{user}', 'show');
           Route::post('/users', 'store');
           Route::patch('/users/{user}', 'update');
           Route::delete('/users/{user}', 'destroy');
       });
    });

