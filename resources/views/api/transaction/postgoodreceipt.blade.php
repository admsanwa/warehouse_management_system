@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>Create Good Receipt Form</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/transaction/goodreceipt') }}" class="btn btn-primary btn-sm">Prepare Good
                                Receipt</a>
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
                            <div id="feedbackBox" class="toast-notification">
                                <span id="feedbackMessage"></span>
                            </div>
                            {{-- ================= HEADER SECTION ================= --}}
                            <div class="card-header bg-primary text-white py-2">
                                <h5 class="mb-0"><i class="fa fa-file-alt"></i> Document Information</h5>
                            </div>

                            <form id="goodReceiptForm" class="needs-validation" novalidate>
                                @csrf

                                {{-- ================= BASIC INFO ================= --}}
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Purchase Order</label>
                                            <input type="hidden" name="docentry" class="form-control"
                                                value="{{ $getRecord->doc_entry }}">
                                            <input type="number" name="docnum" class="form-control"
                                                value="{{ $getRecord->po }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Default Warehouse</label>
                                            <input type="text" name="warehouse" class="form-control"
                                                value="{{ $getRecord->whse }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Alasan Good Receipt</label>
                                            <input type="text" name="reason" class="form-control" id="reason"
                                                value="{{ $getRecord->reason }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Account Code</label>
                                            <input type="text" name="acct_code" class="form-control" id="acct_code"
                                                value="{{ $getRecord->acct_code }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">No Surat Jalan</label>
                                            <input type="text" class="form-control" name="no_surat_jalan"
                                                value="{{ $getRecord->no_surat_jalan }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">No Surat Jalan Barang Datang</label>
                                            <input type="text" class="form-control text-end" name="ref_surat_jalan"
                                                value="{{ $getRecord->ref_surat_jalan }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Internal No</label>
                                            <input type="text" class="form-control text-end" name="internal_no"
                                                value="{{ $getRecord->internal_no }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">IO</label>
                                            <input type="text" name="no_io" class="form-control text-end"
                                                value="{{ $getRecord->io }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">So</label>
                                            <input type="text" name="no_so" class="form-control text-end"
                                                value="{{ $getRecord->so }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">No Good Issue</label>
                                            <input type="text" name="no_gi" class="form-control"
                                                value="{{ $getRecord->no_gi }}" readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">No Inventory Transfer</label>
                                            <input type="text" name="no_inventory_tf" class="form-control"
                                                value="{{ $getRecord->no_inventory_tf }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Type Inventory Transaction</label>
                                            <input name="type_inv_transaction" type="number" class="form-control"
                                                value="{{ $getRecord->type_inv_transaction }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Project Code</label>
                                            <input name="project" type="text" class="form-control"
                                                value="{{ $getRecord->project_code }}" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">OCR / Distr Rule</label>
                                            <input name="cost_center" type="text" class="form-control"
                                                value="{{ $getRecord->distr_rule }}" readonly>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" rows="2" readonly>{{ $getRecord->remarks }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- ================= LINE ITEMS ================= --}}
                                <div class="card-header bg-dark text-white py-2 mt-3">
                                    <h6 class="mb-0"><i class="fa fa-list"></i> Unit Items</h6>
                                </div>

                                <div class="card-body p-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped align-middle table-sm">
                                            @if (count($getData) > 0)
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Item Code</th>
                                                        <th>Item Desc</th>
                                                        <th class="text-end">Qty</th>
                                                        <th>UoM</th>
                                                        <th>Unit Price</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="itemTable">
                                                    @foreach ($getData as $i => $line)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                {{ $line->item_code ?? '-' }}
                                                                <input type="hidden"
                                                                    name="lines[{{ $i }}][ItemCode]"
                                                                    value="{{ $line->item_code ?? '-' }}">
                                                            </td>
                                                            <td>{{ $line->item_desc ?? '-' }}
                                                                <input type="hidden"
                                                                    name="lines[{{ $i }}][Dscription]"
                                                                    value="{{ $line->item_desc ?? '-' }}">
                                                            </td>
                                                            <td class="text-end qty">
                                                                {{ formatDecimalsSAP($line->qty) }}
                                                                <input type="hidden"
                                                                    name="lines[{{ $i }}][qty]"
                                                                    value="{{ $line->qty ?? 0 }}">
                                                            </td>
                                                            <td>
                                                                {{ $line->uom ?? '-' }}
                                                                <input type="hidden"
                                                                    name="lines[{{ $i }}][UnitMsr]"
                                                                    value="{{ $line->uom ?? '' }}">
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01"
                                                                    name="lines[{{ $i }}][Price]"
                                                                    class="form-control price-input text-end {{ $getRecord->is_posted == 1 ? 'bg-light' : '' }}"
                                                                    style="min-width:60px !important;"
                                                                    value="{{ $line->price ?? 0 }}"
                                                                    {{ $getRecord->is_posted == 1 ? 'readonly' : '' }}>
                                                            </td>
                                                            <td class="text-end">
                                                                <span class="total"></span>
                                                                <input type="hidden"
                                                                    name="lines[{{ $i }}][LineTotal]"
                                                                    value="0" class="line-total">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="fw-bold">
                                                        <td colspan="6" class="text-end" style="font-weight: bold">
                                                            Grand Total</td>
                                                        <td id="grandTotal" class="text-end">0</td>
                                                    </tr>
                                                </tfoot>
                                            @else
                                                <tbody>
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">No line data
                                                            found</td>
                                                    </tr>
                                                </tbody>
                                            @endif
                                        </table>
                                    </div>
                                </div>

                                {{-- ================= FOOTER ================= --}}
                                @if ($getRecord->is_posted == 0)
                                    <div class="card-footer text-end">
                                        <button type="submit" class="btn btn-primary"
                                            onclick="return AddGoodReceiptForm();" id="btnSubmit">
                                            <i class="fa fa-save"></i> Save
                                        </button>
                                    </div>
                                @endif

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


            const table = document.getElementById('itemTable');
            const grandTotalEl = document.getElementById('grandTotal');
            const rupiah = (number) => {
                return new Intl.NumberFormat("id-ID", {
                    style: "decimal",
                    currency: "IDR"
                }).format(number).split(',')[0];
            }

            // Function to safely calculate totals
            function calculateTotals() {
                let grandTotal = 0;

                table.querySelectorAll('tr').forEach(row => {
                    const qty = parseFloat(row.querySelector('.qty')?.textContent) || 0;
                    const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
                    const total = qty * price;

                    const totalEl = row.querySelector('.total');
                    if (totalEl) {
                        totalEl.textContent = "Rp " + rupiah(total.toFixed(2));
                        row.querySelector('.line-total').value = total.toFixed(2);
                    }

                    grandTotal += total;
                });

                grandTotalEl.textContent = "Rp " + rupiah(grandTotal.toFixed(2));
            }

            // Recalculate when user types or leaves the price input
            table.addEventListener('input', function(e) {
                if (e.target.classList.contains('price-input')) {
                    calculateTotals();
                }
            });

            // Initial calculation
            calculateTotals();
        });


        function AddGoodReceiptForm() {
            event.preventDefault();
            const btn = document.getElementById("btnSubmit");
            btn.disabled = true;
            const requiredFields = {
                reason: document.getElementById("reason")?.value || "",
                acct_code: document.getElementById("acct_code")?.value || "",
                remarks: document.getElementById("remarks")?.value || ""
            };
            const errorMsg = {
                reason: "Alasan Goods Receipt",
                acct_code: "Account Code",
                remarks: "Remarks"
            };

            const emptyFields = Object.keys(requiredFields).filter(key => !requiredFields[key]);

            if (emptyFields.length > 0) {
                const fieldNames = emptyFields.map(key => `- ${errorMsg[key]}`).join("\n");
                alert("Pastikan field berikut diisi sebelum submit:\n\n" + fieldNames);
                btn.disabled = false;
                return false;
            }

            let form = document.getElementById("goodReceiptForm");
            let formData = new FormData(form);
            showLoadingOverlay("Loading Good Receipt...");
            fetch("/post_gr", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        hideLoadingOverlay();
                        showToast("✅ Berhasil " + data.message, "success");
                        btn.disabled = false;
                        setTimeout(() => {
                            window.location.reload();
                        }, 800)
                    } else {
                        hideLoadingOverlay();
                        if (data.errors) {
                            let errorMessages = Object.values(data.errors).flat().join("\n");
                            showToast("❌ Gagal simpan:\n" + errorMessages, 'error');
                        } else {
                            showToast("❌ Gagal simpan: " + data.message, 'error');
                        }

                        btn.disabled = false;
                    }
                })
                .catch(err => {
                    hideLoadingOverlay();
                    console.error("Error:", err);
                    alert("Terjadi error saat simpan data!");
                    btn.disabled = false;
                });

            return false;
        }

        function showToast(message, type = 'success') {
            const box = document.getElementById('feedbackBox');
            const text = document.getElementById('feedbackMessage');

            box.classList.remove('success', 'error', 'show'); // remove existing styles
            box.classList.add('toast-notification', type, 'show');

            // Update content and styling
            text.textContent = message;
            box.classList.add('toast-notification', type, 'show');
            setTimeout(() => {
                box.classList.remove('show');
                document.getElementById("scannerInput").focus();
            }, 3000);
        }
    </script>
@endsection
