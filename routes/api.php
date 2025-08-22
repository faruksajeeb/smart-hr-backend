<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
#update profile
Route::put('/profile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
#change password
Route::put('/change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
#logout
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
#delete account
Route::delete('/delete-account', [AuthController::class, 'deleteAccount'])->middleware('auth:sanctum');
