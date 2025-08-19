<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ItemsMaklonModel;
use App\Models\PurchaseOrderDetailsModel;
use App\Models\PurchasingModel;
use App\Models\StockModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PurchasingController extends Controller
{
    public function index(Request $request)
    {
        $getRecord      = PurchasingModel::with("po_details")->get()->values();
        $getRecordTwo   = PurchasingModel::with("maklon_details")->get()->values();
        // $getRecord      = PurchaseOrderDetailsModel::with("stocks")->get()->unique("nopo")->values();
        $getPagination  = PurchasingModel::getRecord($request);

        $purchasingSummary = [];
        $purchasingSummaryTwo = [];
        foreach ($getRecord as $record) {
            $po = $record->no_po;
            $purchaseQty = PurchaseOrderDetailsModel::where("nopo", $po)->sum("qty");
            $stockInQty = StockModel::where("no_po", $po)->sum("qty");

            $purchasingSummary[$po] = [
                'remain' => $purchaseQty - $stockInQty
            ];
        }
        foreach ($getRecordTwo as $record) {
            $po             = $record->no_po;
            $purchaseQty    = PurchaseOrderDetailsModel::where("nopo", $po)->sum("qty");
            $goodReceipt    = ItemsMaklonModel::where("po", $po)
                ->where(function ($query) {
                    $query->whereNotNull("gr")->where("gr", "<>", 0);
                })->sum("qty");

            $purchasingSummaryTwo[$po] = [
                'remain' => $purchaseQty - $goodReceipt
            ];
        }

        return view("backend.purchasing.list", compact('getRecord', 'getPagination', 'purchasingSummary', 'purchasingSummaryTwo'));
    }

    public function view(Request $request, $id)
    {
        $getRecord  = PurchasingModel::find($id);
        $getPO      = PurchasingModel::where("id", $id)->value("no_po");
        $getData    = PurchaseOrderDetailsModel::where("nopo", $getPO)->get();
        return view("backend.purchasing.view", compact('getRecord', 'getData', 'getPO'));
    }

    public function upload_form()
    {
        return view("backend.purchasing.upload");
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv'
        ]);

        $path = $request->file('file')->storeAs('uploads', $request->file('file')->getClientOriginalName());
        $fullPath = storage_path('app/' . $path);
        $data = array_map('str_getcsv', file($fullPath));

        // dd($data);
        // dd(DB::connection()->getDatabaseName());
        DB::beginTransaction();
        try {
            foreach ($data as $index => $row) {
                if ($index === 0 && is_string($row[0])) {
                    continue;
                }

                try {
                    if (count($row) < 11) {
                        throw new \Exception("Row $index has insufficient columns.");
                    }

                    PurchasingModel::create([
                        'no_po'           => $row[0],
                        'vendor'          => $row[1],
                        'contact_person'  => $row[2],
                        'buyer'           => $row[3],
                        'posting_date' => Carbon::createFromFormat('d.m.Y', $row[4])->format('Y-m-d'),
                        'status'          => $row[5],
                        'item_code'       => $row[6],
                        'item_type'       => $row[7],
                        'item_desc'       => $row[8],
                        'qty' => str_replace(',', '', $row[9]),
                        'uom'             => $row[10],
                    ]);
                } catch (\Exception $e) {
                    dd("Error in row $index:", $row, $e->getMessage());
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Upload failed at row $index: " . $e->getMessage());
            Log::error($e); // logs stack trace too
            return back()->with('error', "Upload failed at row $index. " . $e->getMessage());
        }

        return back()->with('success', "POs Imported Succesfully");
    }
}
