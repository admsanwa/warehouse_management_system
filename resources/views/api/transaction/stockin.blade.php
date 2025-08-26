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
                                    <input type="number" name="on_hand" id="on_hand" class="form-control mt-2" readonly>
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
                                <div class="col-sm-8">
                                    @if (!empty($po))
                                        <input type="number" name="no_po" id="no_po" value="{{ $po }}"
                                            class="form-control mt-2" readonly required>
                                    @else
                                        <select name="no_po" id="no_po" class="form-control mt-2" required>
                                            <option value="">Select Nomor PO</option>
                                        </select>
                                    @endif
                                    <input type="hidden" name="docEntry" id="docEntry" value="{{ $docEntry ?? '' }}" />
                                </div>
                                {{-- <label class="col-sm-4 col-form-label">Good Receipt PO :</label>
                                <div class="col-sm-8">
                                    <input type="hidden" name="grpo" id="grpo" value="{{ $docEntry ?? '' }}"
                                        class="form-control mt-2" readonly required>
                                </div> --}}
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
                        <div class="px-2" id="scannedBarcodes">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-striped table-borderd table-sm nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Item Code</th>
                                            <th>Item Desc</th>
                                            <th>Open Qty</th>
                                            <th>Qty</th>
                                            <th>Uom</th>
                                            {{-- <th>Delete</th> --}}
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
                    </div>
                </form>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">History GRPO</h3>
                    </div>
                    <div class="card-body px-2">
                        <div class="table-responsive">
                            <table class="table table-striped table-borderd table-sm nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No PO</th>
                                        <th>Item Code</th>
                                        <th>Item Desc</th>
                                        <th>Qty</th>
                                        <th>Uom</th>
                                        <th>Tanggal Dibuat</th>
                                    </tr>
                                </thead>
                                <tbody id="grpoHistoriesTbody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Include barcode scanner JS -->
    <script>
        let tempPoData = [];
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
            const poSelect = document.getElementById('no_po');
            poSelect.addEventListener("change", function() {
                clearFormData();
                const tBody = document.getElementById("itemRows");
                tBody.innerHTML = "";
                const selectedDocNum = this.value;
                if (!selectedDocNum) return;
                const selectedPO = tempPoData.find(po => po.DocNum === selectedDocNum);
                // console.log("Selected PO: ", selectedPO);
                appendDataOnPo(selectedPO);
                loadGrpoHistories(selectedPO);
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
            const docEntry = document.getElementById("docEntry").value;
            const noPo = document.getElementById("no_po").value;
            const fileInputWrapper = document.getElementById("fileInput");
            clearFormData();
            const fileInput = fileInputWrapper.querySelector("input[type='file']");
            fileInput.disabled = true;
            fetch("/stockin-add", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        item_code: code,
                        po: noPo,
                        docEntry: docEntry
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
                        const poData = data.poData;
                        if (!poData) {
                            showToast("❌ Error: Nomor PO tidak ditemukan untuk barcode ini", 'error');
                            fileInput.disabled = false;
                            return;
                        }
                        // console.log("posData: ", poData.length);
                        if (poSelect instanceof HTMLSelectElement && Array.isArray(poData) && poData.length > 0) {
                            // simpan ke temporary data untuk select handling
                            tempPoData = poData;

                            // reset isi select
                            poSelect.innerHTML = '<option value="" selected disabled>-- Pilih Nomor PO --</option>';

                            // isi dari data
                            poData.forEach(po => {
                                if (po.DocNum && po.DocNum.trim() !== "") {
                                    const option = document.createElement('option');
                                    option.value = po.DocNum;
                                    option.textContent =
                                        `${po.DocNum} - ${po.CardName ?? ''}`; // lebih informatif
                                    poSelect.appendChild(option);
                                }
                            });
                        } else {
                            appendDataOnPo(poData);
                        }


                        showToast("✅ Success Scan: " + data.ItemName, 'success');
                    } else {
                        // console.log("grpo", data.grpo);
                        showToast("❌ Error: " + data.message, 'error');
                        document.getElementById("scannerInput").focus();
                    }
                })
                .finally(() => {
                    if (fileInput) fileInput.disabled = false;
                    document.getElementById("scannerInput").focus();
                })
                .catch(error => {
                    if (fileInput) fileInput.disabled = false;
                    console.error("Fetch error: ", error);
                    document.getElementById("scannerInput").focus();
                })
        }

        function clearFormData() {
            document.getElementById("cardName").value = "";
            document.getElementById("cardCode").value = "";
            document.getElementById("docDate").value = "";
            document.getElementById("numAtCard").value = "";
            document.getElementById("U_MEB_No_SO").value = "";
            document.getElementById("U_MEB_NO_IO").value = "";
            document.getElementById("itemRows").innerHTML = "";
            document.getElementById("grpoHistoriesTbody").innerHTML = "";
        }

        function appendDataOnPo(data) {
            document.getElementById("docEntry").value = data.DocEntry;
            document.getElementById("cardName").value = data.CardName;
            document.getElementById("cardCode").value = data.CardCode;
            document.getElementById("docDate").value = data.DocDate;
            document.getElementById("numAtCard").value = data.NumAtCard;
            document.getElementById("U_MEB_No_SO").value = data.U_MEB_No_SO;
            document.getElementById("U_MEB_NO_IO").value = data.U_MEB_NO_IO;
            document.getElementById("U_MEB_Ket_Pur").value = data.U_MEB_Ket_Pur;
            // console.log(data);
            loadScannedBarcodes(data.Lines);
            loadGrpoHistories(data);
        }

        function loadScannedBarcodes(items) {
            const fileInput = document.querySelector('#fileInput input[type="file"]');
            if (fileInput) {
                fileInput.value = "";
            }

            const tBody = document.getElementById("itemRows");
            const itemCode = document.getElementById("item_code").value;
            items.forEach((stocks) => {
                if (stocks.ItemCode == itemCode) {
                    const idx = tBody.rows.length;

                    const description = (stocks.Dscription ?? "") +
                        (stocks.FreeTxt ? " - " + stocks.FreeTxt : "");

                    const row = `
                    <tr>
                        <td>${idx + 1}</td>
                        <td>
                            ${stocks.ItemCode}
                            <input type="hidden" name="stocks[${idx}][LineNum]" value="${stocks.LineNum}">
                            <input type="hidden" name="stocks[${idx}][BaseEntry]" value="${stocks.DocEntry}">
                            <input type="hidden" name="stocks[${idx}][ItemCode]" value="${stocks.ItemCode}">
                        </td>
                        <td>
                            ${description}
                            <input type="hidden" name="stocks[${idx}][Dscription]" value="${stocks.Dscription ?? ""}">
                        </td>
                        <td>-</td>
                        <td>
                            <input type="number" name="stocks[${idx}][qty]" class="form-control" style="min-width:80px !important;" value="0">
                            <input type="hidden" name="stocks[${idx}][PriceBefDi]" value="${stocks.PriceBefDi}">
                            <input type="hidden" name="stocks[${idx}][DiscPrcnt]" value="${stocks.DiscPrcnt}">
                            <input type="hidden" name="stocks[${idx}][VatGroup]" value="${stocks.VatGroup}">
                            <input type="hidden" name="stocks[${idx}][AcctCode]" value="${stocks.AcctCode}">
                            <input type="hidden" name="stocks[${idx}][OcrCode]" value="${stocks.OcrCode}">
                            <input type="hidden" name="stocks[${idx}][FreeTxt]" value="${stocks.FreeTxt ?? ""}">
                        </td>
                        <td>
                            ${stocks.UnitMsr ?? ""}
                            <input type="hidden" name="stocks[${idx}][UnitMsr]" value="${stocks.UnitMsr ?? ""}">
                        </td>
                        <td>
                            <button type="button" onclick="deleteItem(this)" class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    `;
                    tBody.insertAdjacentHTML("beforeend", row);
                }
            });
        }


        function deleteItem(button) {
            if (!confirm("Yakin ingin menghapus item ini?")) return;
            const row = button.closest("tr");
            if (row) {
                row.remove();
            }
        }

        function AddStockupForm() {
            event.preventDefault();
            const btn = document.getElementById("btnSubmitStock");
            btn.disabled = true;

            const noPo = document.getElementById("no_po").value;
            const docEntry = document.getElementById("docEntry").value;
            const remark = document.getElementById("remarks").value;
            if (!noPo || !docEntry || !remark) {
                showToast("❌ Error: Pastikan Nomer Purchasing Order dan Remark di isi sebelum submit!")
                btn.disabled = false;
                return false;
            }

            let form = document.getElementById("stockupForm");
            let formData = new FormData(form);
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
                        showToast("✅ Berhasil" + data.message, "success");
                        btn.disabled = false;
                        setTimeout(() => {
                            // window.location.reload();
                        }, 1000)
                    } else {
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
                        <td>${item.qty ?? 0}</td>
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
            }, 3000);
        }

        function formatTimestamp(dateString) {
            if (!dateString) return "-";
            const date = new Date(dateString);
            return date.toLocaleString("id-ID", {
                day: "2-digit",
                month: "long",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            });
        }
    </script>
@endsection
