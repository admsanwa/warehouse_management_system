<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Http;
use Illuminate\Http\Request;
use App\Services\SapService;
use Illuminate\Support\Arr;
use App\Models\SapReasonModel as SapReason;
use App\Models\BuyerModel;
use App\Models\DeliveryModel;
use Illuminate\Support\Facades\DB;

class InventorytfController extends Controller
{
    protected $sap;

    public function __construct(SapService $sap)
    {
        $this->sap = $sap;
    }

    public function create()
    {
        $inv_trans_reasons = SapReason::where('type', 'inv-trans')
            ->orderBy('reason_code')
            ->pluck('reason_desc', 'reason_code')
            ->toArray();
        $buyers = BuyerModel::all();
        return view("api.inventorytf.create", compact("inv_trans_reasons", "buyers"));
    }

    public function list(Request $request)
    {
        $param = [
            "page" => (int) $request->get('page', 1),
            "limit" => (int) $request->get('limit', 5),
            "DocNum" => $request->get('number'),
            "U_MEB_NO_IO" => $request->get('U_MEB_NO_IO'),
            "DocDate" => $request->get('date'),
            "DocStatus" =>  $request->get('status', 'O'),
            "Series" =>  $request->get('series'),
        ];

        $getInvtf = $this->sap->getInventoryTransfers($param);
        if (empty($getInvtf) || $getInvtf['success'] !== true) {
            return back()->with('error', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.');
        }

        // Filter ulang kalau ada Series
        if (!empty($param['Series']) && !empty($getInvtf['data'])) {
            $getInvtf['data'] = collect($getInvtf['data'])
                ->where('Series', $param['Series'])
                ->values()
                ->all();

            $getInvtf['total'] = count($getInvtf['data']);
        }
        $currentCount = $getInvtf['total'] ?? count($getInvtf['data'] ?? []);
        $totalPages = ($currentCount < $param['limit']) ? $param['page'] : $param['page'] + 1;

        return view('api.inventorytf.list', [
            'getInvtf'      => $getInvtf['data'],
            'page'        => $getInvtf['page'],
            'limit'       => $getInvtf['limit'],
            'total'       => $getInvtf['total'],
            'totalPages'  => $totalPages,
        ]);
    }

    public function view(Request $request)
    {
        $param = [
            "Page" => 1,
            "limit" => 1,
            "DocNum" =>  $request->query('docNum'),
            "DocEntry" => $request->query('docEntry')
        ];
        $getInvtf = $this->sap->getInventoryTransfers($param);

        if (empty($getInvtf) || !Arr::get($getInvtf, 'success')) {
            return back()->with(
                'error',
                Arr::get($getInvtf, 'message', 'Gagal mengambil data dari SAP. Silakan coba lagi nanti.')
            );
        }
        $invtf = Arr::get($getInvtf, 'data.0', []);
        $lines = Arr::get($invtf, 'Lines', []);

        $get_series = $this->sap->getSeries(['page' => 1, 'limit' => 1, 'Series' => (int) $invtf['Series']]);
        $series =   Arr::get($get_series, 'data.0', []);
        return view(
            'api.inventorytf.view',
            [
                'invtf'    => $invtf,
                'lines' => $lines,
                'series' => $series,
            ]
        );
    }

    public function transfer(Request $request)
    {
        try {
            $validated = $request->validate([
                'docNum' => 'nullable|string',
                'cardName' => 'nullable|string',
                'FromWhsCode' => 'required|string',
                'ToWhsCode' => 'required|string|different:FromWhsCode',
                'U_MEB_Default_Whse' => 'nullable|string',
                'U_SI_No_Surat_Jalan' => 'nullable|string',
                'SlpCode' => 'nullable|string',
                'Ref2' => 'nullable|string',
                'U_MEB_NO_IO' => 'nullable|string',
                'U_MEB_No_SO' => 'nullable|string',
                'U_MEB_ProjectDetail' => 'nullable|string',
                'U_MEB_Internal_No' => 'nullable|string',
                'U_MEB_Project_Code' => 'nullable|string',
                'U_MEB_NO_GI' => 'nullable|string',
                'U_MEB_No_Inv_Trf_Asa' => 'nullable|string',
                'U_MEB_Type_Inv_Trans' => 'nullable|string',
                'U_MEB_Dist_Rule' => 'nullable|string',
                'U_SI_No_Produksi' => 'nullable|string',
                'U_MEB_No_Prod_Order' => 'nullable|string',
                'U_SI_HARI_TGL_KIRIM' => 'nullable|date',
                'U_SI_Lokasi' => 'nullable|string',
                'U_SI_KMPN_TMBHN' => 'nullable|string',
                'remarks' => 'required|string',
                'Comments' => 'nullable|string',
                'stocks' => 'required|array|min:1',
                'stocks.*.ItemCode' => 'required|string',
                'stocks.*.qty' => 'required|string',
                'stocks.*.UnitMsr' => 'nullable|string',
            ]);

            $postData = [
                // 'DocDate'    => now()->format('Y/m/d'),
                'DocDate'    => "2025/09/30",
                'Comment'    => $validated['Comments'] ?? null,
                'Ref2'       => $validated['Ref2'] ?? null,
                'Ext' => [
                    "SalesPersonCode" => $validated["SlpCode"],
                    'FromWhsCode' => $validated['FromWhsCode'],
                    'ToWhsCode'   => $validated['ToWhsCode'],
                    "U_MEB_Default_Whse" => $validated['U_MEB_Default_Whse'] ?? null,
                    "U_MEB_Internal_No" => $validated['U_MEB_Internal_No'] ?? null,
                    "U_SI_No_Surat_Jalan" => $validated['U_SI_No_Surat_Jalan'] ?? null,
                    "U_MEB_NO_IO" => $validated['U_MEB_NO_IO'] ?? null,
                    "U_MEB_No_SO" => $validated['U_MEB_No_SO'] ?? null,
                    "U_MEB_Project_Code" => $validated['U_MEB_Project_Code'] ?? null,
                    "U_MEB_Type_Inv_Trans" => $validated['U_MEB_Type_Inv_Trans'] ?? null,
                    "U_MEB_PONo_Maklon" => $validated['docNum'] ?? null,
                    "U_MEB_DIST_RULE" => $validated['U_MEB_Dist_Rule'] ?? null,
                    "U_MEB_Vendor_Maklon" => $validated['cardName'] ?? null,
                    "U_MEB_NO_GI" => $validated['U_MEB_NO_GI'] ?? null,
                    "U_MEB_ProjectDetail" => $validated['U_MEB_ProjectDetail'] ?? null,
                    "U_MEB_No_Inv_Trf_Asa" => $validated['U_MEB_No_Inv_Trf_Asa'] ?? null,
                    "U_SI_No_Produksi" => $validated['U_SI_No_Produksi'] ?? null,
                    "U_MEB_No_Prod_Order" => $validated['U_MEB_No_Prod_Order'] ?? null,
                    "U_SI_HARI_TGL_KIRIM" => $validated['U_SI_HARI_TGL_KIRIM'] ?? null,
                    "U_SI_Lokasi" => $validated['U_SI_Lokasi'] ?? null,
                    "U_SI_KMPN_TMBHN" => $validated['U_SI_KMPN_TMBHN'] ?? null,
                    "JournalMemo" => $validated['remarks'],
                ],
                'Lines' => [],
            ];

            $lines = [];
            foreach ($validated['stocks'] as $row) {
                $entryQty = (float) str_replace(',', '.', str_replace('.', '', $row['qty']));
                $lines[] = [
                    'ItemCode'    => $row['ItemCode'] ?? null,
                    'Quantity'    => $entryQty,
                    'FromWhsCode'  => $validated['FromWhsCode'],
                    'ToWhsCode'    =>  $validated['ToWhsCode'],
                    'OcrCode'    => $validated['U_MEB_Dist_Rule'] ?? null,
                    'ProjectCode'    => $validated['U_MEB_Project_Code'],
                    "Ext" => [
                        "U_MEB_DIST_RULE" => $validated['U_MEB_Dist_Rule'] ?? null,
                    ]
                ];
            }

            $postData['Lines'] = $lines;

            $post_transfer = $this->sap->postInventoryTransfer($postData);

            if (empty($post_transfer['success'])) {
                throw new \Exception($post_transfer['message'] ?? 'SAP Inventory Transfer failed without message');
            }

            // save to db || Currently update
            if (!empty($post_transfer) && Arr::get($post_transfer, 'success') === true) {
                if (
                    ($validated['ToWhsCode'] === 'JK001' && $validated['U_MEB_Default_Whse'] === 'JK001') ||
                    ($validated['ToWhsCode'] === 'SB904' && $validated['U_MEB_Default_Whse'] === 'SB904')
                ) {
                    // get prod order
                    $param = [
                        "DocEntry" => $request->prodDocEntry,
                    ];
                    $prods = $this->sap->getProductionOrders($param);

                    if (empty($prods) || !Arr::get($prods, 'success')) {
                        return back()->with(
                            'error',
                            Arr::get($prods, 'message', 'Gagal mengambil data dari Production Order SAP. Silakan coba lagi nanti.')
                        );
                    }

                    $prod = Arr::get($prods, 'data.0', []);

                    DB::transaction(function () use ($request, $validated, $prod) {
                        $delivery = DeliveryModel::create([
                            'doc_entry' => $request->prodDocEntry ?? 0,
                            'io' => $validated['U_MEB_NO_IO'] ?? '-',
                            'prod_order' => $validated['U_MEB_No_Prod_Order'] ?? 0,
                            'prod_no' => $prod['ItemCode'] ?? '-',
                            'prod_desc' => $prod['ItemName'] ?? '-',
                            'series' => $prod['Series'] ?? 0,
                            'remark' => '-',
                            'tracker_by' => '-'
                        ]);

                        return response()->json([
                            'success' => true,
                            'message' => 'Inventory transfer berhasil di simpan ke DB WMS',
                            'data'  => $delivery,
                        ]);
                    });
                }
            }

            // 5️⃣ Response sukses
            return response()->json([
                'success' => true,
                'message' => 'Inventory transfer berhasil dikirim ke SAP',
                'data' => $postData,
                'response' => $post_transfer ?? [],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
                'data' => $postData ?? '',
                'response' => $post_transfer ?? [],
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan inventory transfer: ' . $e->getMessage(),
                'data' => $postData ?? '',
                'response' => $post_transfer ?? [],
            ], 500);
        }
    }

    public function so_search(Request $request)
    {
        $param = [
            "limit" => (int) $request->get('limit', 5),
            "DocNum" => $request->get('q'),
            "U_MEB_NO_IO" => $request->get('no_io'),
            'page'       => 1,
        ];

        $getSales = $this->sap->getSalesOrders($param);
        if (empty($getSales) || $getSales['success'] !== true) {
            return response()->json([
                'results' => [],
            ]);
        }
        $soData = collect($getSales['data'] ?? [])->map(function ($item) {
            return [
                'id'   => $item['DocEntry'],
                'docnum'   => $item['DocNum'],
                'text' => $item['DocNum'],
            ];
        });

        return response()->json([
            'results' => $soData,
            'sales_orders' => $getSales['data'],
        ]);
    }
}
