<?php

use App\Http\Controllers\LeadController;
use App\Http\Controllers\AdminAuth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CustomFieldController;
use App\Http\Controllers\PublicLeadController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LeadCategoryController;
use Illuminate\Support\Facades\Route;

// CSRF token route
Route::get('/csrf-token', function () {
    return response()->json([
        'token' => csrf_token()
    ]);
})->middleware('web');

// Admin routes
Route::middleware('web')->group(function () {
    // Guest routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.login');
        });

        Route::get('admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
        Route::post('admin/login', [LoginController::class, 'login'])->name('admin.login.submit');
    });

    // Protected routes
    Route::middleware(['admin'])->group(function () {
        // Admin dashboard
        Route::get('/home', function () {
            return redirect()->route('admin.dashboard');
        });

        // Admin routes
        Route::prefix('admin')->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
            Route::post('logout', [LoginController::class, 'logout'])->name('admin.logout');
            Route::resource('custom-fields', CustomFieldController::class)->names('admin.custom-fields');
            Route::post('custom-fields/reorder', [CustomFieldController::class, 'reorder'])->name('admin.custom-fields.reorder');
            
            // User management routes
            Route::resource('users', UserController::class)->names('admin.users');

            // Lead category routes
            Route::resource('lead-categories', LeadCategoryController::class)->names('admin.lead-categories');
            
            // Leads routes
            Route::get('leads/embed', [LeadController::class, 'embed'])->name('admin.leads.embed');
            Route::get('leads/export', [LeadController::class, 'export'])->name('admin.leads.export');
            Route::post('leads/import', [LeadController::class, 'import'])->name('admin.leads.import');
            Route::resource('leads', LeadController::class)->names('admin.leads');
        });
    });
});

// Public lead form routes - these should be last to avoid conflicts
Route::middleware('web')->group(function () {
    Route::get('leads/form/{token?}', [PublicLeadController::class, 'form'])->name('public.leads.form');
    Route::post('leads/submit', [PublicLeadController::class, 'store'])->name('public.leads.store');
    Route::get('leads/embed.js', [PublicLeadController::class, 'script'])->name('public.leads.script');
});
