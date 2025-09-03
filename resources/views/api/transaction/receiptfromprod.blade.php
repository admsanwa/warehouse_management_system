@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="content-header">
                <div class="container-fluid">
                    <section class="row mb-2">
                        <div class="col col-sm-6">
                            <h1>Transactions</h1>
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
                        <h3 class="card-title">Receipt from Production</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-lable">Scan Barcode :</label>
                            <div class="col-sm-8">
                                <span class="badge bg-info text-dark mb-2">
                                    <i class="fas fa-info-circle"> Untuk Scan Item/Barang masuk ke Warehouse</i>
                                </span>
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger mr-1"
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

                <form id="prodReceiptForm">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recently Scanned</h3>
                        </div>

                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Nomor Production Order :</label>
                                <div class="col-sm-6 mb-2 row">
                                    <div class="col-lg-4 col-sm-12 mb-2">
                                        <select name="series" class="form-control" id="seriesSelect"></select>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <select name="prod_order" id="prod_order" class="form-control mt-2"
                                            data-docnum="{{ $po ?? '' }}" data-docentry="{{ $docEntry ?? '' }}"
                                            required>
                                            <option value="">Select No Production Order</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="docnum" id="docnum" value="{{ $docNum ?? '' }}" />
                                    <input type="hidden" name="docEntry" id="docEntry" value="{{ $docEntry ?? '' }}" />
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">Production Type:</label>
                                <div class="col-sm-6 mb-2">
                                    <select name="prod_type" id="prod_type" class="form-control mt-2" required>
                                        <option value="" disabled selected>Pilih Production Type</option>
                                        <option value="Assembly">Assembly</option>
                                        <option value="Disassembly">Disassembly</option>
                                    </select>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">Warehouse:</label>
                                <div class="col-sm-6 mb-2">
                                    <select name="warehouse" id="warehouse" class="form-control mt-2" required>
                                        <option value="" disabled selected>Pilih Warehouse</option>
                                    </select>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">Alasan Goods Receipt :</label>
                                <div class="col-sm-6 mb-2">
                                    <select name="reason" id="reason" class="form-control mt-2" required>
                                        <option value="" disabled selected>Pilih Alasan</option>
                                        @foreach ($gr_reason as $key => $item)
                                            <option value="{{ $key }}">{{ $key }} - {{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="no_io" class="col-sm-4 col-form-label">No IO :</label>
                                <div class="col-sm-6 mb-2">
                                    <input type="text" name="no_io" id="no_io" class="form-control mt-2"
                                        placeholder="No IO akan terisi otomatis" readonly>
                                    <small class="text-muted">Nomor IO otomatis terisi dari production order</small>
                                </div>

                                <label for="" class="col-sm-4 col-form-lable">No SO :</label>
                                <div class="col-sm-6 mb-2">
                                    <input type="text" name="no_so" id="no_so" class="form-control mt-2"
                                        placeholder="Nomor SO akan terisi otomatis" readonly>
                                    <small class="text-muted">Nomor SO otomatis terisi dari production order</small>
                                </div>

                                <label for="project" class="col-sm-4 col-form-label">Project Code :</label>
                                <div class="col-sm-6 mb-2">
                                    <input type="text" name="project" id="project" class="form-control mt-2"
                                        placeholder="Project Code akan terisi otomatis" readonly>
                                    <small class="text-muted">Project Code otomatis terisi dari production order</small>
                                </div>
                                <label for="cost_center" class="col-sm-4 col-form-label">OCR / Distribution Rules
                                    :</label>
                                <div class="col-sm-6 mb-2">
                                    <input type="text" name="cost_center" id="cost_center" class="form-control mt-2"
                                        placeholder="Distribution Rules akan terisi otomatis" readonly>
                                    <small class="text-muted">OCR / Distribution Rules otomatis terisi dari production
                                        order</small>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">Remarks :</label>
                                <div class="col-sm-6">
                                    <textarea type="text" name="remarks" id="remarks" class="form-control mt-2" placeholder="Masukkan Keterangan"></textarea>
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
                                        <th>Planned Qty</th>
                                        <th>Receipt Qty</th>
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
                                        <button type="submit" onclick="return AddProdReceiptForm();"
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
            </div>
        </section>
    </div>

    <script>
        let temPoData = [];
        let selectedPo = [];
        window.addEventListener("load", function() {
            const poSelect = $("#prod_order");
            formatInputDecimals(document.getElementById("on_hand"));

            const tBody = document.getElementById('itemRows');
            const docNum = poSelect.data("docnum");
            const docEntry = poSelect.data("docentry");

            poSelect.on("change", function(e) {
                const selectedData = $(this).select2('data')[0];
                console.log(selectedData);
                tBody.innerHTML = "";
                if (!selectedData) {
                    clearProdData();
                    return;
                }
                const selectedDocEntry = selectedData.id;
                const selectedDocNum = selectedData.docnum;
                const found = temPoData.find(item => item.DocNum == selectedDocNum && item.DocEntry ==
                    selectedDocEntry);
                selectedPo = found;
                if (!selectedPo) {
                    console.log("❌ Data tidak ditemukan untuk DocNum:", selectedDocNum);
                    clearProdData();
                    return;
                }
                console.log("Prod dipilih:", selectedPo);
                appendProdData(found);
                loadScannedBarcodes();
            });

            poSelect.select2({
                placeholder: "Pilih No. Production Number",
                allowClear: true,
                width: "100%",
                minimumInputLength: 3,
                language: {
                    inputTooShort: function() {
                        return "Ketik 3 karakter atau lebih";
                    },
                    noResults: () => "Tidak ada data ditemukan",
                    searching: () => "Mohon ditunggu, sedang mencari...",
                },
                ajax: {
                    url: "/productionOrderSearch",
                    dataType: "json",
                    delay: 600,
                    data: function(params) {
                        const item_code = document.getElementById("item_code").value;

                        const seriesData = $("#seriesSelect").select2('data');
                        const series = seriesData.length > 0 ? seriesData[0].id : null;

                        if (docNum && docEntry) {
                            return {
                                q: docNum,
                                docEntry: docEntry,
                                series: series,
                                limit: 1, // karena spesifik
                            };
                        }
                        return {
                            q: params.term,
                            series: series,
                            limit: 5,
                        };
                    },
                    processResults: function(data) {
                        // console.log("🔥 Response server:", data);
                        temPoData = data.prods || [];
                        return {
                            results: (data.results || []).map(item => ({
                                id: item.id,
                                text: item.text,
                                docnum: item.docnum,
                            }))
                        };
                    },

                    cache: true
                }
            });

            poSelect.on("select2:open", function() {
                let searchField = document.querySelector(".select2-container .select2-search__field");
                if (searchField) {
                    searchField.placeholder = "Ketik disini untuk cari production order";
                }
            });

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
            $("#seriesSelect").select2({
                placeholder: "Pilih Series",
                allowClear: true,
                width: "100%",
                language: {
                    inputTooShort: function() {
                        return "Ketik kode series untuk mencari...";
                    },
                    noResults: function() {
                        return "Tidak ada data ditemukan";
                    },
                    searching: function() {
                        return "Sedang mencari...";
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
                            ObjectCode: '202'
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
        });
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("scannerInput");
            input.focus();
            input.addEventListener("keypress", function(e) {
                // console.log("Pressed key:", e.key, e.keyCode);
                if (e.key === "Enter") {
                    e.preventDefault();
                    const code = input.value.trim();
                    if (code !== "") {
                        document.getElementById('item_code').value = code;
                        sendScannedCode(code);
                        input.value = "";
                    }
                }
            });

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
            const fileInputWrapper = document.getElementById("fileInput");
            const fileInput = fileInputWrapper.querySelector("input[type='file']");

            fileInput.disabled = true;
            showLoadingOverlay("Scanning Barcode...");
            fetch("/rfp-add", { //ganti ke 
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        item_code: code,
                    })
                })
                .then(res => res.json())
                .then(data => {
                    hideLoadingOverlay();
                    if (!data.success) {
                        showToast("❌ Error: " + data.message, 'error');
                        return false;
                    }

                    // console.log("data", data);
                    // const pomSelect = document.getElementById("pom");
                    document.getElementById("item_code").value = data.itemCode;
                    document.getElementById("item_desc").value = data.ItemName;
                    document.getElementById("on_hand").value = data.warehouseStock.OnHand;

                    showToast("✅ Success Scan: " + data.ItemName, 'success');
                    loadScannedBarcodes();
                })
                .finally(() => {
                    fileInput.disabled = false;
                    document.getElementById("scannerInput").focus();
                })
                .catch(error => {
                    hideLoadingOverlay();
                    fileInput.disabled = false;
                    console.error("Fetch error: ", error);
                    showToast("❌ Gagal Scan Item Code, Koneksi terganggu silakan coba lagi");
                    document.getElementById("scannerInput").focus();
                })
        }

        function loadScannedBarcodes() {
            const fileInput = document.querySelector('#fileInput input[type="file"]');
            if (fileInput) {
                fileInput.value = "";
            }
            console.log("selectedPo", selectedPo);
            if (!selectedPo || selectedPo.length <= 0) {
                return;
            }

            const tBody = document.getElementById("itemRows");
            const itemCode = document.getElementById("item_code").value;
            const docEntry = document.getElementById("docEntry").value;

            if (!itemCode) {
                alert("Harap Scan Barcode terlebih dulu!");
                return;
            }


            const stocks = selectedPo;
            if (itemCode != stocks.ItemCode) {
                alert(`Item dengan kode ${itemCode} tidak sesuai dengan nomor PO yang dipilih.`);
                return false;
            }
            const idx = tBody.rows.length;
            let inputQty =
                `<input type="text" name="stocks[${idx}][qty]" class="form-control" style="min-width:80px !important;" value="0">`;
            const totalReceiptQty = (stocks.CmpltQty || 0) + (stocks.RjctQty || 0);
            const row = `
                <tr>
                    <td>${idx + 1}</td>
                    <td>
                        ${stocks.ItemCode}
                        <input type="hidden" name="stocks[${idx}][BaseEntry]" value="${docEntry}">
                    </td>
                    <td>${stocks.ItemName}</td>
                    <td>${formatDecimalsSAP(stocks.PlannedQty)}</td>
                    <td>${formatDecimalsSAP(totalReceiptQty)}</td>
                    <td>${inputQty}</td>
                    <td>${stocks.InvntryUoM ?? ""}</td>
                    <td>
                        <button type="button" onclick="deleteItem(this)" class="btn btn-danger btn-sm">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            tBody.insertAdjacentHTML("beforeend", row);

            const newInput = tBody.querySelector(`input[name="stocks[${idx}][qty]"]`);
            if (newInput) {
                formatInputDecimals(newInput);
            }
        }

        function AddProdReceiptForm() {
            event.preventDefault();
            const btn = document.getElementById("btnSubmit");
            btn.disabled = true;
            const requiredFields = {
                reason: document.getElementById("reason")?.value || "",
                // no_surat_jalan: document.getElementById("no_surat_jalan")?.value || "",
                remarks: document.getElementById("remarks")?.value || ""
            };
            const errorMsg = {
                reason: "Alasan Goods Receipt",
                // no_surat_jalan: "No Surat Jalan",
                remarks: "Remarks"
            };

            const emptyFields = Object.keys(requiredFields).filter(key => !requiredFields[key]);

            if (emptyFields.length > 0) {
                const fieldNames = emptyFields.map(key => `- ${errorMsg[key]}`).join("\n");
                alert("Pastikan field berikut diisi sebelum submit:\n\n" + fieldNames);
                btn.disabled = false;
                return false;
            }

            let form = document.getElementById("prodReceiptForm");
            let formData = new FormData(form);
            showLoadingOverlay("Loading Receipt From Production...");
            fetch("/save_prod_receipt", {
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
                        }, 1000)
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

        function appendProdData(data) {
            $("#docNum").val(data.DocNum || "");
            $("#docEntry").val(data.DocEntry || "");
            $("#no_io").val(data.U_MEB_NO_IO || "");
            $("#no_so").val(data.OriginNum || "");
            $("#project").val(data.Project || "");
            $("#cost_center").val(data.OcrCode || "");
            return;
        }

        function clearProdData() {
            $("#docnum").val();
            $("#docEntry").val("");
            $("#no_io").val("");
            $("#no_so").val("");
            $("#project").val("");
            $("#cost_center").val("");
            return;
        }
    </script>
@endsection
