<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Backend\JobsHistoryController;
use App\Http\Controllers\Backend\JobsController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\DeliveryController;
use App\Http\Controllers\Backend\EmployeesController;
use App\Http\Controllers\Backend\InventorytfController;
use App\Http\Controllers\Backend\ItemsController;
use App\Http\Controllers\Backend\ListTransactionsController;
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
    // dashboard
    Route::get('admin/dashboard-list', [DashboardController::class, 'dashboard_plan']);
    Route::get('admin/dashboard', [DashboardController::class, 'dashboard']);
    Route::get('admin/dashboard/minstock', [DashboardController::class, 'min_stock']);
    Route::get('admin/dashboard/aftercheck', [DashboardController::class, 'after_check']);
    Route::get('admin/dashboard/delivstatus', [DashboardController::class, 'deliv_status']);
    Route::get('admin/dashboard/ifp', [DashboardController::class, 'prod_release']);
    Route::get('admin/dashboard/goodreceiptpo', [DashboardController::class, 'grpo']);
    Route::get('admin/dashboard/goodissued', [DashboardController::class, 'good_issued']);
    Route::get('admin/dashboard/goodreceipt', [DashboardController::class, 'good_receipt']);
    Route::get('admin/dashboard/rfp', [DashboardController::class, 'receipt_from_prod']);
    Route::get('admin/production/clear-bon-notif', [DashboardController::class, 'clearBonNotif']);
    Route::get('admin/production/clear-memo-notif', [DashboardController::class, 'clearMemoNotif']);
    Route::get('admin/production/clear-qc-notif', [DashboardController::class, 'clearQcNotif']);
    Route::get('admin/listtransaction/clear-grpo-notif', [DashboardController::class, 'clearGrpoNotif']);
    Route::get('admin/listtransaction/clear-gr-notif', [DashboardController::class, 'clearGrNotif']);
    Route::get('admin/delivery/clear-delivery-notif', [DashboardController::class, 'clearDeliveryNotif']);

    // employee
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
    Route::get('admin/items/printppic', [ItemsController::class, 'print_ppic']);
    Route::post('admin/items/add', [ItemsController::class, 'post']);
    Route::get('admin/items/delete/{id}', [ItemsController::class, 'delete']);
    Route::get('admin/items/deleteall', [ItemsController::class, 'deleteall']);
    Route::get('admin/items/additem', [ItemsController::class, 'add']);
    Route::post('admin/items/additem', [ItemsController::class, 'post_item']);
    Route::get('admin/items/list', [ItemsController::class, 'list']);
    Route::get('admin/items/upload', [ItemsController::class, 'upload']);
    Route::get('/print/barcodes/pdf', [ItemsController::class, 'printBarcodeWithPdf']);
    // Purchasing
    Route::get('admin/purchasing_old', [PurchasingController::class, 'old_index']);
    Route::get('admin/purchasing/view_old/{id}', [PurchasingController::class, 'old_view']);


    Route::get('admin/purchasing/purchaseorder', [PurchasingController::class, 'index']);
    Route::get('admin/purchasing/view', [PurchasingController::class, 'view']);
    Route::get('admin/purchasing/upload', [PurchasingController::class, 'upload_form']);
    Route::post('admin/purchasing/upload', [PurchasingController::class, 'upload']);
    // currently update
    Route::get('admin/purchasing/barcode', [PurchasingController::class, 'barcode']);
    Route::get('admin/purchasing/barcode/{docEntry}', [PurchasingController::class, 'barcode_po']);
    Route::get('/print/barcodes/pdfpo', [PurchasingController::class, 'printBarcodeWithPdf']);
    Route::post('/print/pdfmaklon', [PurchasingController::class, 'printBarcodeWithPdfMaklon']);

    // bon
    Route::get('admin/production/bon', [ProductionController::class, 'bon']);
    Route::post('admin/production/bon', [ProductionController::class, 'create_bon']);
    Route::get('admin/production/bonedit/{id}', [ProductionController::class, 'edit_bon']);
    Route::post('admin/production/updatebon/{id}', [ProductionController::class, 'update_bon']);
    Route::get('admin/production/bondelete/{id}', [ProductionController::class, 'bon_delete']);
    Route::get('admin/production/listbon', [ProductionController::class, 'list_bon']);
    Route::get('admin/production/bondetails/{id}', [ProductionController::class, 'bon_details']);
    Route::post('/approve-bon', [ProductionController::class, 'approve_bon']);
    // memo
    Route::get('admin/production/memo', [ProductionController::class, 'memo']);
    Route::post('admin/production/memo', [ProductionController::class, 'create_memo']);
    Route::get('admin/production/memoedit/{id}', [ProductionController::class, 'edit_memo']);
    Route::post('admin/production/memoupdate/{id}', [ProductionController::class, 'update_memo']);
    Route::get('admin/production/memodelete/{id}', [ProductionController::class, 'delete_memo']);
    Route::get('admin/production/listmemo', [ProductionController::class, 'list_memo']);
    Route::get('admin/production/memodetails/{id}', [ProductionController::class, 'detail_memo']);
    Route::post('/approve-memo', [ProductionController::class, 'approve']);
    // production order
    Route::get("admin/production/barcode", [ProductionController::class, "barcode"]);
    Route::get("admin/production/add", [ProductionController::class, "add_print"]);
    Route::get("admin/production/print", [ProductionController::class, "print"]);
    Route::get("admin/production/delete/{id}", [ProductionController::class, "delete"]);
    Route::get("admin/production/deleteall", [ProductionController::class, "deleteall"]);
    Route::get('admin/production/po', [ProductionController::class, 'index']);
    Route::get('admin/production/view', [ProductionController::class, 'view']);
    // Route::get('admin/production/view/{id}', [ProductionController::class, 'view']);
    Route::get('admin/production/upload', [ProductionController::class, 'upload_form']);
    Route::post('admin/production/upload', [ProductionController::class, 'upload']);
    Route::get('admin/production/{prod_no}', [ProductionController::class, 'view_prod']);
    Route::get('/print/barcodes/prod', [ProductionController::class, 'printBarcodeWithPdf']);
    Route::get('/preparematerial', [ProductionController::class, 'preparemat_form']);
    Route::post('/preparematerial', [ProductionController::class, 'create_preparemat']);
    Route::get("/listpreparemat", [ProductionController::class, 'list_preparemat']);
    Route::get("preparematdetails/{docEntry}", [ProductionController::class, 'preparemat_details']);
    Route::post("preparematdetails/{docEntry}", [ProductionController::class, 'update_preparemat']);
    Route::post('insert-po', [ProductionController::class, 'insert_po'])->name('insert.po');

    // qc
    Route::get("admin/quality/list", [QualityController::class, "index"]);
    Route::post("admin/quality/{docentry}", [QualityController::class, "result"]);
    Route::post("admin/quality/{docEntry}/{itemCode}", [QualityController::class, "result_two"]);
    Route::get("admin/quality/history", [QualityController::class, "history"]);

    // stock
    Route::get('admin/stock', [StockController::class, 'index']);

    // inventorytf
    Route::get('admin/inventorytf/create', [InventorytfController::class, 'create']);
    Route::post('admin/inventorytf/transfer', [InventorytfController::class, 'transfer']);
    Route::get('admin/inventorytf/list', [InventorytfController::class, 'list']);
    Route::get('admin/inventorytf/view', [InventorytfController::class, 'view']);

    // transaction
    // stockin
    Route::get('/scanned-barcodes/{grpo}', [TransactionController::class, 'getScannedBarcodes']);
    Route::post('/stockin-add', [TransactionController::class, 'scan_and_store']);
    Route::get('/grpo-histories', [TransactionController::class, 'grpo_histories']);
    Route::get('admin/transaction/stockin', [TransactionController::class, 'stock_in']);
    // Route::get('admin/transaction/stockin/{po}', [TransactionController::class, 'stockin_po']);
    Route::post('admin/transaction/stockup', [TransactionController::class, 'stock_up']);
    Route::get('admin/transaction/stockdel/{grpo}', [TransactionController::class, 'stock_del']);
    Route::post("admin/transaction/stockindelone/{id}", [TransactionController::class, 'stockin_delone']);
    Route::get('admin/transaction/stockdet/{grpo}', [TransactionController::class, 'stock_det']);
    // stockout    
    Route::get('admin/transaction/stockout', [TransactionController::class, 'stock_out']);
    Route::get("admin/transaction/stockout/{prod_order}", [TransactionController::class, 'stockout_po']);
    Route::post("/stockout-issued", [TransactionController::class, "scan_and_issued"]);
    Route::get("/scanned-barcodes-out/{isp}", [TransactionController::class, "getScanOut"]);
    Route::post("/save_prod_issue", [TransactionController::class, "save_production_issue"]);
    Route::get("admin/transaction/stockoutdet/{isp}", [TransactionController::class, "stockout_det"]);
    Route::get("admin/transaction/stockoutdel/{isp}", [TransactionController::class, "stockout_del"]);
    Route::post("admin/transaction/stockoutdelone/{id}", [TransactionController::class, "stockout_delone"]);
    // receipt from prod
    Route::get("admin/transaction/rfp", [TransactionController::class, "receipt_from_prod"]);
    Route::post("/rfp-add", [TransactionController::class, "scan_and_receipt"]);
    Route::post("/save_prod_receipt", [TransactionController::class, "save_prod_receipt"]);
    Route::get("/scanned-barcodes-rfp/{number}", [TransactionController::class, "getScannedRfp"]);
    Route::post("admin/transaction/rfpup", [TransactionController::class, "rfp_update"]);
    Route::post("admin/transaction/rfpdelone/{id}", [TransactionController::class, "rfp_delone"]);
    Route::get("admin/transaction/rfpdetail/{number}", [TransactionController::class, "rfp_detail"]);
    Route::get("admin/transaction/rfpdelete/{number}", [TransactionController::class, "rfp_delete"]);
    // goodissue
    Route::post("admin/transaction/giup", [TransactionController::class, "gi_update"]);
    Route::get("/scanned-barcodes-gi/{gi}", [TransactionController::class, "get_scanned_gi"]);
    Route::get("admin/transaction/goodissued", [TransactionController::class, 'good_issued']);
    Route::post("/save_gi", [TransactionController::class, 'save_good_issue']);
    Route::post("/good-issued", [TransactionController::class, "scan_and_out"]);
    Route::post("admin/transaction/gidelone/{id}", [TransactionController::class, "gi_delone"]);
    Route::get("admin/transaction/gidetail/{gi}", [TransactionController::class, "gi_detail"]);
    Route::get("admin/transaction/gidelete/{gi}", [TransactionController::class, "gi_delete"]);
    // goodreceipt
    Route::get("admin/transaction/goodreceipt", [TransactionController::class, "good_receipt"]);
    Route::post("/save_gr", [TransactionController::class, 'save_good_receipt']);
    Route::post("/post_gr", [TransactionController::class, 'post_good_receipt']);
    Route::post("/good-receipt", [TransactionController::class, "scan_and_greceipt"]);
    Route::get("/scanned-barcodes-gr/{gr}", [TransactionController::class, "get_scanned_gr"]);
    Route::post("admin/transaction/grdelone/{id}", [TransactionController::class, "gr_delone"]);
    Route::post("admin/transaction/grupdate", [TransactionController::class, "gr_update"]);
    Route::get("admin/transaction/grdetail/{gr}", [TransactionController::class, "gr_detail"]);
    Route::get("admin/transaction/grdelete/{gr}", [TransactionController::class, "gr_delete"]);
    Route::get("admin/transaction/listgoodreceipt", [TransactionController::class, "list_gr"]);
    Route::get("admin/transaction/postgoodreceipt/{doc_entry}", [TransactionController::class, "post_gr"]);

    // list transactions
    Route::get("admin/listtransaction/stockin", [ListTransactionsController::class, "stock_in"]);
    Route::get("admin/listtransaction/stockout", [ListTransactionsController::class, "stock_out"]);
    Route::get("admin/listtransaction/rfp", [ListTransactionsController::class, "rfp"]);
    Route::get("admin/listtransaction/goodissued", [ListTransactionsController::class, "goodissue"]);
    Route::get("admin/listtransaction/goodreceipt", [ListTransactionsController::class, "goodreceipt"]);
    Route::post("/update-grpo-temp", [ListTransactionsController::class, "updateGRPOTemp"]);
    Route::post("/update-gr-temp", [ListTransactionsController::class, "updateGRTemp"]);

    // Reports
    Route::get("admin/reports/finishgoods", [ReportsController::class, "finish_goods"]);
    Route::get("admin/reports/semifg", [ReportsController::class, "semifg"]);
    Route::get("/reports-bon", [ReportsController::class, 'bon']);
    Route::post('/reports-bon-data', [ReportsController::class, 'data'])->name('report.bon.data');
    Route::get("/reports-memo", [ReportsController::class, 'memo']);
    Route::post('/reports-memo-data', [ReportsController::class, 'memo_data'])->name('report.memo.data');

    // delivery
    Route::get("admin/delivery/list", [DeliveryController::class, "index"]);
    Route::post("admin/delivery/estimate/{docEntry}", [DeliveryController::class, "estimate"]);
    Route::get("admin/delivery/history", [DeliveryController::class, "history"]);
    Route::post("/update-delivery-temp", [DeliveryController::class, "updateDeliveryTemp"]);

    // get data api
    Route::get('/purchasing/seriesSearch', [PurchasingController::class, 'series_search']);
    Route::get('/purchaseOrderSearch', [PurchasingController::class, 'po_search']);
    Route::get('/purchaseOrderSearchAll', [PurchasingController::class, 'po_search_all']);
    Route::get('/productionOrderSearch', [ProductionController::class, 'prod_search']);
    Route::get('/warehouseSearch', [ItemsController::class, 'warehouse_search']);
    Route::get('/warehouseSearchCustom', [ItemsController::class, 'warehouse_search_custom']);
    Route::get('/costCenterSearch', [ItemsController::class, 'cost_center_search']);
    Route::get('/projectSearch', [ItemsController::class, 'project_search']);
    Route::get('/onhandSearch', [ItemsController::class, 'onhand_search']);
    Route::get('/salesOrderSearch', [InventorytfController::class, 'so_search']);
});

Route::get('logout', [AuthController::class, 'logout']);

use App\Notifications\MailMemoApproval;

Route::get('/send-test-mail', function () {
    Notification::route('mail', 'danyxdevelop@gmail.com')
        ->notify(new MailMemoApproval(
            "Dany",
            "BON-12345",
            url('/memo/12345/approve')
        ));

    return 'Mail sent!';
});
