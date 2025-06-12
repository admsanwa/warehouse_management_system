<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Backend\JobsHistoryController;
use App\Http\Controllers\Backend\JobsController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\EmployeesController;
use App\Http\Controllers\Backend\ItemsController;
use App\Http\Controllers\Backend\ProductionController;
use App\Http\Controllers\Backend\PurchasingController;
use App\Http\Controllers\Backend\StockController;
use App\Http\Controllers\Backend\TransactionController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Auth
Route::get('/', [AuthController::class, 'index']);
Route::get('forgot-password', [AuthController::class, 'forgot_password']);
Route::get('register', [AuthController::class, 'register']);
Route::post('register_post', [AuthController::class, 'register_post']);
Route::post('check_email', [AuthController::class, 'check_email']);
Route::post('login_post', [AuthController::class, 'login_post']);

Route::group(['middleware' => 'admin'], function () {
    Route::get('admin/dashboard', [DashboardController::class, 'dashboard']);
    Route::get('admin/employees', [EmployeesController::class, 'index']);
    Route::get('admin/employees/add', [EmployeesController::class, 'add']);
    Route::post('admin/employees/add', [EmployeesController::class, 'add_post']);
    Route::get('admin/employees/view/{id}', [EmployeesController::class, 'view']);
    Route::get('admin/employees/edit/{id}', [EmployeesController::class, 'edit']);
    Route::post('admin/employees/edit/{id}', [EmployeesController::class, 'update']);
    Route::get('admin/employees/delete/{id}', [EmployeesController::class, 'delete']);

    // JobsController
    Route::get('admin/jobs', [JobsController::class, 'index']);
    Route::get('admin/jobs_export', [JobsController::class, 'jobs_export']);

    // ItemsController
    Route::get('admin/items/barcode', [ItemsController::class, 'index']);
    Route::get('admin/items/print', [ItemsController::class, 'print']);
    Route::post('admin/items/add', [ItemsController::class, 'post']);
    Route::get('admin/items/delete/{id}', [ItemsController::class, 'delete']);
    Route::get('admin/items/deleteall', [ItemsController::class, 'deleteall']);
    Route::get('admin/items/additem', [ItemsController::class, 'add']);
    Route::post('admin/items/additem', [ItemsController::class, 'post_item']);
    Route::get('admin/items/list', [ItemsController::class, 'list']);
    Route::get('admin/items/trash/{id}', [ItemsController::class, 'trash']);
    Route::get('admin/items/edit/{id}', [ItemsController::class, 'edit']);
    Route::get('admin/items/upload', [ItemsController::class, 'upload']);

    // Purchasing
    Route::get('admin/purchasing', [PurchasingController::class, 'index']);
    Route::get('admin/purchasing/view/{id}', [PurchasingController::class, 'view']);
    Route::get('admin/purchasing/upload', [PurchasingController::class, 'upload_form']);
    Route::post('admin/purchasing/upload', [PurchasingController::class, 'upload']);

    // production
    Route::get('admin/production', [ProductionController::class, 'index']);
    Route::get('admin/production/view/{id}', [ProductionController::class, 'view']);
    Route::get('admin/production/upload', [ProductionController::class, 'upload_form']);
    Route::post('admin/production/upload', [ProductionController::class, 'upload']);

    // stock
    Route::get('admin/stock', [StockController::class, 'index']);

    // transaction
    Route::get('admin/transaction/stockin', [TransactionController::class, 'stock_in']);
    Route::post('admin/transaction/stockup', [TransactionController::class, 'stock_up']);
    Route::get('admin/transaction/stockdel/{id}', [TransactionController::class, 'stock_del']);
    Route::get('admin/transaction/stockdet/{grpo}', [TransactionController::class, 'stock_det']);
    Route::get('admin/transaction/stockout', [TransactionController::class, 'stock_out']);
    Route::post('/stockin-add', [TransactionController::class, 'scan_and_store']);
    Route::get('/scanned-barcodes/{grpo}', [TransactionController::class, 'getScannedBarcodes']);
});

Route::get('logout', [AuthController::class, 'logout']);
