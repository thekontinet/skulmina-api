<?php

use App\Http\Controllers\Api\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\V1\Auth\RegisteredUserController;
use App\Http\Controllers\Api\V1\QuestionController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ExaminationController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

Route::middleware(['auth:sanctum'])->group(function(){
    Route::get('/user', UserController::class);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::apiResource('examinations', ExaminationController::class);
    Route::apiResource('examinations/{examination}/questions', QuestionController::class);
});
