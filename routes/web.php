<?php

use Illuminate\Support\Facades\Route;

// Dashboard - root redirect
Route::redirect('/', '/dashboard');

Route::view('/dashboard', 'pages.dashboard')->name('dashboard');

Route::prefix('error')->group(function () {
    Route::view('400', 'errors.400')->name('error.page.400');
    Route::view('401', 'errors.401')->name('error.page.401');
    Route::view('403', 'errors.403')->name('error.page.403');
    Route::view('404', 'errors.404')->name('error.page.404');
    Route::view('500', 'errors.500')->name('error.page.500');
    Route::view('503', 'errors.503')->name('error.page.503');
});