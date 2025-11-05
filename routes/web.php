<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\RefreshTokenController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\EmailVerificationLinkController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/auth/register', RegisteredUserController::class);
Route::get('/auth/refresh', RefreshTokenController::class);
Route::post('/auth/password_reset', PasswordResetLinkController::class);
Route::get('/auth/email_verification', EmailVerificationLinkController::class);


