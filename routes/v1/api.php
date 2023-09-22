<?php

use App\Http\Controllers\Api\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\V1\Examination\ExaminationController;
use App\Http\Controllers\Api\V1\Examination\ExaminationEnrollmentController;
use App\Http\Controllers\Api\V1\Examination\ExaminationProcessController;
use App\Http\Controllers\Api\V1\Examination\QuestionController;
use App\Http\Controllers\Api\V1\Examination\RandomQuestionController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\StudentExaminationController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', AuthUserController::class);
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::apiResource('/users', UserController::class);

    // Teachers Exam Route
    Route::apiResource('examinations', ExaminationController::class);
    Route::apiResource('questions', QuestionController::class);
    Route::post('examinations/{examination}/enroll', ExaminationEnrollmentController::class)
        ->name('exam.enroll');

    // Students Exam Route
    Route::get('students/{user}/exams', StudentExaminationController::class)->name('student.exams');
    Route::get('examinations/{examination}/questions', RandomQuestionController::class)
        ->name('questions.random');
    Route::post('examinations/{examination}/submit', ExaminationProcessController::class)
        ->name('exam.submit');
});
