
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AdminController;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'loginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'registerForm'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register']);

});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('transactions', TransactionController::class)
        ->except(['show']);

    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/reports/print', [ReportController::class, 'print'])
        ->name('reports.print');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('settings.index');

    Route::post('/settings', [SettingsController::class, 'update'])
        ->name('settings.update');

    Route::resource('savings-goals', SavingsGoalController::class);
    Route::post('savings-goals/{savingsGoal}/deposit', [SavingsGoalController::class, 'addDeposit'])
        ->name('savings-goals.deposit');
    Route::post('savings-goals/{savingsGoal}/withdrawal', [SavingsGoalController::class, 'addWithdrawal'])
        ->name('savings-goals.withdrawal');

});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
});
