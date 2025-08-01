<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // User Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);
    



    // Task Management
    Route::get('/tasks', [TaskController::class, 'index']);         // List all tasks (for logged-in user)
    Route::post('/tasks', [TaskController::class, 'store']);        // Create a new task
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
Route::put('/tasks/{task}', [TaskController::class, 'update']);
Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
Route::patch('/tasks/{task}/complete', [TaskController::class, 'markComplete']);
Route::get('/tasks/overdue', [TaskController::class, 'overdue']);
Route::patch('/tasks/{id}/restore', [TaskController::class, 'restore']);




});
