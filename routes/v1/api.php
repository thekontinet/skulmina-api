<?php

use App\Http\Controllers\Api\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\Examination\ExaminationController;
use App\Http\Controllers\Api\V1\Examination\ExaminationProcessController;
use App\Http\Controllers\Api\V1\Examination\QuestionController;
use App\Http\Controllers\Api\V1\Examination\RandomQuestionController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\AuthUserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', AuthUserController::class);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::apiResource('/users', UserController::class);

    // Teachers & Admin Exam Route
    Route::apiResource('courses', CourseController::class);
    Route::post('courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::apiResource('examinations', ExaminationController::class);
    Route::apiResource('questions', QuestionController::class);

    // Students Exam Route
    Route::get('examinations/{examination}/questions', RandomQuestionController::class)
        ->name('questions.random');
    Route::post('examinations/{examination}/submit', ExaminationProcessController::class)
        ->name('exam.submit');
});
