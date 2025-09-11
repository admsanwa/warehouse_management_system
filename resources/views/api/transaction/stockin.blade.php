@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>Transactions</h1>
                    </div>
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
                        <h3 class="card-title">Stock In</h3>
                    </div>
                    <form action="{{ url('admin/transaction/stockin') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Scan Barcode :</label>
                                <div class="col-sm-8">
                                    <span class="badge bg-info text-dark mb-2">
                                        <i class="fas fa-info-circle"> Untuk Scan item/barang masuk ke Warehouse</i>
                                    </span>
                                    <!-- Buttons to choose method -->
                                    <div class="mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary mr-1"
                                            onclick="startCamera()">Use Camera</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="showFileInput()">Upload Image</button>
                                    </div>
                                    <div id="reader" style="width:300px; display: none;"></div>
                                    <div id="fileInput" style="display: none;">
                                        <input type="file" accept="image/*" onchange="scanImage(this)"
                                            class="form-control">
                                    </div>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">Item Code :</label>
                                <div class="col-sm-6">
                                    <input type="number" name="id" id="id" class="form-control mt-2" hidden>
                                    <input type="text" name="item_code" id="item_code" class="form-control mt-2"
                                        readonly>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">Item Description :</label>
                                <div class="col-sm-6">
                                    <input type="text" name="item_desc" id="item_desc" class="form-control mt-2"
                                        readonly>
                                </div>
                                <label for="" class="col-sm-4 col-form-lable">On Hand :</label>
                                <div class="col-sm-6">
                                    <input type="text" name="on_hand" id="on_hand" class="form-control mt-2" readonly>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <form id="stockupForm">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recently Scanned</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Nomor PO :</label>
                                <div class="col-sm-8 row">
                                    <div class="col-lg-4 col-sm-12 mb-2">
                                        <select name="series" class="form-control" id="seriesSelect"></select>
                                    </div>
                                    <div class="col-lg-8 col-sm-12">
                                        <select name="no_po" id="no_po" class="form-control"
                                            data-docnum="{{ $po ?? '' }}" data-docentry="{{ $docEntry ?? '' }}"
                                            required>
                                            <option value="">Select No Production Order</option>
                                        </select>
                                        <small class="text-muted">Memilih series akan mempermudah pencarian data PO yang
                                            sesuai.</small>
                                    </div>
                                </div>
                                <input type="hidden" name="docNum" id="docNum" value="{{ $po ?? '' }}" />
                                <input type="hidden" name="docEntry" id="docEntry" value="{{ $docEntry ?? '' }}" />
                                <label class="col-sm-4 col-form-label">Vendor:</label>
                                <div class="col-sm-8">
                                    <input type="text" name="cardName" id="cardName" value=""
                                        class="form-control mt-2" readonly required>
                                </div>
                                <label class="col-sm-4 col-form-label">Kode Vendor :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="cardCode" id="cardCode" value=""
                                        class="form-control mt-2" readonly required>
                                </div>
                                <label class="col-sm-4 col-form-label">PO Date :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="docDate" id="docDate" value=""
                                        class="form-control mt-2" readonly required>
                                </div>
                                <label class="col-sm-4 col-form-label">Nomor Surat Jalan :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="numAtCard" id="numAtCard" value=""
                                        class="form-control mt-2" readonly required>
                                </div>
                                <label class="col-sm-4 col-form-label">Nomor IO :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="U_MEB_NO_IO" id="U_MEB_NO_IO" value=""
                                        class="form-control mt-2" readonly required>
                                </div>
                                <label class="col-sm-4 col-form-label">Nomor SO :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="U_MEB_No_SO" id="U_MEB_No_SO" value=""
                                        class="form-control mt-2" readonly required>
                                </div>
                                <label class="col-sm-4 col-form-label">No Internal Production :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="U_MEB_Ket_Pur" id="U_MEB_Ket_Pur" value=""
                                        class="form-control mt-2" readonly required>
                                </div>
                                <label class="col-sm-4 col-form-label">Remarks :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="remarks" id="remarks" class="form-control mt-2"
                                        placeholder="Masukkan keterangan proyek disini" required>
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
                                        <th>Plan Qty</th>
                                        <th>Open Qty</th>
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
                                        <button type="submit" onclick="return AddStockupForm();"
                                            class="btn btn-success float-right" id="btnSubmitStock"><i
                                                class="fa fa-check"></i>
                                            Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-footer">
                            <button onclick="history.back()" class="btn btn-default"><i class="fa fa-arrow-left"></i>
                                Back</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <!-- Include barcode scanner JS -->
    <script>
        let tempPoData = [];
        let selectedPo = [];
        window.addEventListener("load", function() {
            formatInputDecimals(document.getElementById("on_hand"));
            const poSelect = $("#no_po");

            const tBody = document.getElementById('itemRows');
            const docNum = poSelect.data("docnum");
            const docEntry = poSelect.data("docentry");

            poSelect.on("change", function(e) {
                const selectedData = $(this).select2('data')[0];
                // console.log("Selected:", selectedData);
                if (!selectedData) {
                    tBody.innerHTML = "";
                    cleanDataOnPo();
                    return;
                }
                const selectedDocEntry = selectedData.id;
                const selectedDocNum = selectedData.docnum;
                selectedPo = tempPoData.find(
                    item => item.DocNum == selectedDocNum && item.DocEntry == selectedDocEntry
                );
                if (!selectedPo) {
                    cleanDataOnPo();
                    return;
                }
                console.log("PO dipilih:", selectedPo);
                tBody.innerHTML = "";
                appendDataOnPo(selectedPo);
                loadScannedBarcodes();
            });

            poSelect.select2({
                placeholder: "Pilih No. PO",
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
                    delay: 600,
                    data: function(params) {
                        const item_code = document.getElementById("item_code").value;

                        const seriesData = $("#seriesSelect").select2('data');
                        const series = seriesData.length > 0 ? seriesData[0].id : null;

                        if (docNum && docEntry) {
                            return {
                                q: docNum,
                                series: series,
                                docentry: docEntry,
                                limit: 1, // karena spesifik
                            };
                        }
                        return {
                            q: params.term,
                            limit: 5,
                            code: item_code,
                            series: series,
                            status: "Open",
                        };
                    },

                    transport: function(params, success, failure) {
                        const item_code = document.getElementById("item_code").value;

                        // if (!item_code) {
                        //     alert("Item code wajib diisi!");
                        //     return;
                        // }

                        // lanjut request normal
                        var $request = $.ajax(params);
                        $request.then(success);
                        $request.fail(failure);
                        return $request;
                    },
                    processResults: function(data) {
                        tempPoData = data.po || [];
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
                    searchField.placeholder = "Ketik disini untuk cari nomor PO";
                }
            });

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
        })
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("scannerInput");
            input.focus();
            input.addEventListener("keypress", function(e) {
                console.log("Pressed key:", e.key, e.keyCode);
                if (e.key === "Enter") {
                    e.preventDefault(); // ← 🛑 This stops the form from submitting
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
            document.getElementById("item_desc").value = "";
            document.getElementById("on_hand").value = "";
            showLoadingOverlay("Scanning Barcode...");
            fetch("/stockin-add", {
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
                    const poSelect = document.getElementById('no_po');
                    const tBody = document.getElementById('tBody');
                    if (data.success) {
                        // console.log("data", data);
                        document.getElementById("id").value = data.id;
                        document.getElementById("item_desc").value = data.ItemName;
                        document.getElementById("on_hand").value = data.warehouseStock.OnHand;


                        hideLoadingOverlay();
                        const loadScan = loadScannedBarcodes();
                        if (loadScan === false) {
                            return;
                        }
                        showToast("✅ Success Scan: " + data.ItemName, 'success');
                    } else {
                        // console.log("grpo", data.grpo);
                        hideLoadingOverlay();
                        showToast("❌ Error: " + data.message, 'error');
                        document.getElementById("scannerInput").focus();
                    }
                })
                .finally(() => {
                    if (fileInput) fileInput.disabled = false;
                    document.getElementById("scannerInput").focus();
                })
                .catch(error => {
                    hideLoadingOverlay();
                    if (fileInput) fileInput.disabled = false;
                    console.error("Fetch error: ", error);
                    document.getElementById("scannerInput").focus();
                })
        }

        function appendDataOnPo(data) {
            document.getElementById("docNum").value = data.DocNum;
            document.getElementById("docEntry").value = data.DocEntry;
            document.getElementById("cardName").value = data.CardName;
            document.getElementById("cardCode").value = data.CardCode;
            document.getElementById("docDate").value = data.DocDate;
            document.getElementById("numAtCard").value = data.NumAtCard;
            document.getElementById("U_MEB_No_SO").value = data.U_MEB_No_SO;
            document.getElementById("U_MEB_NO_IO").value = data.U_MEB_NO_IO;
            document.getElementById("U_MEB_Ket_Pur").value = data.U_MEB_Ket_Pur;
            // console.log(data);

            // loadGrpoHistories(data);
        }

        function cleanDataOnPo() {
            document.getElementById("docNum").value = "";
            document.getElementById("docEntry").value = "";
            document.getElementById("cardName").value = "";
            document.getElementById("cardCode").value = "";
            document.getElementById("docDate").value = "";
            document.getElementById("numAtCard").value = "";
            document.getElementById("U_MEB_No_SO").value = "";
            document.getElementById("U_MEB_NO_IO").value = "";
            document.getElementById("U_MEB_Ket_Pur").value = "";
            return;
        }

        function loadScannedBarcodes() {
            const fileInput = document.querySelector('#fileInput input[type="file"]');
            if (fileInput) {
                fileInput.value = "";
            }

            if (!selectedPo || selectedPo.length <= 0) {
                console.warn("PO yg dipilih kosong");
                return;
            }

            const tBody = document.getElementById("itemRows");
            const itemCode = document.getElementById("item_code").value;

            if (!itemCode) {
                alert("Harap Scan Barcode terlebih dulu!");
                return;
            }
            const lines = selectedPo['Lines'] || [];

            // filter semua line dengan itemCode sama & masih ada OpenQty
            let matchingLines = lines.filter(item => item.ItemCode === itemCode && item.OpenQty > 0);

            // cek line mana yang sudah pernah dipakai di tabel
            const existingLineNums = [...document.querySelectorAll('#itemRows input[name*="[LineNum]"]')]
                .map(input => parseInt(input.value));

            matchingLines = matchingLines.filter(item => !existingLineNums.includes(item.LineNum));
            if (matchingLines.length === 0 || !matchingLines) {
                console.warn("Item Tidak Ada");
                showToast(`${itemCode} sudah habis atau semua linenum sudah terpakai.`, "error");
                return false;
            }

            const stock = matchingLines[0];

            if (stock.OpenQty <= 0) {
                showToast("Tidak bisa menambahkan barcode ini karena sudah line status close.", "error");
                return false;
            }

            const idx = tBody.rows.length;
            const description = (stock.Dscription ?? "") + (stock.FreeTxt ? " - " + stock.FreeTxt : "");

            const row = `
        <tr>
            <td>${idx + 1}</td>
            <td>
                ${stock.ItemCode}
                <input type="hidden" name="stocks[${idx}][LineNum]" value="${stock.LineNum}">
                <input type="hidden" name="stocks[${idx}][BaseEntry]" value="${stock.DocEntry}">
                <input type="hidden" name="stocks[${idx}][ItemCode]" value="${stock.ItemCode}">
            </td>
            <td>
                ${description}
                <input type="hidden" name="stocks[${idx}][Dscription]" value="${stock.Dscription ?? ""}">
            </td>
            <td> ${formatDecimalsSAP(stock.Quantity)}</td>
            <td> ${formatDecimalsSAP(stock.OpenQty)}</td>
            <td>
                <input type="hidden" name="stocks[${idx}][PlanQty]" value="${stock.Quantity}">
                <input type="hidden" name="stocks[${idx}][OpenQty]" value="${stock.OpenQty}">
                <input type="text" name="stocks[${idx}][qty]" class="form-control format-sap" step="0.01" style="min-width:80px !important;" value="0">
                <input type="hidden" name="stocks[${idx}][PriceBefDi]" value="${stock.PriceBefDi}">
                <input type="hidden" name="stocks[${idx}][DiscPrcnt]" value="${stock.DiscPrcnt}">
                <input type="hidden" name="stocks[${idx}][VatGroup]" value="${stock.VatGroup}">
                <input type="hidden" name="stocks[${idx}][AcctCode]" value="${stock.AcctCode}">
                <input type="hidden" name="stocks[${idx}][OcrCode]" value="${stock.OcrCode}">
                <input type="hidden" name="stocks[${idx}][FreeTxt]" value="${stock.FreeTxt ?? ""}">
            </td>
            <td>
                ${stock.UnitMsr ?? ""}
                <input type="hidden" name="stocks[${idx}][UnitMsr]" value="${stock.UnitMsr ?? ""}">
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
            return;
        }


        function deleteItem(button) {
            if (!confirm("Yakin ingin menghapus item ini?")) return;
            const row = button.closest("tr");
            if (row) {
                row.remove();
                reorderTableRows()
            }
        }

        function AddStockupForm() {
            event.preventDefault();
            const btn = document.getElementById("btnSubmitStock");
            btn.disabled = true;

            const docNum = document.getElementById("docNum").value;
            const docEntry = document.getElementById("docEntry").value;
            const remark = document.getElementById("remarks").value;
            if (!docNum || !docEntry || !remark) {
                showToast("❌ Error: Pastikan Nomer Purchasing Order dan Remark di isi sebelum submit!")
                btn.disabled = false;
                return false;
            }

            let form = document.getElementById("stockupForm");
            let formData = new FormData(form);
            showLoadingOverlay("Loading GRPO...");
            fetch("/admin/transaction/stockup", {
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
                        showToast("✅ Berhasil" + data.message, "success");
                        btn.disabled = false;
                        setTimeout(() => {
                            window.location.reload();
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
                    console.error("Error:", err);
                    hideLoadingOverlay();
                    alert("Terjadi error saat simpan data!");
                    btn.disabled = false;
                });

            return false; // Allow form submission
        }

        function loadGrpoHistories(data) {
            fetch(`/grpo-histories?DocEntry=${data.DocEntry}&DocNum=${data.DocNum}`, {
                    method: "GET",
                    headers: {
                        "Accept": "application/json"
                    }
                })
                .then(response => response.json())
                .then(res => {
                    const tBody = document.getElementById("grpoHistoriesTbody");
                    tBody.innerHTML = "";

                    if (res.success && res.data.length > 0) {
                        res.data.forEach((item, index) => {
                            const trHTML = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.no_po ?? "-"}</td>
                        <td>${item.item_code ?? "-"}</td>
                        <td>${item.item_desc ?? "-"}</td>
                        <td>${formatDecimalsSAP(item.qty)}</td>
                        <td>${item.uom ?? "-"}</td>
                        <td>${item.created_at ?formatTimestamp(item.created_at): "-"}</td>
                    </tr>
                `;
                            tBody.insertAdjacentHTML("beforeend", trHTML);
                        });
                    } else {
                        tBody.insertAdjacentHTML(
                            "beforeend",
                            `<tr><td colspan="7" class="text-center">Data tidak ditemukan</td></tr>`
                        );
                    }
                })
                .catch(err => {
                    console.error("Error:", err);
                    const tBody = document.getElementById("grpoHistoriesTbody");
                    tBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Gagal memuat data</td></tr>`;
                });
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
            }, 6000);
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
