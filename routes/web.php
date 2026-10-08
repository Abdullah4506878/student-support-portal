<?php

use App\Enums\RoleName;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:'.RoleName::Student->value])
    ->prefix('student')->name('student.')
    ->group(function () {
        Route::view('dashboard', 'student.dashboard')->name('dashboard');
    });

Route::middleware(['auth', 'verified', 'role:'.RoleName::AdminOfficer->value])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::view('dashboard', 'admin.dashboard')->name('dashboard');
    });

Route::middleware(['auth', 'verified', 'role:'.RoleName::SuperAdmin->value])
    ->prefix('super-admin')->name('super-admin.')
    ->group(function () {
        Route::view('dashboard', 'super-admin.dashboard')->name('dashboard');
    });

require __DIR__.'/settings.php';
