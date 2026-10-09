<?php

use App\Enums\RoleName;
use App\Http\Controllers\ApplicationAttachmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Livewire\Student\Applications\Create as ApplicationsCreate;
use App\Livewire\Student\Applications\Index as ApplicationsIndex;
use App\Livewire\Student\Applications\Show as ApplicationsShow;
use App\Livewire\Student\Profile as StudentProfile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('applications/attachments/{attachment}', [ApplicationAttachmentController::class, 'show'])
        ->name('applications.attachments.show');
});

Route::middleware(['auth', 'verified', 'role:'.RoleName::Student->value])
    ->prefix('student')->name('student.')
    ->group(function () {
        Route::get('dashboard', StudentDashboardController::class)->name('dashboard');
        Route::livewire('profile', StudentProfile::class)->name('profile');

        Route::livewire('applications', ApplicationsIndex::class)->name('applications.index');
        Route::livewire('applications/create', ApplicationsCreate::class)->name('applications.create');
        Route::livewire('applications/{application}', ApplicationsShow::class)->name('applications.show');
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
