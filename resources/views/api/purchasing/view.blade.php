@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>Purchasing List Details</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/purchasing') }}" class="btn btn-primary btn-sm">Purchasing List</a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col col-md-12">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">View Purchase Line</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Series</label>
                                    <div class="col-sm-4">
                                        :
                                        @if (!empty($series) && isset($series['ObjectCode'], $series['SeriesName']))
                                            {{ $series['SeriesName'] }}
                                        @else
                                            <span class="text-danger">⚠️ Series tidak ditemukan: {{ $po['Series'] }}</span>
                                        @endif
                                    </div>
                                    <label for="" class="col-sm-2 col-form-lable">IO</label>
                                    <div class="col-sm-4">: {{ $po['U_MEB_NO_IO '] ?? '-' }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Nomor PO</label>
                                    <div class="col-sm-4">: {{ $po['DocNum'] ?? '-' }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Nomor PO Maklon</label>
                                    <div class="col-sm-4">: {{ $po['U_MEB_PONo_Maklon '] ?? '-' }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Vendor Code</label>
                                    <div class="col-sm-4">: {{ $po['CardCode'] ?? '-' }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Internal No</label>
                                    <div class="col-sm-4">: {{ $po['U_MEB_Internal_No'] ?? '-' }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Vendor</label>
                                    <div class="col-sm-4">: {{ $po['CardName'] ?? '-' }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">No SO</label>
                                    <div class="col-sm-4">: {{ $po['U_MEB_No_SO'] ?? '-' }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Vendor Ref No</label>
                                    <div class="col-sm-4">: {{ $po['NumAtCard'] ?? '-' }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Approved By</label>
                                    <div class="col-sm-4">: {{ $approve_by }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Contact Person</label>
                                    <div class="col-sm-4">: {{ $po['CntctCode'] ?? '-' }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Knowing By</label>
                                    <div class="col-sm-4">: {{ $knowing_by }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Buyer</label>
                                    <div class="col-sm-4">: {{ $buyer }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Contract</label>
                                    <div class="col-sm-4">: {{ $po['U_MEB_TTD'] ?? '-' }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Posting Date</label>
                                    <div class="col-sm-4">: {{ $po['DocDate'] ?? '-' }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Contract Addendum</label>
                                    <div class="col-sm-4">: {{ $po['U_MEB_HRMT_Kami'] ?? '-' }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Account Code</label>
                                    <div class="col-sm-4">: {{ $po['AcctCode'] ?? '-' }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Distr. Rule</label>
                                    <div class="col-sm-4">: {{ $po['OcrCode'] ?? '-' }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Status</label>
                                    <div class="col-sm-4">: {{ $po['DocStatus'] }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Note</label>
                                    <div class="col-sm-4">: - </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-stripped nowrap">
                                        @if (count($lines) > 0)
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Item Code</th>
                                                    <th>Item Type</th>
                                                    <th>Item Desc</th>
                                                    <th>Plan Qty</th>
                                                    <th>Open Qty</th>
                                                    <th>Uom</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($lines as $line)
                                                    @php
                                                        // Hitung total PlannedQty & IssuedQty untuk semua Lines line ini
                                                        $totalPlannedQty = collect($line['Lines'] ?? [])->sum(function (
                                                            $l,
                                                        ) {
                                                            return (float) ($l['PlannedQty'] ?? 0);
                                                        });

                                                        $totalIssuedQty = collect($line['Lines'] ?? [])->sum(function (
                                                            $l,
                                                        ) {
                                                            return (float) ($l['IssuedQty'] ?? 0);
                                                        });
                                                        $needIssue = $totalIssuedQty < $totalPlannedQty; // true = masih harus issue
                                                    @endphp

                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $line['ItemCode'] ?? '-' }}</td>
                                                        <td>{{ $line['Dscription'] ?? '-' }}</td>
                                                        <td>{{ $line['FreeTxt'] ?? '-' }}</td>
                                                        <td>{{ formatDecimalsSAP($line['Quantity']) }}</td>
                                                        <td>{{ formatDecimalsSAP($line['OpenQty']) }}</td>
                                                        <td>{{ $line['UnitMsr'] ?? '-' }}</td>
                                                        <td>{{ $line['LineStatus'] ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        @else
                                            <tbody>
                                                <tr>
                                                    <td colspan="100%">Tidak ada data yang ditemukan</td>
                                                </tr>
                                            </tbody>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button onclick="history.back()" class="btn btn-default">Back</button>
                                @php
                                    $itemCode = $lines[0]['ItemCode'] ?? '';
                                @endphp
                                @if ($user->department === 'Production and Warehouse')
                                    @if ($po['DocStatus'] == 'Open')
                                        @if (stripos($itemCode, 'Maklon') !== false)
                                            <a href="{{ url('admin/transaction/goodissued') }}"
                                                class="btn btn-outline-success">
                                                <i class="fa fa-arrow-right"></i> Open GI
                                            </a>
                                            <a href="{{ url('admin/transaction/goodreceipt') }}"
                                                class="btn btn-outline-success">
                                                <i class="fa fa-arrow-right"></i> Open GR
                                            </a>
                                        @elseif (strpos($itemCode, 'RM') === 0)
                                            <a href="{{ url('admin/transaction/stockin?po=' . $po['DocNum'] . '&docEntry=' . $po['DocEntry']) }}"
                                                class="btn btn-outline-success">
                                                <i class="fa fa-arrow-right"></i> Open GRPO
                                            </a>
                                        @else
                                            {{ $po['DocStatus'] }}
                                        @endif
                                    @else
                                        {{ $po['DocStatus'] }}
                                    @endif
                                @else
                                    @if ($po['DocStatus'] == 'Open' && $itemCode)
                                        @if (str_contains($itemCode, 'Maklon'))
                                            GI/GR
                                        @elseif (str_starts_with($itemCode, 'RM'))
                                            Open GRPO
                                        @else
                                            Open
                                        @endif
                                    @else
                                        {{ $po['DocStatus'] }}
                                    @endif
                                @endif
                                {{-- currently update --}}
                                @if (($user->department == 'Production and Warehouse' && $user->level == 'Leader') || $user->department == 'IT')
                                    <a href="{{ url('admin/purchasing/barcode/' . $po['DocEntry']) }}"
                                        class="btn btn-outline-success">
                                        <i class="fa fa-arrow-right"></i> Print Barcode
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
