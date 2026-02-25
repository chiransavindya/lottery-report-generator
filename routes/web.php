<?php

use App\Services\XmlParserService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes (public)
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Routes cleaned for production

// Authenticated routes - All roles
Route::middleware(['auth'])->group(function () {

    // Dashboard - All authenticated users
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Home/Dashboard
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    // About Page
    Route::get('/about', function () {
        return view('about');
    })->name('about');

    // Uploads - Operators and above
    Route::middleware(['role:operator,admin,super_admin'])->group(function () {
        Route::get('/uploads', [App\Http\Controllers\UploadController::class, 'index'])->name('uploads.index');
        Route::post('/uploads', [App\Http\Controllers\UploadController::class, 'store'])->name('uploads.store');
        Route::get('/uploads/{batch}', [App\Http\Controllers\UploadController::class, 'show'])->name('uploads.show');
    });


    // Batch deletion - Admins and above
    Route::middleware(['role:admin,super_admin'])->group(function () {
        Route::delete('/uploads/{batch}', [App\Http\Controllers\UploadController::class, 'destroy'])->name('uploads.destroy');
    });

    // Reports - Operators and above
    Route::middleware(['role:operator,admin,super_admin'])->group(function () {
        Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/create', [App\Http\Controllers\ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [App\Http\Controllers\ReportController::class, 'store'])->name('reports.store');
        Route::post('/reports/consolidated', [App\Http\Controllers\ReportController::class, 'generateConsolidated'])->name('reports.consolidated');
        // Specific routes MUST come before generic {report}/{language?} route
        Route::get('/reports/{report}/download-zip', [App\Http\Controllers\ReportController::class, 'downloadZip'])->name('reports.download.zip');
        Route::get('/reports/{report}/download-merged', [App\Http\Controllers\ReportController::class, 'downloadMerged'])->name('reports.download.merged');
        Route::get('/reports/{report}/preview/{language}', [App\Http\Controllers\ReportController::class, 'previewPdf'])->name('reports.preview');
        Route::get('/reports/{report}/download/{language}', [App\Http\Controllers\ReportController::class, 'download'])->name('reports.download');
        // Generic route MUST be last
        Route::get('/reports/{report}/{language?}', [App\Http\Controllers\ReportController::class, 'show'])->name('reports.show');
    });

    // Report management - Admins and above
    Route::middleware(['role:admin,super_admin'])->group(function () {
        Route::post('/reports/{report}/publish', [App\Http\Controllers\ReportController::class, 'publish'])->name('reports.publish');
        Route::delete('/reports/{report}', [App\Http\Controllers\ReportController::class, 'destroy'])->name('reports.destroy');
    });

    // User management - Super Admin only
    Route::middleware(['role:super_admin'])->group(function () {
        Route::resource('users', App\Http\Controllers\UserController::class);
    });
});
