<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ReportController;
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

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('members', MemberController::class);
    Route::post('/members/import', [MemberController::class, 'import'])->name('members.import');
    Route::get('/members-export/excel', [MemberController::class, 'exportExcel'])->name('members.export.excel');
    Route::get('/members-export/pdf', [MemberController::class, 'exportPdf'])->name('members.export.pdf');
    Route::get('/categories/{category}/export/excel', [CategoryController::class, 'exportExcel'])->name('categories.export.excel');
    Route::resource('categories', CategoryController::class);

    Route::resource('attendances', AttendanceController::class)->except(['show']);
    Route::view('/notifications', 'notifications.index')->name('notifications.index');
    Route::view('/settings', 'settings.index')->name('settings.index');

    Route::middleware('role:admin,pendeta,koordinator')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });
});
