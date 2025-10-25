<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\RefreshTokenController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/auth/register', [RegisteredUserController::class, 'store']);
Route::get('/auth/refresh', RefreshTokenController::class);
