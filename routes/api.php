<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\Settings\BusinessSettingController;
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

    Route::apiResource('master-data', MasterDataController::class);
    Route::post('/master-data/import', [MasterDataController::class, 'import']);
    Route::get('/master-data/export', [MasterDataController::class, 'export'])->name('master-data.export');
    Route::get('master-data-types', [MasterDataController::class, 'masterDataTypes']); 
    Route::get('active-master-data', [MasterDataController::class, 'activeMasterData']); 
    Route::patch('master-data/{master_datum}/toggle-status', [MasterDataController::class, 'toggleStatus']);

    Route::apiResource('employees', EmployeeController::class);
    Route::post('/employees/import', [EmployeeController::class, 'import']);
    Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::get('employees-types', [EmployeeController::class, 'masterDataTypes']); 
    Route::get('active-employees', [EmployeeController::class, 'activeEmployees']); 
    Route::patch('employees/{master_datum}/toggle-status', [MasterDataController::class, 'toggleStatus']);


    Route::apiResource(' business-settings', BusinessSettingController::class);
});

