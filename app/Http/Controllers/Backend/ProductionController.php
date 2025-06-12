<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ProductionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $getRecord = ProductionModel::getRecord($request);
        return view('backend.production.list', compact('getRecord'));
    }

    public function view($id)
    {
        $data['getRecord'] = ProductionModel::find($id);
        return view('backend.production.view', $data);
    }

    public function upload_form()
    {
        return view('backend.production.upload');
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

                    ProductionModel::create([
                        'prod_no'  => Carbon::createFromFormat('d.m.Y', $row[1])->format('Y-m-d'),
                        'prod_desc' => $row[1],
                        'remarks'   => $row[2],
                        'doc_num'   => $row[3],
                        'io_no'     => $row[4],
                        'due_date'  => $row[5],
                        'item_code' => $row[6],
                        'item_type' => $row[7],
                        'item_desc' => $row[8],
                        'qty'       => $row[9],
                        'uom'       => $row[10],
                        'status'    => $row[11]
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

        return back()->with('success', "Productions Imported Succesfully");
    }
}
