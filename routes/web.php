<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionPackageController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Superuser\UserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;

// Root redirect
Route::redirect('/', '/dashboard');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard (Protected by Auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Role-Based Routes
Route::middleware(['auth'])->group(function () {

    // 1. Superuser Only: User Management
    Route::middleware(['role:superuser'])->prefix('superuser')->name('superuser.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::post('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::post('/users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
        Route::patch('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.update-password');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // 2. Question Management (Teacher & Administrator & Superuser)
    Route::middleware(['role:teacher,administrator,superuser'])->group(function () {
        Route::resource('question-packages', QuestionPackageController::class);
        Route::post('question-packages/{question_package}/toggle-publish', [QuestionPackageController::class, 'togglePublish'])
            ->name('question-packages.toggle-publish');
        Route::get('question-packages/{questionPackage}/results', [QuestionPackageController::class, 'results'])
            ->name('question-packages.results');
        Route::resource('question-packages.questions', QuestionController::class)->shallow();
    });

    // 3. Exam / CBT (Student & Administrator & Superuser)
    Route::middleware(['role:student,administrator,superuser'])->group(function () {
        Route::get('exams', [ExamController::class, 'index'])->name('exams.index');
        Route::post('exams/packages/{questionPackage}/start', [ExamController::class, 'start'])->name('exams.start');
        Route::get('exams/attempt/{questionAttempt}', [ExamController::class, 'attempt'])->name('exams.attempt');
        Route::post('exams/attempt/{questionAttempt}/save', [ExamController::class, 'saveResponse'])->name('exams.save-response');
        Route::post('exams/attempt/{questionAttempt}/submit', [ExamController::class, 'submit'])->name('exams.submit');
        Route::get('exams/results/{questionAttempt}', [ExamController::class, 'results'])->name('exams.results');
    });
});

// Error pages
Route::prefix('error')->group(function () {
    Route::view('400', 'errors.400')->name('error.page.400');
    Route::view('401', 'errors.401')->name('error.page.401');
    Route::view('403', 'errors.403')->name('error.page.403');
    Route::view('404', 'errors.404')->name('error.page.404');
    Route::view('500', 'errors.500')->name('error.page.500');
    Route::view('503', 'errors.503')->name('error.page.503');
});
