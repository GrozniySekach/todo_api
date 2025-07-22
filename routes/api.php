<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register'])->name('register');
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me'])->name('me');
    
    Route::apiResource('profiles', App\Http\Controllers\Api\ProfileController::class)->except(['index'])->names('profiles');
    
    Route::apiResource('projects', App\Http\Controllers\Api\ProjectController::class)->names('projects');
    Route::post('/projects/{project}/add-user', [App\Http\Controllers\Api\ProjectController::class, 'addUser'])->name('projects.add-user');
    Route::post('/projects/{project}/remove-user', [App\Http\Controllers\Api\ProjectController::class, 'removeUser'])->name('projects.remove-user');
    
    Route::apiResource('tasks', App\Http\Controllers\Api\TaskController::class)->names('tasks');
    Route::get('/tasks/{task}/deleted', [App\Http\Controllers\Api\TaskController::class, 'showDeleted'])->name('tasks.showDeleted');
    Route::post('/tasks/{task}/restore', [App\Http\Controllers\Api\TaskController::class, 'restore'])->name('tasks.restore');
    Route::post('/tasks/{task}/share', [App\Http\Controllers\Api\TaskController::class, 'share'])->name('tasks.share');
    Route::post('/tasks/{task}/unshare', [App\Http\Controllers\Api\TaskController::class, 'unshare'])->name('tasks.unshare');
    
    Route::apiResource('tags', App\Http\Controllers\Api\TagController::class)->names('tags');
});