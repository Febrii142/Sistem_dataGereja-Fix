<?php

use App\Http\Controllers\AdminApprovalController;
use App\Http\Controllers\AnggotaKeluargaController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JemaatDashboardController;
use App\Http\Controllers\JemaatRegistrationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [RegistrationController::class, 'create'])->name('register');
    Route::get('/register/success', [RegistrationController::class, 'success'])->name('register.success');
    Route::post('/register', [RegistrationController::class, 'store'])->name('register.store');
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

    Route::prefix('admin/registrations')
        ->middleware('permission:view_users')
        ->group(function () {
            Route::get('/pending', [AdminApprovalController::class, 'pending'])->name('admin.registrations.pending');
            Route::post('/{user}/approve', [AdminApprovalController::class, 'approve'])
                ->middleware('permission:edit_users')
                ->name('admin.registrations.approve');
            Route::post('/{user}/reject', [AdminApprovalController::class, 'reject'])
                ->middleware('permission:edit_users')
                ->name('admin.registrations.reject');
        });

    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware(['role:Admin', 'permission:assign_roles'])
        ->name('roles.index');

    Route::prefix('jemaat')
        ->name('jemaat.')
        ->middleware('role:Jemaat Gereja')
        ->group(function () {
            Route::get('/dashboard', [JemaatDashboardController::class, 'dashboard'])->name('dashboard');
            Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
            Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
            Route::get('/profile/edit', [ProfileController::class, 'edit'])->middleware('approved')->name('profile.edit');
            Route::post('/profile', [ProfileController::class, 'update'])->middleware('approved')->name('profile.update');
            Route::get('/family', [JemaatDashboardController::class, 'family'])->name('family');
            Route::post('/family/store', [JemaatDashboardController::class, 'storeFamily'])->middleware('approved')->name('family.store');
            Route::post('/family/{id}/update', [JemaatDashboardController::class, 'updateFamily'])->middleware('approved')->name('family.update');
            Route::delete('/family/{id}', [JemaatDashboardController::class, 'deleteFamily'])->middleware('approved')->name('family.delete');

            Route::get('/registration/step/{step}', [JemaatRegistrationController::class, 'showStep'])->name('registration.show');
            Route::post('/registration/step/{step}', [JemaatRegistrationController::class, 'saveStep'])->middleware('approved')->name('registration.save');
            Route::post('/registration/draft', [JemaatRegistrationController::class, 'saveDraft'])->middleware('approved')->name('registration.draft');

            Route::get('/keluarga', [AnggotaKeluargaController::class, 'index'])->name('keluarga.index');
            Route::get('/keluarga/create', [AnggotaKeluargaController::class, 'create'])->middleware('approved')->name('keluarga.create');
            Route::post('/keluarga', [AnggotaKeluargaController::class, 'store'])->middleware('approved')->name('keluarga.store');
            Route::get('/keluarga/{id}/edit', [AnggotaKeluargaController::class, 'edit'])->middleware('approved')->name('keluarga.edit');
            Route::put('/keluarga/{id}', [AnggotaKeluargaController::class, 'update'])->middleware('approved')->name('keluarga.update');
            Route::delete('/keluarga/{id}', [AnggotaKeluargaController::class, 'destroy'])->middleware('approved')->name('keluarga.destroy');
        });
});
