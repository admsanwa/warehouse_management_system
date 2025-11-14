@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>Production List Details</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/purchasing') }}" class="btn btn-primary btn-sm">Production List</a>
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
                                <h3 class="card-title">View Production Order</h3>
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
                                    <label class="col-sm-2 col-form-label">IO No</label>
                                    <div class="col-sm-4">: {{ $getRecord['U_MEB_NO_IO'] }}</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Doc Number</label>
                                    <div class="col-sm-4">: {{ $getRecord['DocNum'] }}</div>
                                    <label class="col-sm-2 col-form-label">Product Type</label>
                                    <div class="col-sm-4">: {{ $getRecord['U_MEB_PROD_TYPE'] }}</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Product No</label>
                                    <div class="col-sm-4">: {{ $getRecord['ItemCode'] }}</div>
                                    <label class="col-sm-2 col-form-label">Project Code</label>
                                    <div class="col-sm-4">: {{ $getRecord['U_MEB_Project_Code'] }}</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Product Desc</label>
                                    <div class="col-sm-4">: {{ $getRecord['ItemName'] }}</div>
                                    <label class="col-sm-2 col-form-label">Contract</label>
                                    <div class="col-sm-4">: {{ $getRecord['U_MEB_Contract'] }}</div>
                                </div>

                                <div class="row align-items-center mb-2">
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <div class="d-flex justify-content-between border-bottom pb-1">
                                            <label class="fw-semibold mb-0">
                                                Planned Qty
                                            </label>
                                            <span class="fw-bold">{{ formatDecimalsSAP($getRecord['PlannedQty']) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between border-bottom pb-1">
                                            <label class="fw-semibold mb-0">
                                                Receipt Qty
                                            </label>
                                            <span class="fw-bold">{{ formatDecimalsSAP($getRecord['CmpltQty']) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row align-items-center mb-2">
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <div class="d-flex justify-content-between border-bottom pb-1">
                                            <label class="fw-semibold text-danger mb-0">
                                                Reject Qty
                                            </label>
                                            <span
                                                class="text-danger fw-bold">{{ formatDecimalsSAP($totalRejectQty) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between align-items-start border-bottom pb-1">
                                            <div>
                                                <label class="fw-semibold mb-0 d-block">
                                                    <i class="bi bi-box-seam me-1"></i> Total Complete Qty
                                                </label>
                                                <small class="text-muted">(Receipt Qty + Reject Qty)</small>
                                            </div>
                                            <span class="fw-bold fs-6 text-end">
                                                {{ formatDecimalsSAP($getRecord['CmpltQty'] + $totalRejectQty) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Sales Order</label>
                                    <div class="col-sm-4">: {{ $getRecord['OriginNum'] }}</div>
                                    <label class="col-sm-2 col-form-label">Contract Adendum</label>
                                    <div class="col-sm-4">: {{ $getRecord['U_MEB_ProjectDetail'] }}</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Distr. Rule</label>
                                    <div class="col-sm-4">: {{ $getRecord['OcrCode'] }}</div>
                                    <label class="col-sm-2 col-form-label">No Internal</label>
                                    <div class="col-sm-4">: {{ $getRecord['U_MEB_Internal_Prod'] }}</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Due Date</label>
                                    <div class="col-sm-4">: {{ $getRecord['DueDate'] }}</div>
                                    <label class="col-sm-2 col-form-label">Status</label>
                                    <div class="col-sm-4">: {{ $getRecord['Status'] }}</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Remarks</label>
                                    <div class="col-sm-10">: {{ $getRecord['Comments'] }}</div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="card mt-3 shadow-sm border-0 rounded-2">
                            <div class="card-header bg-secondary text-white fw-bold">
                                Reject Quantity
                            </div>
                            <div class="card-body">
                                @if ($rfp->count())
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped align-middle mb-3">
                                            <thead class="table-light">
                                                <tr class="text-center">
                                                    <th style="width: 5%">#</th>
                                                    <th>Product No</th>
                                                    <th>Plan Qty</th>
                                                    <th>Reject Quantity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($rfp as $index => $item)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>{{ $item->prod_no }}</td>
                                                        <td>{{ $getRecord['PlannedQty'] }}</td>
                                                        <td class="text-end">{{ formatDecimalsSAP($item->rjct_qty) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="fw-bold">
                                                <tr>
                                                    <td colspan="2" class="text-end">Total Reject Qty</td>
                                                    <td class="text-end">{{ formatDecimalsSAP($totalRejectQty) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">Tidak ada data reject quantity.</p>
                                @endif
                            </div>
                        </div> --}}
                        <div class="card">
                            <div class="card-body">
                                <div class="table table-responsive">
                                    <table class="table table-stripped">
                                        @if (count($lines) > 0)
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Item Code</th>
                                                    <th>Item Type</th>
                                                    <th>Plan Qty</th>
                                                    <th>Issued Qty</th>
                                                    <th>Uom</th>
                                                    <th>Status</th>
                                                    @if ($user->department == 'Quality Control' || $user->department == 'IT')
                                                        <th>QC</th>
                                                        <th>Check Qty</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($lines as $line)
                                                    @php
                                                        $quality = $qualities->get($line['ItemCode'] ?? '');
                                                        $statusMap = [
                                                            1 => 'OK',
                                                            2 => 'NG',
                                                            3 => 'Need Approval',
                                                            4 => 'Need Paint',
                                                            5 => 'Painting by Inhouse',
                                                            6 => 'Painting by Makloon',
                                                        ];

                                                        $prefix = ['RM', 'SI', 'SF'];
                                                        // Hitung total PlannedQty & IssuedQty untuk semua Lines order ini
                                                        $totalPlannedQty = collect($lines)
                                                            ->filter(function ($l) use ($prefix) {
                                                                return in_array(substr($l['ItemCode'], 0, 2), $prefix);
                                                            })
                                                            ->sum(function ($l) {
                                                                return (float) ($l['PlannedQty'] ?? 0);
                                                            },
                                                        );

                                                        $totalIssuedQty = collect($lines)
                                                            ->filter(function ($l) use ($prefix) {
                                                                return in_array(substr($l['ItemCode'], 0, 2), $prefix);
                                                            })
                                                            ->sum(function ($l) {
                                                                return (float) ($l['IssuedQty'] ?? 0);
                                                            },

                                                        );
                                                        $needIssue = $totalIssuedQty < $totalPlannedQty; // true = masih harus issue
                                                        // @dd(['need issue' => $needIssue, 'total issue' => $totalIssuedQty, 'total plan' => $totalPlannedQty, 'line' => $lines]);

                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $line['ItemCode'] ?? '-' }}</td>
                                                        <td>{{ $line['ItemName'] ?? '-' }}</td>
                                                        <td>{{ formatDecimalsSAP($line['PlannedQty']) }}</td>
                                                        <td>{{ formatDecimalsSAP($line['IssuedQty']) }}</td>
                                                        <td>{{ $line['InvntryUoM'] ?? '-' }}</td>
                                                        <td>
                                                            @if ($needIssue)
                                                                Release
                                                            @else
                                                                Receipt
                                                            @endif
                                                        </td>
                                                        @if ($user->department == 'Quality Control' || $user->department == 'IT')
                                                            @if (str_starts_with(strtoupper($line['ItemCode']), 'RM'))
                                                                <td>{{ $statusMap[$quality->result ?? 0] ?? '-' }}</td>
                                                                <td>
                                                                    <a href="#" data-bs-toggle="modal"
                                                                        data-bs-target="#modal_{{ $line['ItemCode'] }}"><i
                                                                            class="fa fa-eye"></i> Check QC</a>
                                                                </td>
                                                            @endif
                                                        @endif
                                                        @include('partials.modal.assessmentqc', [
                                                            'quality' => $line,
                                                            'getRecord' => $getRecord,
                                                            'user' => $user,
                                                        ])
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
                                @if ($getRecord['Status'] == 'Released')
                                    @if ($user->department === 'Production')
                                        @if ($needIssue)
                                            <a href="{{ url('admin/transaction/stockout?docNum=' . $getRecord['DocNum'] . '&docEntry=' . $getRecord['DocEntry']) }}"
                                                class="btn btn-sm btn-outline-success"><i class="fa fa-arrow-right"></i>
                                                Released</a>
                                        @else
                                            <a href="{{ url('admin/transaction/rfp?docNum=' . $getRecord['DocNum'] . '&docEntry=' . $getRecord['DocEntry']) }}"
                                                class="btn btn-sm btn-outline-success">
                                                <i class="fa fa-arrow-right"></i> Receipt
                                            </a>
                                        @endif
                                    @endif
                                @else
                                    {{ $getRecord['Status'] }}
                                @endif

                                @if (($user->department === 'Production and Warehouse' && $user->level === 'Leader') || $user->department === 'IT')
                                    <a href="{{ url('/preparematerial?docNum=' . $getRecord['DocNum'] . '&docEntry=' . $getRecord['DocEntry']) }}"
                                        class="btn btn-sm btn-outline-success"><i class="fa fa-arrow-right"></i> Form
                                        Prepare Material</a>
                                @endif
                                <button onclick="history.back()" class="btn btn-default">Back</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
