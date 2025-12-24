@extends('backend.layouts.app')

@section('content')
    <style>
        .box {
            display: inline-block;
            min-width: 180px;
            text-align: left;
            font-weight: bold;
            font-size: 14px;
        }

        .box div {
            display: flex;
            justify-content: space-between;
        }

        .fixed-table {
    table-layout: fixed;
}

.fixed-table td,
.fixed-table th {
    word-wrap: break-word;
}

    </style>

    <div class="memo-container">
        <div class="memo-header d-flex justify-content-between">
            <div class="text-left">
                <img src="{{ asset('assets/images/logo/logo-sanwamas.png') }}" alt="Logo-Sanwa">
                <p class="company-address mb-0">Jl. Raya Bekasi KM. 27, K.A Bungur Pondok Ungu, Bekasi</p>
                <p class="company-address mb-0">Phone: (021) 888 0338 &nbsp; Fax: (021) 888 0340</p>
            </div>
            <div class="text-right text-end">
                <div class="box">
                    <div class="d-flex justify-content-between">
                        <span>No :</span>
                        <input type="hidden" id="no_bon" value="{{ $bon->no }}">
                        <span>{{ $bon->no }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Date :</span>
                        <span>{{ \Carbon\Carbon::parse($bon->date)->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>


        <h4 class="memo-title">BON PEMBELIAN BARANG</h4>

        <table class="table table-borderless">
            <tr>
                <td>Bagian</td>
                <td>: {{ $bon->section }}</td>
            </tr>
            <tr>
                <td>IO</td>
                <td>: {{ $bon->io }}</td>
            </tr>
            <tr>
                <td>Project</td>
                <td>: {{ $bon->project }}</td>
            </tr>
            <tr>
                <td>Make to</td>
                <td>: {{ $bon->make_to }}</td>
            </tr>
        </table>

        <table class="table table-bordered details-table fixed-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th style="width: 100px;">Qty</th>
                    <th>Keterangan</th>
                    @if ($signBuyer)
                        <th style="width: 250px;">PO</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($bon->details as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $detail->item_code }}</td>
                        <td>{{ $detail->item_name }}</td>
                        <td>{{ $detail->qty . ' ' . $detail->uom }}</td>
                        <td class="text-center">{{ $detail->remark }}</td>
                        @if ($signBuyer)
                        <td>
                            <form id="insertPoForm_{{ $bon->id }}" method="post">
                            @csrf
                                <div>
                                    <select name="series" class="form-control series-select" data-bon-id="{{ $detail->id }}" data-object-code="22"
                                       data-selected-id={{ $detail->no_series }} data-selected-text={{ $detail->no_series }} required></select>
                                </div>
                                <div>
                                    <select name="po" data-bon-id="{{ $detail->id }}" class="form-control po-select" value="{{ $detail->no_po }}"
                                        data-selected-id={{ $detail->no_po }} data-selected-text={{ $detail->no_po }} required>
                                    </select>
                                    <small class="text-muted">Memilih series akan mempermudah pencarian data PO yang
                                        sesuai.</small>
                                </div>
                            </form>
                        </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>


        <div class="signature-block row mt-5">
            @if ($signApprove)
                <div class="col md-4 text-center col-sign"
                    style="display: {{ $signApprove->sign === 1 ? 'block' : 'none' }}">
                    <div class="fw-bold mb-1">Disetujui Oleh,</div>
                    <div class="img-sign mb-2"><img src="{{ asset('assets/images/sign/' . $signApprove->user->sign) }}"
                            width="100" height="65" alt="sign"></div>
                    <div class="signature-name fw-semibold">{{ $signApprove->user->fullname }}</div>
                    <div class="signature-dept text-muted small">{{ $signApprove->user->department }}</div>
                </div>
            @endif
            @if ($signBuyer)
                <div class="col md-4 text-center col-sign"
                    style="display: {{ $signBuyer->sign === 1 ? 'block' : 'none' }}">
                    <div class="fw-bold mb-1">Bagian Pembeli,</div>
                    <div class="img-sign mb-2"><img src="{{ asset('assets/images/sign/' . $signBuyer->user->sign) }}"
                            width="100" height="65" alt="sign"></div>
                    <div class="signature-name fw-semibold">{{ $signBuyer->user->fullname }}</div>
                    <div class="signature-dept text-muted small">{{ $signBuyer->user->department }}</div>
                </div>
            @endif
            <div class="col md-4 text-center col-sign">
                <div class="fw-bold mb-1">Pemesan,</div>
                <div class="img-sign mb-2"><img src="{{ asset('assets/images/sign/' . $bon->createdBy->sign) }}"
                        width="100" height="65" alt="sign"></div>
                <div class="signature-name fw-semibold">{{ $bon->createdBy->fullname }}</div>
                <div class="signature-dept text-muted small">{{ $bon->createdBy->department }}</div>
            </div>
        </div>
        <div id="no-print">
            <button class="btn btn-primary btn-print" onclick="window.print()">
                <i class="fa fa-print"></i> Print
            </button>
            <button onclick="history.back()" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back
            </button>

            <button class="btn btn-success"
                @if ($user->department === 'Purchasing') 
                    onclick="approve()"
                @else
                    onclick="approveBon()"
                @endif
                style="display: {{ $user->department === 'Purchasing' || ($user->department === 'Procurement, Installation and Delivery' && $user->level === 'Manager') 
                    ? 'block' : 'none' }}">
                <i class="fa fa-check-circle"></i> Approve
            </button>

            <div id="loadingOverlay"
                style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; text-align:center;">
                <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); color:white;">
                    <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;"></div>
                    <div class="mt-3">Processing... Please wait</div>
                </div>
            </div>

            @include('partials.modal.insertpo', ['bon' => $bon])
        </div>
    </div>
    <!-- Include barcode scanner JS -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $(".series-select").each(function () {
                const $series = $(this);
                const bonId = $series.data("bon-id");
                const objectCode = $series.data("object-code")
        
                $series.select2({
                    placeholder: "Choose Series",
                    allowClear: true,
                    width: "100%",
                    language: {
                        inputTooShort: function() {
                            return "Type series for searching...";
                        },
                        noResults: function() {
                            return "Data not found";
                        },
                        searching: function() {
                            return "Stiil searching...";
                        },
                    },
                    ajax: {
                        url: "/purchasing/seriesSearch",
                        dataType: "json",
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                                ObjectCode: objectCode
                            };
                        },
                        processResults: function(data) {
                            console.log("Response dari server:", data);
                            return {
                                results: data.results || []
                            };
                        }
                    }
                });

                // default series
                const prefix = {!! json_encode(Auth::user()->default_series_prefix) !!};
                setDefaultSeries($series, objectCode, prefix);

                setSelect2Value(
                    $series,
                    $series.data("selected-id"),
                    $series.data("selected-text")
                )
            });

            $(".po-select").each(function() {
                const $po = $(this);
                const bonId = $po.data("bon-id");
                const $series = $(".series-select[data-bon-id='" + bonId + "']");
                $po.select2({
                    placeholder: "Select No Purchase Order",
                    allowClear: true,
                    width: "100%",
                    minimumInputLength: 3,
                    language: {
                        inputTooShort: function() {
                            return "Type min 3 character";
                        },
                        noResults: function() {
                            return "Data not found";
                        },
                        searching: function() {
                            return "Still searching...";
                        }
                    },
                    ajax: {
                        url: "/purchaseOrderSearch",
                        dataType: "json",
                        delay: 600,
                        data: function(params) {
                            const seriesData = $("#seriesSelect_{{ $bon->id }}").select2('data');
                            const series = seriesData.length > 0 ? seriesData[0].id : null;
        
                            return {
                                q: params.term,
                                limit: 5,
                                series: series,
                                status: "Open",
                            };
                        },
                        processResults: function(data) {
                            tempPoData = data.po || [];
                            return {
                                results: (data.results || []).map(item => ({
                                    id: item.docnum,
                                    text: item.text,
                                }))
                            };
                        },
                        cache: true
                    }
                });

                setSelect2Value(
                    $po,
                    $po.data("selected-id"),
                    $po.data("selected-text")
                )
            })
        });

        function showLoading() {
            document.getElementById("loadingOverlay").style.display = "block";
        }

        function hideLoading() {
            document.getElementById("loadingOverlay").style.display = "none";
        }

        function setSelect2Value($select, id, text) {
            if (!id) return;

            const option = new Option(text, id, true, true);
            $select.append(option).trigger('change');
        }

        async function approve() {
            const results = [];

            $('.series-select').each(function () {
                const $series = $(this);
                const bonId = $series.data('bon-id');

                const seriesData = $series.select2('data');
                const series = seriesData.length ? seriesData[0] : null;

                const $po = $('.po-select[data-bon-id="' + bonId + '"]');
                const poData = $po.select2('data');
                const po = poData.length ? poData[0] : null;
                const noBon = document.getElementById("no_bon").value;

                results.push({
                   bon_id: bonId,
                   po_no: po ? po.id : null,
                   series: series ? series.id : null,
                   no_bon: noBon ? noBon : 0
                })

                console.log("results", results);
            });

            if (results.some(r => !r.series || !r.po_no)) {
                alert("Series and PO must be selected for all rows");
                return;
            }
            
            if (!confirm("Are you sure approve to BON this?")) return;
                showLoading();

            try {
                const insertPoUrl = "{{ route('insert.po') }}";
                const res = await fetch(insertPoUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').content
                    },
                    body: JSON.stringify({
                        results: results,
                    })
                });

                const data = await res.json();
                alert(data.message || "PO Inserted Successfully");

            } catch (error) {
                console.error(error);
                alert("Failed to insert PO, Please try again");
            } finally {
                hideLoading();
                location.reload();
            }
            
        }

        function approveBon() {
            showLoading();
            const no_bon = document.getElementById("no_bon").value;
            if (!confirm("Are you sure approve to BON this?")) return;

            console.log("delvi approve");
            fetch("/approve-bon", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        no_bon
                    })
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    hideLoading();
                    if (data.success) location.reload();
                })
                .catch(error => {
                    console.error("Fetch Error:", error);
                    alert("An error occurred. Check console for details.");
                });
        }
    </script>
@endsection
