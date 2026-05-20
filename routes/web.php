<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionPackageController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ExamController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Dashboard & root
Route::redirect('/', '/dashboard');
Route::view('/dashboard', 'pages.dashboard')->name('dashboard');

// Mock Auth system for development ease of use
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('auth.mock-login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $role = $request->input('role', 'student');
    $user = User::where('role', $role)->first();
    if (!$user) {
        $user = User::factory()->create([
            'name' => ucfirst($role) . ' User',
            'email' => $role . '@example.com',
            'role' => $role
        ]);
    }
    Auth::login($user);
    return redirect('/dashboard')->with('success', "Berhasil masuk sebagai {$role}!");
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login')->with('success', 'Berhasil keluar!');
})->name('logout');

// Question Management Routes (Teacher / Admin / Student)
Route::middleware(['auth'])->group(function () {
    // Question Packages CRUD
    Route::resource('question-packages', QuestionPackageController::class);
    Route::post('question-packages/{questionPackage}/toggle-publish', [QuestionPackageController::class, 'togglePublish'])
        ->name('question-packages.toggle-publish');

    // Questions CRUD (Nested under Question Packages)
    Route::resource('question-packages.questions', QuestionController::class)->shallow();

    // CBT / Exam Routes (Student)
    Route::get('exams', [ExamController::class, 'index'])->name('exams.index');
    Route::post('exams/packages/{questionPackage}/start', [ExamController::class, 'start'])->name('exams.start');
    Route::get('exams/attempt/{questionAttempt}', [ExamController::class, 'attempt'])->name('exams.attempt');
    Route::post('exams/attempt/{questionAttempt}/save', [ExamController::class, 'saveResponse'])->name('exams.save-response');
    Route::post('exams/attempt/{questionAttempt}/submit', [ExamController::class, 'submit'])->name('exams.submit');
    Route::get('exams/results/{questionAttempt}', [ExamController::class, 'results'])->name('exams.results');
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