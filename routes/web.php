<?php

use App\Http\Controllers\SuperAdmin\AuthController;
use App\Http\Controllers\SuperAdmin\TenantController;
use App\Http\Controllers\DevController;
use App\Services\WizardService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Central Web Routes (Super Admin)
|--------------------------------------------------------------------------
|
| These routes are only accessible from the central domain (localhost).
| Tenant subdomains are handled in routes/tenant.php.
|
*/

Route::prefix('super-admin')->name('super-admin.')->group(function () {
    // Auth
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Tenant management (protected)
    Route::middleware('super-admin.auth')->group(function () {
        Route::get('/', [TenantController::class, 'index'])->name('dashboard');
        Route::get('tenants/create', [TenantController::class, 'create'])->name('tenants.create');
        Route::post('tenants', [TenantController::class, 'store'])->name('tenants.store');
        Route::delete('tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');
    });
});

// Redirect root to super-admin
Route::get('/', fn() => redirect('/super-admin'));

if (env('APP_DEBUG')) {
    Route::get('__vite_ping', function () {
        $filePath = base_path('public/hot');
        if (file_exists($filePath)) {
            return redirect(file_get_contents($filePath) . '/__vite_ping');
        }
    });

    Route::get('__dev__', [DevController::class, 'index']);
}
