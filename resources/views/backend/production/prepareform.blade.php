@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>Create Prepare Material Form</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/production/po') }}" class="btn btn-primary btn-sm">Production Order</a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <section class="col col-md-12">
                        <div class="card shadow-sm">
                            @include('_message')

                            {{-- ================= HEADER SECTION ================= --}}
                            <div class="card-header bg-primary text-white py-2">
                                <h5 class="mb-0"><i class="fa fa-file-alt"></i> Document Information</h5>
                            </div>

                            <form action="{{ url('/preparematerial')}}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                @csrf

                                {{-- ================= BASIC INFO ================= --}}
                                <div class="card-body">
                                    @php
                                        $seriesValue = (!empty($series) && isset($series['ObjectCode'], $series['SeriesName']))
                                            ? $series['SeriesName']
                                            : "⚠️ Series tidak ditemukan: " . ($po['Series'] ?? 'N/A');
                                    @endphp

                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Series</label>
                                            <input type="text" class="form-control" name="Series"
                                                value="{{ $seriesValue }}" readonly
                                                style="{{ str_starts_with($seriesValue, '⚠️') ? 'color:red;' : '' }}">
                                            <input type="hidden" name="DocEntry" value="{{ $getRecord['DocEntry'] }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Doc Number</label>
                                            <input type="number" name="DocNum" class="form-control" value="{{ $getRecord['DocNum'] }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Product No</label>
                                            <input type="text" name="ItemCode" class="form-control" value="{{ $getRecord['ItemCode'] }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Product Description</label>
                                            <input type="text" class="form-control" value="{{ $getRecord['ItemName'] }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Due Date</label>
                                            <input type="text" class="form-control" value="{{ $getRecord['DueDate'] }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Distr Rule</label>
                                            <input type="text" class="form-control" value="{{ $getRecord['OcrCode'] }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Planned Qty</label>
                                            <input type="text" class="form-control text-end"
                                                value="{{ formatDecimalsSAP($getRecord['PlannedQty']) }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Reject Qty</label>
                                            <input type="text" class="form-control text-end"
                                                value="{{ formatDecimalsSAP($getRecord['RjctQty']) }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Complete Qty</label>
                                            <input type="text" class="form-control text-end"
                                                value="{{ formatDecimalsSAP($getRecord['CmpltQty']) }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Total Receipt</label>
                                            <input type="text" class="form-control text-end"
                                                value="{{ formatDecimalsSAP($getRecord['CmpltQty'] + $getRecord['RjctQty']) }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Status</label>
                                            <input type="text" class="form-control" value="{{ $getRecord['Status'] }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Sales Order</label>
                                            <input type="number" class="form-control" value="{{ $getRecord['OriginNum'] }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                {{-- ================= UDF SECTION ================= --}}
                                <div class="card-header bg-secondary text-white py-2 mt-3">
                                    <h6 class="mb-0"><i class="fa fa-tags"></i> User Defined Fields</h6>
                                </div>

                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">IO</label>
                                            <input type="text" name="U_MEB_NO_IO" class="form-control" value="{{ $getRecord['U_MEB_NO_IO'] }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Product Type</label>
                                            <input type="text" class="form-control" value="{{ $getRecord['U_MEB_PROD_TYPE'] }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Project Code</label>
                                            <input type="text" class="form-control" value="{{ $getRecord['U_MEB_Project_Code'] }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Internal No</label>
                                            <input type="text" class="form-control" value="{{ $getRecord['U_MEB_Internal_Prod'] }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Contract Adendum</label>
                                            <input type="text" class="form-control" value="{{ $getRecord['U_MEB_ProjectDetail'] }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Contract</label>
                                            <input type="text" class="form-control" value="{{ $getRecord['U_MEB_Contract'] }}" readonly>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Remarks</label>
                                            <textarea class="form-control" rows="2" readonly>{{ $getRecord['Comments'] }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- ================= LINE ITEMS ================= --}}
                                <div class="card-header bg-dark text-white py-2 mt-3">
                                    <h6 class="mb-0"><i class="fa fa-list"></i> Line Items</h6>
                                </div>

                                <div class="card-body p-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped align-middle table-sm">
                                            @if (count($lines) > 0)
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Item Code</th>
                                                        <th>Item Type</th>
                                                        <th class="text-end">Plan Qty</th>
                                                        <th class="text-end">Issued Qty</th>
                                                        <th class="text-end">Prepare Qty</th>
                                                        <th>UoM</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($lines as $i => $line)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                {{ $line['ItemCode'] ?? '-' }}
                                                                <input type="hidden" name="lines[{{ $i }}][ItemCode]" value="{{ $line['ItemCode'] ?? '-'}}">
                                                            </td>
                                                            <td>{{ $line['ItemName'] ?? '-' }}</td>
                                                            <td class="text-end">{{ formatDecimalsSAP($line['PlannedQty']) }}</td>
                                                            <td>{{ formatDecimalsSAP($line['IssuedQty']) }}</td>
                                                            <td>
                                                                @if (!in_array($line['ItemCode'], ['Z-DL', 'Z-FOH']))
                                                                    <input type="text" step="0.01" name="lines[{{ $i }}][PrepareQty]" class="form-control format-wms form-control-sm text-end" value="0">
                                                                @endif
                                                            </td>
                                                            <td>{{ $line['InvntryUoM'] ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @else
                                                <tbody>
                                                    <tr><td colspan="6" class="text-center text-muted">No line data found</td></tr>
                                                </tbody>
                                            @endif
                                        </table>
                                    </div>
                                </div>

                                {{-- ================= FOOTER ================= --}}
                                <div class="card-footer text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Select2 CSS -->

    <script>
        $(document).ready(function() {
            function initSelect2($el) {
                $el.select2({
                    placeholder: "Input Item Code...",
                    allowClear: true,
                    width: "100%",
                    language: {
                        inputTooShort: function() {
                            return "Input Item Code for searching...";
                        },
                        noResults: function() {
                            return "Not Found";
                        },
                        searching: function() {
                            return "Loading...";
                        },
                    },
                    ajax: {
                        url: "/onhandSearch",
                        dataType: "json",
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: (data.results || []).map((item) => ({
                                    id: item.id,
                                    text: item.text,
                                    uom: item.uom,
                                    item_desc: item.item_desc,
                                })),
                            };
                        },
                    },
                });

                // Event ketika pilih barang
                $el.on("select2:select", function(e) {
                    let data = e.params.data;
                    let $block = $(this).closest(".unit-block");
                    $block.find('input[name="uom[]"]').val(data.uom || "");
                    $block.find('input[name="item_desc[]"]').val(data.item_desc || "");
                });
            }

            $("#seriesSelect").select2({
                placeholder: "Select Series",
                allowClear: true,
                width: "100%",
                language: {
                    inputTooShort: function() {
                        return "Input Series for searching...";
                    },
                    noResults: function() {
                        return "Not Found";
                    },
                    searching: function() {
                        return "Loading...";
                    },
                },
                ajax: {
                    url: "/purchasing/seriesSearch",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        if (!params) {
                            return;
                        }
                        return {
                            q: params.term,
                            ObjectCode: '22'
                        };
                    },
                    processResults: function(data) {
                        console.log("Response dari server:", data); // cek di console
                        return {
                            results: (data.results || []).map(item => ({
                                id: item.id,
                                text: item.text
                            }))
                        };
                    }
                }
            });
            setDefaultSeries("#seriesSelect", "22");

            // select wh
            warehouseSelect2("FromWhsCode");
            warehouseSelect2("ToWhsCode");
            function warehouseSelect2(elementId) {
                const el = $("#" + elementId)
                if (el.length) {
                    el.select2({
                        allowClear: true,
                        width: "100%",
                        language: {
                            inputTooShort: function() {
                                return "Type for searching...";
                            },
                            noResults: function() {
                                return "Not Found";
                            },
                            searching: function() {
                                return "Loading...";
                            }
                        },
                        ajax: {
                            url: "/warehouseSearch",
                            dataType: "json",
                            delay: 250,
                            data: function(params) {
                                let searchData = {
                                    q: params.term,
                                    limit: 10,
                                }
                                return searchData;
                            },
                            processResults: function(data) {
                                console.log("Response dari server:", data); // cek di console
                                return {
                                    results: (data.results || []).map(item => ({
                                        id: item.id,
                                        text: item.text
                                    }))
                                };
                            }
                        }
                    });
                }
            }

            // format sap
            document.querySelectorAll('.format-wms').forEach(el => formatInputDecimalsWMS(el));     
        });

    </script>
@endsection
