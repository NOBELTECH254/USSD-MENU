<?php

use App\Http\Controllers\Apps\PermissionManagementController;
use App\Http\Controllers\Apps\RoleManagementController;
use App\Http\Controllers\Apps\UserManagementController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ChartsController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\AccountSignupsController;
use App\Http\Controllers\UssdSessionsController;
use App\Http\Controllers\ResponseTemplatesController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', [DashboardController::class, 'index']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', [DashboardController::class, 'index'])->name('get_data');
    Route::get('/get_chart', [DashboardController::class, 'get_chart'])->name('get_chart');
    Route::get('/get_daily_dashboard', [DashboardController::class, 'get_daily_dashboard'])->name('get_daily_dashboard');
    Route::get('/get_week_dashboard', [DashboardController::class, 'get_week_dashboard'])->name('get_week_dashboard');
    Route::get('/get_month_dashboard', [DashboardController::class, 'get_month_dashboard'])->name('get_month_dashboard');

    Route::name('user-management.')->group(function () {
        Route::resource('/user-management/users', UserManagementController::class);
        Route::resource('/user-management/roles', RoleManagementController::class);
        Route::resource('/user-management/permissions', PermissionManagementController::class);
    });
    
    Route::resource('profiles', ProfilesController::class);
    
    Route::post('profiles/{uuid}/reset', [ProfilesController::class, 'reset'])->name('profiles.reset');
    Route::patch('profiles/{uuid}/update-status', [ProfilesController::class, 'update_status'])
    ->name('profiles.update-status');
    Route::resource('messages', MessagesController::class);
    Route::resource('account-signups', AccountSignupsController::class);
    Route::resource('ussd-sessions', UssdSessionsController::class);
    Route::get('/menu-requests', [UssdSessionsController::class, 'menu_requests'])->name('menu-requests');
    Route::get('/menu-requests/{uuid}/show', [UssdSessionsController::class, 'menu_requests_show'])->name('menu-requests.show');

    Route::resource('response-templates', ResponseTemplatesController::class);
Route::patch('response-templates/{uuid}/update-status', [ResponseTemplatesController::class, 'update_status'])
    ->name('response-templates.update-status');

    Route::get('/charts', [ChartsController::class, 'index'])->name('charts');

});

Route::get('/error', function () {
    abort(500);
});

Route::get('/auth/redirect/{provider}', [SocialiteController::class, 'redirect']);

require __DIR__ . '/auth.php';
