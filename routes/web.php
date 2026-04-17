<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AnggotaKeluargaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JemaatDashboardController;
use App\Http\Controllers\JemaatController;
use App\Http\Controllers\JemaatRegistrationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view_dashboard')
        ->name('dashboard');

    Route::resource('members', MemberController::class);
    Route::post('/members/import', [MemberController::class, 'import'])
        ->middleware('permission:import_members')
        ->name('members.import');
    Route::get('/members-export/excel', [MemberController::class, 'exportExcel'])
        ->middleware('permission:export_members')
        ->name('members.export.excel');
    Route::get('/members-export/pdf', [MemberController::class, 'exportPdf'])
        ->middleware('permission:export_members')
        ->name('members.export.pdf');

    Route::get('/categories', [CategoryController::class, 'index'])
        ->middleware('permission:view_categories')
        ->name('categories.index');

    Route::resource('attendances', AttendanceController::class)->except(['show']);
    Route::view('/notifications', 'notifications.index')->name('notifications.index');
    Route::view('/settings', 'settings.index')
        ->middleware('permission:view_settings')
        ->name('settings.index');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    Route::resource('users', UserController::class)->except(['show']);

    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware(['role:Admin', 'permission:assign_roles'])
        ->name('roles.index');

    Route::prefix('jemaat')
        ->name('jemaat.')
        ->middleware('role:Jemaat Gereja')
        ->group(function () {
            Route::get('/dashboard', [JemaatDashboardController::class, 'dashboard'])->name('dashboard');
            Route::get('/profile/show', [JemaatController::class, 'showProfile'])->name('profile.show');
            Route::get('/profile', [JemaatDashboardController::class, 'profile'])->name('profile');
            Route::post('/profile/update', [JemaatDashboardController::class, 'updateProfile'])->name('profile.update.post');
            Route::get('/family', [JemaatDashboardController::class, 'family'])->name('family');
            Route::post('/family/store', [JemaatDashboardController::class, 'storeFamily'])->name('family.store');
            Route::post('/family/{id}/update', [JemaatDashboardController::class, 'updateFamily'])->name('family.update');
            Route::delete('/family/{id}', [JemaatDashboardController::class, 'deleteFamily'])->name('family.delete');

            Route::get('/registration/step/{step}', [JemaatRegistrationController::class, 'showStep'])->name('registration.show');
            Route::post('/registration/step/{step}', [JemaatRegistrationController::class, 'saveStep'])->name('registration.save');
            Route::post('/registration/draft', [JemaatRegistrationController::class, 'saveDraft'])->name('registration.draft');

            Route::get('/keluarga', [AnggotaKeluargaController::class, 'index'])->name('keluarga.index');
            Route::get('/keluarga/create', [AnggotaKeluargaController::class, 'create'])->name('keluarga.create');
            Route::post('/keluarga', [AnggotaKeluargaController::class, 'store'])->name('keluarga.store');
            Route::get('/keluarga/{id}/edit', [AnggotaKeluargaController::class, 'edit'])->name('keluarga.edit');
            Route::put('/keluarga/{id}', [AnggotaKeluargaController::class, 'update'])->name('keluarga.update');
            Route::delete('/keluarga/{id}', [AnggotaKeluargaController::class, 'destroy'])->name('keluarga.destroy');
        });
});
