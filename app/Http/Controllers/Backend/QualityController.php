<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\barcodeQualityModel;
use App\Models\ProductionModel;
use App\Models\QualityModel;
use Auth;
use Illuminate\Http\Request;

class QualityController extends Controller
{
    public function index(Request $request)
    {
        $getRecord = ProductionModel::getRecord($request)->where("status", 1)->unique("prod_no");
        return view("backend.quality.list", compact("getRecord"));
    }

    public function result(Request $request, $prod_no)
    {
        $request->validate([
            'check' => "required|numeric|min:1"
        ]);

        $io = ProductionModel::where("prod_no", $prod_no)->value("io_no");
        $check = $request->check == 1 ? "OK" : "NG";

        QualityModel::updateOrCreate(
            ['io' => $io],
            ['result' => trim($request->check)]
        );
        // dd($qualityIo);

        return redirect()->back()->with("success", "Telah berhasil menilai product: {$prod_no} menjadi {$check}");
    }

    public function barcode(Request $request)
    {
        $getRecord = ProductionModel::with("quality")->where("status", 1)->whereHas("quality", function ($query) {
            $query->where("result", 1);
        })->get();

        $user = Auth::user()->username;
        $addedBarcodes = barcodeQualityModel::where('username', $user)->latest()->take(5)->get();

        // dd($getProd);
        return view("backend.quality.barcode", compact("getRecord", "addedBarcodes"));
    }

    public function add_print(Request $request)
    {
        // dd($request->all()); // Check what data is being sent
        $user   = Auth::user()->username;
        $barcode = request()->validate([
            'prod_no'  => 'required|string',
            'prod_desc'  => 'required|string',
            'qty'   => 'required|numeric|min:1',
        ]);

        $barcode        = new barcodeQualityModel();
        $barcode->prod_no  = trim($request->prod_no);
        $barcode->prod_desc  = trim($request->prod_desc);
        $barcode->qty   = trim($request->qty);
        $barcode->username = $user;
        $barcode->save();
        // dd($barcode);

        return redirect('admin/quality/barcode')->with('success', "Succesfully add barcode {$barcode->prod_no}");
    }

    public function delete($id)
    {
        $recordDelete = barcodeQualityModel::find($id);
        $recordDelete->delete();

        return redirect()->back()->with("error", "Barcode print berhasil dihapus");
    }

    public function deleteall()
    {
        $user = Auth::user()->username;
        $recordDelete = barcodeQualityModel::where("username", $user);
        $recordDelete->delete();

        return redirect()->back()->with("error", "Berhasil hapus semua barcode print");
    }

    public function print(Request $request)
    {
        $user = Auth::user()->username;
        $addedBarcodes = barcodeQualityModel::where("username", $user)->get();

        if (empty($addedBarcodes)) {
            return redirect()->back()->with("error", "Tidak ada barcode untuk print yang dipilih");
        }

        // dd($addedBarcodes);

        return view("backend.quality.print", compact("addedBarcodes"));
    }
}
