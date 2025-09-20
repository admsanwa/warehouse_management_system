@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="content-header">
                <div class="container-fluid">
                    <section class="row mb-2">
                        <div class="col col-sm-6">
                            <h1>Good Receipt</h1>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <section class="content">
            <input id="scannerInput" type="text" autofocus style="opacity: 0; position: absolute;">
            <div class="container-fluid">
                <div class="card">
                    @include('_message')
                    <div id="feedbackBox" class="toast-notification">
                        <span id="feedbackMessage"></span>
                    </div>
                    <div class="card-header">
                        <h3 class="card-title">Good Receipt</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-lable">Scan Barcode :</label>
                            <div class="col-sm-8">
                                <span class="badge bg-info text-dark mb-2">
                                    <i class="fas fa-info-circle"> Untuk Scan Item/Barang Masuk dr Vendor</i>
                                </span>
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary mr-1"
                                        onclick="startCamera()">Use Camera</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="showFileInput()">Upload Image</button>
                                </div>
                                <div id="reader" style="width: 300px; display:none;"></div>
                                <div id="fileInput" style="display: none;">
                                    <input type="file" accept="image/*" onchange="scanImage(this)" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-lable">Item Code :</label>
                            <div class="col-sm-6">
                                <input type="text" name="item_code" id="item_code" class="form-control mt-2" readonly
                                    required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-lable">Item Description :</label>
                            <div class="col-sm-6">
                                <input type="text" name="item_desc" id="item_desc" class="form-control mt-2" readonly
                                    required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-lable">On Hand :</label>
                            <div class="col-sm-6">
                                <input type="text" name="on_hand" id="on_hand" class="form-control mt-2" readonly
                                    required>
                            </div>
                        </div>
                    </div>

                </div>
                <form id="goodReceiptForm">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recently Scanned</h3>
                        </div>

                        <div class="card-body">
                            <div class="form-group row">
                                <label for="" class="col-sm-4 col-form-lable">PO Maklon : </label>
                                <div class="col-sm-6 mb-2">
                                    @if (!empty($po))
                                        <input type="number" name="docnum" id="docnum" value="{{ $po }}"
                                            class="form-control mt-2" readonly>
                                    @else
                                        <select name="no_po" id="no_po" class="form-control mt-2">
                                        </select>
                                        <input type="hidden" name="docnum" id="docnum" value="{{ $po ?? '' }}" />
                                    @endif
                                    <input type="hidden" name="docEntry" id="docEntry" value="{{ $docEntry ?? '' }}" />
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">Default Warehouse:</label>
                                <div class="col-sm-6 mb-2">
                                    <select name="warehouse" id="warehouse" class="form-control mt-2" required>
                                        <option value="" disabled selected>Pilih Warehouse</option>
                                    </select>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">Alasan Goods Receipt :</label>
                                <div class="col-sm-6 mb-2">
                                    <select name="reason" id="reason" class="form-control mt-2" required>
                                        <option value="" disabled selected>Pilih Alasan</option>
                                        @foreach ($gr_reasons as $item)
                                            <option value="{{ $item['reason_code'] }}"
                                                data-acctcode="{{ $item['acct_code'] }}"
                                                data-islock="{{ $item['acct_lock'] }}">
                                                {{ $item['reason_code'] }} - {{ $item['reason_desc'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">Account Code :</label>
                                <div class="col-sm-6 mb-2">
                                    <input type="text" name="acct_code" id="acct_code" class="form-control mt-2"
                                        placeholder="Masukan Acct Code" required>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">No Surat Jalan :</label>
                                <div class="col-sm-6">
                                    <input type="text" name="no_surat_jalan" id="no_surat_jalan"
                                        class="form-control mt-2" placeholder="Masukkan No Surat Jalan" required>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">No Surat Jalan Barang Datang
                                    :</label>
                                <div class="col-sm-6">
                                    <input type="text" name="ref_surat_jalan" id="ref_surat_jalan"
                                        class="form-control mt-2" placeholder="Masukkan No Surat Jalan Barang Datang"
                                        required>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">Internal No
                                    :</label>
                                <div class="col-sm-6">
                                    <input type="text" name="internal_no" id="internal_no" class="form-control mt-2"
                                        placeholder="Masukkan Internal No" required>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">No IO :</label>
                                <div class="col-sm-6 mb-2">
                                    <input type="text" name="no_io" id="no_io" class="form-control mt-2"
                                        placeholder="Masukkan Nomor IO" required>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">No SO :</label>
                                <div class="col-sm-6 mb-2">
                                    <input type="text" name="no_so" id="no_so" class="form-control mt-2"
                                        placeholder="Masukkan Nomor SO" required>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">No. Good Issue
                                    :</label>
                                <div class="col-sm-6">
                                    <input type="text" name="no_gi" id="no_gi" class="form-control mt-2"
                                        placeholder="Masukkan No Good Issue" required>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">No Inventory Transfer :</label>
                                <div class="col-sm-6">
                                    <input type="text" name="no_inventory_tf" id="no_inventory_tf"
                                        class="form-control mt-2" placeholder="Masukkan No Inventory Transfer" required>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">Type Inventory Transaction :</label>
                                <div class="col-sm-6 mb-2">
                                    <select name="type_inv_transaction" id="type_inv_transaction"
                                        class="form-control mt-2" required>
                                        <option value="" disabled selected>Pilih Inventory Transfer :</option>
                                        @foreach ($inv_trans_reasons as $key => $item)
                                            <option value="{{ $key }}">{{ $key }} - {{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">Project Code:</label>
                                <div class="col-sm-6 mb-2">
                                    <select name="project" id="project" class="form-control mt-2" required>
                                    </select>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">OCR / Distribution Rules :</label>
                                <div class="col-sm-6 mb-2">
                                    <select name="cost_center" id="cost_center" class="form-control mt-2" required>
                                    </select>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">Remarks :</label>
                                <div class="col-sm-6">
                                    <input type="text" name="remarks" id="remarks" class="form-control mt-2"
                                        placeholder="Masukkan Keterangan" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-2" id="scannedBarcodes">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-striped table-borderd table-sm nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Item Code</th>
                                        <th>Item Desc</th>
                                        <th>Qty</th>
                                        <th>Uom</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody id="itemRows">
                                </tbody>
                            </table>
                            <div class="card">
                                <div class="card-footer">
                                    <div class="col col-sm-12">
                                        <button type="submit" onclick="return AddGoodReceiptForm();"
                                            class="btn btn-success float-right" id="btnSubmit"><i
                                                class="fa fa-check"></i>
                                            Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="card">
                    <div class="card-footer">
                        <button onclick="history.back()" class="btn btn-default"><i class="fa fa-arrow-left"></i>
                            Back</button>
                    </div>
                </div>
                <!-- Floating Scanner Button -->
                <div style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; text-align: center;">
                    <button type="button" id="focusScannerBtn" class="btn btn-primary rounded-circle shadow-lg"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-barcode fa-lg"></i>
                    </button>
                    <div style="margin-top: 6px; font-size: 12px; font-weight: 600;" class="text-primary">
                        Ready Scan
                    </div>
                </div>
            </div>
        </section>
    </div>
    {{-- <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script> --}}
    <script>
        window.addEventListener("load", function() {
            const poSelect = $("#no_po");
            formatInputDecimals(document.getElementById("on_hand"));
            // console.log(poSelect.length);
            if (poSelect.is("select")) {
                poSelect.on("change", function(e) {
                    const selectedData = $(this).select2('data')[0];
                    if (!selectedData) {
                        document.getElementById("docnum").value = "";
                        document.getElementById("docEntry").value = "";
                        return;
                    }
                    document.getElementById("docnum").value = selectedData.docnum;
                    document.getElementById("docEntry").value = selectedData.id;
                });
                poSelect.select2({
                    placeholder: "Pilih No. PO Maklon",
                    allowClear: true,
                    width: "100%",
                    minimumInputLength: 3,
                    language: {
                        inputTooShort: function() {
                            return "Ketik 3 karakter atau lebih";
                        },
                        noResults: function() {
                            return "Tidak ada data ditemukan";
                        },
                        searching: function() {
                            return "Mohon ditunggu, Sedang mencari...";
                        }
                    },
                    ajax: {
                        url: "/purchaseOrderSearch",
                        dataType: "json",
                        delay: 250,
                        data: function(params) {
                            let searchData = {
                                q: params.term,
                                limit: 5,
                                // code: 'Maklon',
                                status: 'Open',
                            }
                            return searchData;
                        },
                        processResults: function(data) {
                            console.log("Response dari server:", data); // cek di console
                            return {
                                results: (data.results || []).map(item => ({
                                    id: item.id,
                                    text: item.text,
                                    docnum: item.docnum,
                                }))
                            };
                        }
                    }
                });
            }
            const whSelect = $("#warehouse");
            if (whSelect.length) {
                whSelect.select2({
                    placeholder: "Pilih Kode Warehouse",
                    allowClear: true,
                    width: "100%",
                    language: {
                        inputTooShort: function() {
                            return "Ketik untuk mencari...";
                        },
                        noResults: function() {
                            return "Tidak ada data ditemukan";
                        },
                        searching: function() {
                            return "Sedang mencari...";
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
            const ocrSelect = $("#cost_center");
            if (ocrSelect.length) {
                ocrSelect.select2({
                    placeholder: "Pilih Ocr Code",
                    allowClear: true,
                    width: "100%",
                    language: {
                        inputTooShort: function() {
                            return "Ketik untuk mencari...";
                        },
                        noResults: function() {
                            return "Tidak ada data ditemukan";
                        },
                        searching: function() {
                            return "Sedang mencari...";
                        }
                    },
                    ajax: {
                        url: "/costCenterSearch",
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

            const projectSelect = $("#project");
            if (projectSelect.length) {
                projectSelect.select2({
                    placeholder: "Pilih Project",
                    allowClear: true,
                    width: "100%",
                    language: {
                        inputTooShort: function() {
                            return "Ketik untuk mencari...";
                        },
                        noResults: function() {
                            return "Tidak ada data ditemukan";
                        },
                        searching: function() {
                            return "Sedang mencari...";
                        }
                    },
                    ajax: {
                        url: "/projectSearch",
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

            $("#reason").on("change", function() {
                const selected = $(this).find(":selected");

                const acctCode = selected.data("acctcode");
                const isLock = selected.data("islock");

                $("#acct_code").val(acctCode);

                // atur readonly sesuai islock
                if (isLock === "Y") {
                    $("#acct_code").prop("readonly", true);
                } else {
                    $("#acct_code").prop("readonly", false);
                }
            });
        });
        // Event untuk fokuskan ke #scannerInput saat klik floating button
        document.getElementById("focusScannerBtn").addEventListener("click", function() {
            const scannerInput = document.getElementById("scannerInput");
            scannerInput.value = ""; // kosongkan input dulu
            scannerInput.focus(); // lalu fokus
            console.log("🎯 Fokus ke #scannerInput dari floating button & reset value");

            this.classList.add("btn-success");
            setTimeout(() => this.classList.remove("btn-success"), 500);
        });
        document.addEventListener("keydown", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();

                const scannerInput = document.getElementById("scannerInput");
                const code = scannerInput.value.trim();

                console.log("🔎 Enter ditekan, input terbaca:", code);

                if (code !== "") {
                    document.getElementById("item_code").value = code;
                    console.log("✅ Kirim ke sendScannedCode dengan code:", code);

                    sendScannedCode(code);

                    scannerInput.value = "";
                } else {
                    console.log("⚠️ Tidak ada kode di scannerInput, abaikan.");
                }
                setTimeout(() => {
                    scannerInput.focus();
                    console.log("🎯 Fokus balik ke #scannerInput");
                }, 50);
            }
        });
        let html5QrCode;

        function startCamera() {
            document.getElementById("reader").style.display = "block";
            document.getElementById("fileInput").style.display = "none";

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }

            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    // Try to use the back camera if available
                    const backCamera = devices.find(device => device.label.toLowerCase().includes('back'));
                    const cameraId = backCamera ? backCamera.id : devices[0].id;

                    html5QrCode.start(
                        cameraId, {
                            fps: 15, // Higher FPS for faster detection (try 10–30)
                            qrbox: {
                                width: 300,
                                height: 300
                            }, // Larger box can help accuracy
                            aspectRatio: 1.7777778, // 16:9 ratio for widescreen cams
                            disableFlip: true // Prevent flip issues on mirrored webcams
                        },
                        decodedText => {
                            document.getElementById('item_code').value = decodedText;
                            html5QrCode.stop(); // Stop scanning after successful read
                            sendScannedCode(decodedText);
                        },
                        error => {
                            document.getElementById('item_code').value = decodedText;
                            html5QrCode.stop();
                            sendScannedCode(decodedText);

                            console.warn("Scanning not found", error);
                        }
                    );
                }
            });
        }

        function showFileInput() {
            document.getElementById("reader").style.display = "none";
            document.getElementById("fileInput").style.display = "block";

            if (html5QrCode) {
                html5QrCode.stop().catch(err => {});
            }
        }

        function scanImage(input) {
            if (input.files.length === 0) return;

            const file = input.files[0];
            const html5Qr = new Html5Qrcode( /* element id */ "reader");

            html5Qr.scanFile(file, true)
                .then(decodedText => {
                    document.getElementById('item_code').value = decodedText;
                    document.getElementById('item_desc').value = "";
                    document.getElementById('on_hand').value = "";
                    sendScannedCode(decodedText);
                })
                .catch(err => {
                    alert("Failed to scan image: " + err);
                });
        }

        function sendScannedCode(code) {
            const docEntry = document.getElementById("docEntry").value;
            const noPo = document.getElementById("docnum").value;
            const fileInputWrapper = document.getElementById("fileInput");
            const fileInput = fileInputWrapper.querySelector("input[type='file']");

            fileInput.disabled = true;
            showLoadingOverlay("Scanning Barcode...");
            fetch("/good-receipt", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        item_code: code,
                        // docEntry: docEntry,
                        // po: noPo
                    })
                })
                .then(res => res.json())
                .then(data => {
                    console.log("data", data);
                    if (data.success) {
                        // const pomSelect = document.getElementById("pom");
                        document.getElementById("item_code").value = data.itemCode;
                        document.getElementById("item_desc").value = data.ItemName;
                        document.getElementById("on_hand").value = data.warehouseStock.OnHand;

                        loadScannedBarcodes(data.items);
                        hideLoadingOverlay();
                        showToast("✅ Success Scan: " + data.ItemName, 'success');
                    } else {
                        hideLoadingOverlay();
                        showToast("❌ Error: " + data.message, 'error');
                        document.getElementById("scannerInput").focus();
                    }

                })
                .finally(() => {
                    hideLoadingOverlay();
                    fileInput.disabled = false;
                    document.getElementById("scannerInput").focus();
                })
                .catch(error => {
                    fileInput.disabled = false;
                    console.error("Fetch error: ", error);
                    hideLoadingOverlay();
                    showToast("❌ Gagal Scan Item Code, Koneksi terganggu silakan coba lagi");
                    document.getElementById("scannerInput").focus();
                })
        }

        function loadScannedBarcodes(items) {
            console.log("Items", items);
            const fileInput = document.querySelector('#fileInput input[type="file"]');
            if (fileInput) {
                fileInput.value = "";
            }

            const tBody = document.getElementById("itemRows");
            const itemCode = document.getElementById("item_code").value;
            items.forEach((stocks) => {
                const idx = tBody.rows.length;

                const description = (stocks.Dscription ?? "") +
                    (stocks.FreeTxt ? " - " + stocks.FreeTxt : "");

                const row = `
                    <tr>
                        <td>${idx + 1}</td>
                        <td>
                            ${stocks.ItemCode}
                            <input type="hidden" name="stocks[${idx}][ItemCode]" value="${stocks.ItemCode}">
                        </td>
                        <td>
                            ${stocks.ItemName}
                            <input type="hidden" name="stocks[${idx}][Dscription]" value="${stocks.ItemName}">
                        </td>
                        <td>
                            <input type="text" name="stocks[${idx}][qty]" class="form-control" style="min-width:80px !important;" value="0">
                        <td>
                            ${stocks.InvntryUom ?? ""}
                            <input type="hidden" name="stocks[${idx}][UnitMsr]" value="${stocks.InvntryUom ?? ""}">
                        </td>
                        <td>
                            <button type="button" onclick="deleteItem(this)" class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    `;
                tBody.insertAdjacentHTML("beforeend", row);
                reorderTableRows();
                const newInput = tBody.querySelector(`input[name="stocks[${idx}][qty]"]`);
                if (newInput) {
                    formatInputDecimals(newInput);
                }
            });
        }

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
            fetch("/save_gr", {
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
                            // window.location.reload();
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

        function deleteItem(button) {
            if (!confirm("Yakin ingin menghapus item ini?")) return;
            const row = button.closest("tr");
            if (row) {
                row.remove();
                reorderTableRows();
            }
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

        function reorderTableRows() {
            const tBody = document.getElementById("itemRows");
            [...tBody.rows].forEach((row, index) => {
                row.cells[0].innerText = index + 1;

                const inputs = row.querySelectorAll("input, select, textarea");
                inputs.forEach(input => {
                    if (input.name && input.name.includes("stocks[")) {
                        input.name = input.name.replace(/stocks\[\d+\]/, `stocks[${index}]`);
                    }
                });
            });
        }
    </script>
@endsection
