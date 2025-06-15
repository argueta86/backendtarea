<?php

use App\Http\Controllers\Api\CreatePermissionRolController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\TareaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('refresh-token', [AuthController::class, 'refresh']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware('auth:api')->prefix('users')->group(function () {
    Route::get('/role', [CreatePermissionRolController::class, 'getRole'])->middleware('rol:Super Admin');
    Route::post('/permissions', [CreatePermissionRolController::class, 'createPermissionsAction'])->middleware('rol:Super Admin,Admin');
    Route::post('/role', [CreatePermissionRolController::class, 'store'])->middleware('rol:Super Admin');
    Route::post('logout', [AuthController::class, 'logout']);
});
// rutas del dashboard
Route::middleware('auth:api')->group(function () {
    Route::get('/admin-dashboard', function () {
        return response()->json(['message' => 'Welcome to the admin dashboard']);
    })->middleware('rol:Admin,Super Admin');

   // rutas de tareas
    Route::apiResource('tareas', TareaController::class);
    Route::post('/tareas', [TareaController::class, 'store']);
    Route::get('/tareas', [TareaController::class, 'index']);
});
