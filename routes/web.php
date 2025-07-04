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
use App\Http\Controllers\Backend\QualityController;
use App\Http\Controllers\Backend\ReportsController;
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
    Route::get('admin/production/{prod_no}', [ProductionController::class, 'view_prod']);

    // qc
    Route::get("admin/quality/list", [QualityController::class, "index"]);
    Route::post("admin/quality/{prod_no}", [QualityController::class, "result"]);
    Route::get("admin/quality/barcode", [QualityController::class, "barcode"]);
    Route::get("admin/quality/add", [QualityController::class, "add_print"]);
    Route::get("admin/quality/delete/{id}", [QualityController::class, "delete"]);
    Route::get("admin/quality/deleteall", [QualityController::class, "deleteall"]);
    Route::get("admin/quality/print", [QualityController::class, "print"]);

    // stock
    Route::get('admin/stock', [StockController::class, 'index']);

    // transaction
    // stockin
    Route::get('admin/transaction/stockin', [TransactionController::class, 'stock_in']);
    Route::get('admin/transaction/stockin/{po}', [TransactionController::class, 'stockin_po']);
    Route::post('admin/transaction/stockup', [TransactionController::class, 'stock_up']);
    Route::get('admin/transaction/stockdel/{grpo}', [TransactionController::class, 'stock_del']);
    Route::post("admin/transaction/stockindelone/{id}", [TransactionController::class, 'stockin_delone']);
    Route::get('admin/transaction/stockdet/{grpo}', [TransactionController::class, 'stock_det']);
    Route::post('/stockin-add', [TransactionController::class, 'scan_and_store']);
    Route::get('/scanned-barcodes/{grpo}', [TransactionController::class, 'getScannedBarcodes']);
    // stockout    
    Route::get('admin/transaction/stockout', [TransactionController::class, 'stock_out']);
    Route::get("admin/transaction/stockout/{prod_order}", [TransactionController::class, 'stockout_po']);
    Route::post("/stockout-issued", [TransactionController::class, "scan_and_issued"]);
    Route::get("/scanned-barcodes-out/{isp}", [TransactionController::class, "getScanOut"]);
    Route::post("admin/transaction/stockoutup", [TransactionController::class, "stockout_up"]);
    Route::get("admin/transaction/stockoutdet/{isp}", [TransactionController::class, "stockout_det"]);
    Route::get("admin/transaction/stockoutdel/{isp}", [TransactionController::class, "stockout_del"]);
    Route::post("admin/transaction/stockoutdelone/{id}", [TransactionController::class, "stockout_delone"]);
    // receipt from prod
    Route::get("admin/transaction/rfp", [TransactionController::class, "receipt_from_prod"]);
    Route::post("/rfp-add", [TransactionController::class, "scan_and_receipt"]);
    Route::get("/scanned-barcodes-rfp/{number}", [TransactionController::class, "getScannedRfp"]);
    Route::post("admin/transaction/rfpup", [TransactionController::class, "rfp_update"]);
    Route::post("admin/transaction/rfpdelone/{id}", [TransactionController::class, "rfp_delone"]);
    Route::get("admin/transaction/rfpdetail/{number}", [TransactionController::class, "rfp_detail"]);
    Route::get("admin/transaction/rfpdelete/{number}", [TransactionController::class, "rfp_delete"]);

    // Reports
    Route::get("admin/reports/finishgoods", [ReportsController::class, "finish_goods"]);
});

Route::get('logout', [AuthController::class, 'logout']);
