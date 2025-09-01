<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PermissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', function () {
        $user = auth()->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'roles' => $user->getRoleNames(),          // ["admin", "editor"]
            'permissions' => $user->getAllPermissions()->pluck('name'), // ["edit-user", "delete-post"]
        ]);
    });

    Route::get('/profile', [AuthController::class, 'profile']);
    #update profile
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    #change password
    Route::put('/change-password', [AuthController::class, 'changePassword']);
    #logout
    Route::post('/logout', [AuthController::class, 'logout']);
    #delete account
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
    #user resource routes
    Route::apiResource('users', UserController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::apiResource('roles', RoleController::class);
    Route::get('/role-permissions', [RoleController::class, 'rolePermissions']);
    Route::get('/active-roles', [RoleController::class, 'activeRoles']);
});
