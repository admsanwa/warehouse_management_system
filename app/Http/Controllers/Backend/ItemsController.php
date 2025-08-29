<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BarcodeModel;
use App\Models\ItemsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ItemsController extends Controller
{
    public function index(Request $request)
    {
        $user           = Auth::user()->username;
        $getRecord      = ItemsModel::getRecord($request);
        $addedBarcodes  = BarcodeModel::where('username', $user)->latest()->paginate(10);

        return view("backend.items.barcode", compact('getRecord', 'addedBarcodes'));
    }

    public function print(Request $request)
    {
        $user = Auth::user()->username;
        $addedBarcodes = BarcodeModel::where("username", $user)->get();

        if (empty($addedBarcodes)) {
            return redirect()->back()->with('error', 'Tidak ada barcodes yang dipilih untuk print');
        }

        return view('backend.items.print', compact('addedBarcodes'));
    }

    public function print_ppic(Request $request)
    {
        $user = Auth::user()->username;
        $addedBarcodes = BarcodeModel::where("username", $user)->get();

        if (empty($addedBarcodes)) {
            return redirect()->back()->with('error', 'Tidak ada barcodes yang dipilih untuk print');
        }

        return view('backend.items.printppic', compact('addedBarcodes'));
    }

    public function post(Request $request)
    {
        $user   = Auth::user()->username;
        $barcode = request()->validate([
            'code'  => 'required|string',
            'name'  => 'required|string',
            'qty'   => 'required|numeric|min:1',
        ]);

        $barcode        = new BarcodeModel();
        $barcode->code  = trim($request->code);
        $barcode->name  = trim($request->name);
        $barcode->username = $user;
        $barcode->qty   = trim($request->qty);
        $barcode->save();

        return redirect('admin/items/barcode')->with('success', "Succesfully add barcode {$barcode->name}");
    }

    public function delete($id)
    {
        $recordDelete = BarcodeModel::find($id);
        $recordDelete->delete();
        // dd($recordDelete);

        return redirect()->back()->with('error', 'Barcodes successfully delete');
    }

    public function deleteall()
    {
        $user = Auth::user()->username;
        $recordDelete = BarcodeModel::where("username", $user);
        $recordDelete->delete();

        return redirect()->back()->with('error', 'All Barcodes succesfully delete');
    }

    public function add()
    {
        return view("backend.items.add");
    }

    public function post_item(Request $request)
    {
        // dd($request->all());
        $items = request()->validate([
            'posting_date'  => 'required',
            'code'          => 'required|string',
            'name'          => 'required|string'
        ]);

        $items      = new ItemsModel();
        $items->posting_date    = trim($request->posting_date);
        $items->code            = trim($request->code);
        $items->name            = trim($request->name);
        $items->group           = trim($request->group);
        $items->uom             = trim($request->uom);
        $items->stock_min       = trim($request->stock_min);
        $items->stock_max       = trim($request->stock_max);
        $items->save();

        return redirect("admin/items/additem")->with('success', "Successfully add items {$items->name}");
    }

    public function list(Request $request)
    {
        $getRecord = ItemsModel::getRecord($request);
        return view("backend.items.list", compact('getRecord'));
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

                    ItemsModel::create([
                        'posting_date'  => Carbon::createFromFormat('d.m.Y', $row[1])->format('Y-m-d'),
                        'code'          => $row[1],
                        'name'          => $row[2],
                        'group'         => $row[3],
                        'uom'           => $row[4],
                        'stock_min'     => $row[5],
                        'stock_max'     => $row[6],
                        'is_active'     => $row[7],
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

        return back()->with('success', "Items Imported Succesfully");
    }
}
