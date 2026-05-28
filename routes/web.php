<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionPackageController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Superuser\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClassroomController;
use Illuminate\Support\Facades\Auth;

// Root redirect
Route::redirect('/', '/dashboard');

// Public Pages
Route::view('/terms', 'pages.terms')->name('terms');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard (Protected by Auth)
Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile/password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.password');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// Role-Based Routes
Route::middleware(['auth', 'approved'])->group(function () {

    // 1. User Management
    // Superuser routes
    Route::middleware(['role:superuser'])->prefix('superuser')->name('superuser.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::post('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::post('/users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
        Route::patch('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.update-password');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Administrator routes
    Route::middleware(['role:administrator'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
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

        // Classroom Management
        Route::resource('classrooms', ClassroomController::class);
        Route::post('classrooms/{classroom}/students', [ClassroomController::class, 'addStudent'])->name('classrooms.students.add');
        Route::delete('classrooms/{classroom}/students/{user}', [ClassroomController::class, 'removeStudent'])->name('classrooms.students.remove');
        Route::post('classrooms/{classroom}/packages', [ClassroomController::class, 'assignPackage'])->name('classrooms.packages.assign');
        Route::delete('classrooms/{classroom}/packages/{questionPackage}', [ClassroomController::class, 'removePackage'])->name('classrooms.packages.remove');
    });

    // 3. Exam / CBT (Student & Administrator & Superuser)
    Route::middleware(['role:student,administrator,superuser'])->group(function () {
        Route::get('exams', [ExamController::class, 'index'])->name('exams.index');
        Route::post('exams/packages/{questionPackage}/start', [ExamController::class, 'start'])->name('exams.start');
        Route::get('exams/attempt/{questionAttempt}', [ExamController::class, 'attempt'])->name('exams.attempt');
        Route::post('exams/attempt/{questionAttempt}/save', [ExamController::class, 'saveResponse'])->name('exams.save-response');
        Route::post('exams/attempt/{questionAttempt}/submit', [ExamController::class, 'submit'])->name('exams.submit');
    });

    // 4. Exam Results / Cross Check (Student, Teacher, Administrator, Superuser)
    Route::middleware(['role:student,teacher,administrator,superuser'])->group(function () {
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
