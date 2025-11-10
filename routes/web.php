<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\RefreshTokenController;
use App\Http\Controllers\Auth\ResetPasswordLinkController;
use App\Http\Controllers\Auth\EmailVerificationLinkController;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/auth/register', RegisteredUserController::class);
Route::get('/auth/refresh', RefreshTokenController::class);
Route::post('/auth/password_reset', ResetPasswordLinkController::class);


Route::middleware('session')->group(function () {
    Route::get('/auth/email_verification', EmailVerificationLinkController::class);
});
