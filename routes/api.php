<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegistedUserController;

Route::get('/user', function (Request $request) {
    return response()->json([
            'statut' => 200,
            'user' => $request->user(),
            'message' => 'User created successfully!!'
    ]);
})->middleware(['session']);

