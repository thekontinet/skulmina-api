<?php

use App\Http\Controllers\Api\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\V1\Auth\RegisteredUserController;
use App\Http\Controllers\Api\V1\QuestionController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ExaminationController;
use App\Http\Controllers\Api\V1\ExaminationProcessController;
use App\Http\Controllers\ExaminationAssignmentController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

Route::middleware(['auth:sanctum'])->group(function(){
    Route::get('/user', UserController::class);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::apiResource('examinations', ExaminationController::class);
    Route::apiResource('examinations/{examination}/questions', QuestionController::class);
    Route::post('examinations/{examination}/assignments', [ExaminationAssignmentController::class, 'store'])->name('assignments.store');
    Route::delete('examinations/{examination}/assignments', [ExaminationAssignmentController::class, 'destroy'])->name('assignments.destroy');
    Route::post('examinations/{examination}/start', [ExaminationProcessController::class, 'start'])->name('exam.start');
    Route::post('examinations/{examination}/submit', [ExaminationProcessController::class, 'submit'])->name('exam.submit');
});
