@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>Inventory Transfer</h1>
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
                        <h3 class="card-title">Create Inventory Transfer</h3>
                    </div>
                    <form enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Scan Barcode :</label>
                                <div class="col-sm-8">
                                    <span class="badge bg-info text-dark mb-2">
                                        <i class="fas fa-info-circle"> Untuk Scan item/barang inventory transfer</i>
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
                                {{-- <label class="col-sm-4 col-form-label">Kode Vendor :</label>
                                <div class="col-sm-8 mb-2">
                                    <input type="text" name="cardCode" id="cardCode" value=""
                                        class="form-control mt-2" readonly required>
                                </div> --}}
                                {{-- <label class="col-sm-4 col-form-label">Posting Date :</label>
                                <div class="col-sm-8 mb-2">
                                    <input type="date" name="PostingDate" id="PostingDate" value=""
                                        class="form-control mt-2" readonly required>
                                </div> --}}
                                <label class="col-sm-4 col-form-label mb-2">From Warehouse :</label>
                                <div class="col-sm-8">
                                    <select name="FromWhsCode" id="FromWhsCode" class="form-control mt-2" required>
                                        <option value="" disabled selected>Select From Warehouse</option>
                                    </select>
                                </div>
                                <label class="col-sm-4 col-form-label">To Warehouse :</label>
                                <div class="col-sm-8">
                                    <select name="ToWhsCode" id="ToWhsCode" class="form-control mt-2" required>
                                        <option value="">Select To Warehouse</option>
                                    </select>
                                </div>
                                <label class="col-sm-4 col-form-label">Default Warehouse :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="U_MEB_Default_Whse" id="U_MEB_Default_Whse"
                                        value="" class="form-control mt-2" readonly required>
                                </div>
                                <label class="col-sm-4 col-form-label">Nomor Surat Jalan :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="U_SI_No_Surat_Jalan" id="U_SI_No_Surat_Jalan"
                                        value="" class="form-control mt-2" placeholder="Input No Surat Jalan"
                                        required>
                                </div>
                                <label class="col-sm-4 col-form-label">Ref 2 (No SJ barang datang) :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="Ref2" id="Ref2" value=""
                                        placeholder="Input Ref 2 (No SJ barang datang)" class="form-control mt-2"
                                        required>
                                </div>
                                <label class="col-sm-4 col-form-label">Nomor IO :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="U_MEB_NO_IO" id="U_MEB_NO_IO" value=""
                                        class="form-control mt-2">
                                </div>
                                <label class="col-sm-4 col-form-label">Nomor SO :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="U_MEB_No_SO" id="U_MEB_No_SO" value=""
                                        class="form-control mt-2">
                                </div>
                                <label class="col-sm-4 col-form-label">Contract Adendum :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="U_MEB_ProjectDetail" id="U_MEB_ProjectDetail"
                                        value="" class="form-control mt-2" readonly>
                                </div>
                                <label class="col-sm-4 col-form-label">Internal No :</label>
                                <div class="col-sm-8 mb-2">
                                    <input type="text" name="U_MEB_Internal_No" id="U_MEB_Internal_No" value=""
                                        class="form-control mt-2" placeholder="Masukan Nomor Internal Production">
                                </div>
                                <label class="col-sm-4 col-form-label">Project Code :</label>
                                <div class="col-sm-8 mb-2">
                                    <select name="U_MEB_Project_Code" id="U_MEB_Project_Code" class="form-control mt-2"
                                        required>
                                    </select>
                                </div>
                                <label class="col-sm-4 col-form-label">Refer No Good Issue :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="U_MEB_NO_GI" id="U_MEB_NO_GI" value=""
                                        placeholder="Input Refer No Good Issue" class="form-control mt-2" required>
                                </div>

                                <label class="col-sm-4 col-form-label">Inventory Transfer Asal :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="U_MEB_No_Inv_Trf_Asa" id="U_MEB_No_Inv_Trf_Asa"
                                        value="" placeholder="Input Inventory Asal" class="form-control mt-2"
                                        required>
                                </div>
                                <label class="col-sm-4 col-form-label">Type Inventory Transaction :</label>
                                <div class="col-sm-8">
                                    <select name="U_MEB_Type_Inv_Trans" id="U_MEB_Type_Inv_Trans"
                                        class="form-control mt-2" required>
                                        <option value="" disabled selected>Pilih Inventory Transfer</option>
                                        @foreach ($inv_trans_reasons as $key => $item)
                                            <option value="{{ $key }}">{{ $key }} - {{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="col-sm-4 col-form-label">Distribution Rule :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="U_MEB_Dist_Rule" id="U_MEB_Dist_Rule" value="BK-FIN"
                                        class="form-control mt-2" readonly required>
                                </div>
                                <label class="col-sm-4 col-form-label">No Produksi :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="U_SI_No_Produksi" id="U_SI_No_Produksi" value=""
                                        class="form-control mt-2" placeholder="Input No Produksi" required>
                                </div>
                                <label class="col-sm-4 col-form-label">No Production Order :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="U_MEB_No_Prod_Order" id="U_MEB_No_Prod_Order"
                                        value="" placeholder="Input No Production Order" class="form-control mt-2"
                                        required>
                                </div>
                                <label class="col-sm-4 col-form-label">Hari & Tanggal Kirim :</label>
                                <div class="col-sm-8">
                                    <input type="date" name="U_SI_HARI_TGL_KIRIM" id="U_SI_HARI_TGL_KIRIM"
                                        class="form-control mt-2" required>
                                    <input type="text" name="tanggal_display" id="tanggal_display"
                                        class="form-control mt-2" placeholder="hari & tanggal" readonly>
                                </div>
                                <label class="col-sm-4 col-form-label">Lokasi :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="U_SI_Lokasi" id="U_SI_Lokasi" value=""
                                        placeholder="Input Lokasi" class="form-control mt-2" required>
                                </div>
                                <label class="col-sm-4 col-form-label">Komponen Tambahan :</label>
                                <div class="col-sm-8 mb-2">
                                    <input type="text" name="U_SI_KMPN_TMBHN" id="U_SI_KMPN_TMBHN" value=""
                                        placeholder="Input Komponen Tambahan" class="form-control mt-2" required>
                                </div>
                                <label class="col-sm-4 col-form-label">Sales Employee :</label>
                                <div class="col-sm-8">
                                    <select name="SlpCode" id="SlpCode" class="form-control mt-2">
                                        <option value="">Select Sales Employee</option>
                                        {{-- @foreach ($buyers as $key => $buyer)
                                            <option value="{{ $buyer->code }}">{{ $buyer->code }} - {{ $buyer->name }}
                                            </option>
                                        @endforeach --}}
                                    </select>
                                </div>
                                <label class="col-sm-4 col-form-label">Journal Remarks :</label>
                                <div class="col-sm-8">
                                    <input type="text" name="remarks" id="remarks" class="form-control mt-2"
                                        placeholder="Input journal remark" required>
                                </div>
                                <label class="col-sm-4 col-form-label">Remarks :</label>
                                <div class="col-sm-8">
                                    <textarea type="text" name="Comments" id="Comments" class="form-control mt-2" placeholder="Input keterangan"
                                        required></textarea>
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
                                        <th>From WH</th>
                                        <th>To WH</th>
                                        <th>Qty</th>
                                        <th>Uom</th>
                                        {{-- <th>Jumlah Kemasan</th> --}}
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody id="itemRows">
                                </tbody>
                            </table>
                            <div class="card">
                                <div class="card-footer">
                                    <div class="col col-sm-12">
                                        <button type="submit" onclick="return SubmitTransfer();"
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
                </form>
            </div>
        </section>
    </div>

    <!-- Include barcode scanner JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
                // tBody.innerHTML = "";
                appendDataOnPo(selectedPo);
                // loadScannedBarcodes(data.items);
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
                            ObjectCode: '67'
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
            const prefix = {!! json_encode(Auth::user()->default_series_prefix) !!};
            const defaultWhs = {!! json_encode(Auth::user()->warehouse_access) !!};
            setDefaultSeries("#seriesSelect", "22", prefix);
            setDefaultWarehouse("#FromWhsCode", defaultWhs);

            const projectSelect = $("#U_MEB_Project_Code");
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
            $("#ToWhsCode").on("change", function() {
                const $toWhs = $(this);
                const toWhsCode = ($toWhs.val() || "").trim();
                const fromWhsCode = ($("#FromWhsCode").val() || "").trim();

                // Jika sama, tampilkan warning dan reset
                if (toWhsCode && toWhsCode === fromWhsCode) {
                    showToast("❌ Warning: From Warehouse tidak boleh sama dengan To Warehouse", "error");
                    $toWhs.val("").trigger("change"); // ✅ perbaikan di sini
                    return;
                }

                // Update warehouse default
                $("#U_MEB_Default_Whse").val(toWhsCode);
            });
            // $('#SlpCode').select2();
        });

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
                    sendScannedCode(decodedText);
                })
                .catch(err => {
                    alert("Failed to scan image: " + err);
                });
        }

        function sendScannedCode(code) {
            const fromWhsInput = document.getElementById('FromWhsCode');
            const toWhsInput = document.getElementById('ToWhsCode');
            const itemInput = document.getElementById('item_code');
            const fromWhs = fromWhsInput.value.trim();
            const toWhs = toWhsInput.value.trim();
            const fileInputWrapper = document.getElementById("fileInput");
            const fileInput = fileInputWrapper.querySelector("input[type='file']");
            if (!fromWhs) {
                itemInput.value = "";

                showToast("❌ Warning: From Warehouse tidak boleh kosong, isi dulu", "error");
                return;
            }
            if (!toWhs) {
                itemInput.value = "";
                fileInput.value = "";
                showToast("❌ Warning: To Warehouse tidak boleh kosong, isi dulu", "error");
                return;
            }


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
                        const loadScan = loadScannedBarcodes(data.warehouseStock);
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
            // document.getElementById("cardCode").value = data.CardCode;
            // const postingDateInput = document.getElementById("PostingDate");
            // if (postingDateInput && data.DocDate) {
            //     const formattedDate = data.DocDate.replace(/\//g, "-");
            //     postingDateInput.value = formattedDate;
            // }
            document.getElementById("U_MEB_No_SO").value = data.U_MEB_No_SO;
            document.getElementById("U_MEB_NO_IO").value = data.U_MEB_NO_IO;
            document.getElementById("U_MEB_ProjectDetail").value = data.CntctCode;
            document.getElementById("remarks").value = "Based On Purchase Order " + data.DocNum;
        }

        function cleanDataOnPo() {
            document.getElementById("docNum").value = "";
            document.getElementById("docEntry").value = "";
            document.getElementById("cardName").value = "";
            // document.getElementById("cardCode").value = "";
            // document.getElementById("PostingDate").value = "";
            document.getElementById("U_MEB_No_SO").value = "";
            document.getElementById("U_MEB_NO_IO").value = "";
            document.getElementById("remarks").value = "";
            return;
        }

        function loadScannedBarcodes(item) {
            const fileInput = document.querySelector('#fileInput input[type="file"]');
            if (fileInput) fileInput.value = "";

            const tBody = document.getElementById("itemRows");
            const itemCode = document.getElementById("item_code").value;

            if (!itemCode) {
                showToast("⚠️ Harap scan barcode terlebih dahulu!", "error");
                return false;
            }

            const fromWhsCode = document.getElementById('FromWhsCode').value;
            const toWhsCode = document.getElementById('ToWhsCode').value;

            const existing = Array.from(tBody.querySelectorAll('input[name^="stocks"][name$="[ItemCode]"]'))
                .some(input => input.value === item.ItemCode);
            if (existing === true) {
                showToast(`❌ Barcode ${item.ItemCode} sudah ditambahkan sebelumnya.`, "error");
                return false;
            }

            const idx = tBody.rows.length;
            const row = `
                <tr>
                    <td>${idx + 1}</td>
                    <td>
                        ${item.ItemCode}
                        <input type="hidden" name="stocks[${idx}][ItemCode]" value="${item.ItemCode}">
                    </td>
                    <td>${item.ItemName}</td>
                    <td>${fromWhsCode}</td>
                    <td>${toWhsCode}</td>
                    <td>
                        <input type="hidden" name="stocks[${idx}][FromWhsCode]" value="${fromWhsCode}">
                        <input type="hidden" name="stocks[${idx}][ToWhsCode]" value="${toWhsCode}">
                        <input type="text" name="stocks[${idx}][qty]" class="form-control format-sap" step="0.01" style="min-width:80px !important;" value="0">
                    </td>
                    <td>
                        ${item.UnitMsr ?? ""}
                        <input type="hidden" name="stocks[${idx}][UnitMsr]" value="${item.UnitMsr ?? ""}">
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
        }


        function deleteItem(button) {
            if (!confirm("Yakin ingin menghapus item ini?")) return;
            const row = button.closest("tr");
            if (row) {
                row.remove();
                reorderTableRows()
            }
        }

        function SubmitTransfer() {
            event.preventDefault();
            const btn = document.getElementById("btnSubmitStock");
            btn.disabled = true;

            const docNum = document.getElementById("docNum").value;
            const docEntry = document.getElementById("docEntry").value;
            const remark = document.getElementById("remarks").value;
            if (!remark) {
                showToast("❌ Error: Pastikan Nomer Purchasing Order dan Remark di isi sebelum submit!")
                btn.disabled = false;
                return false;
            }

            let form = document.getElementById("stockupForm");
            let formData = new FormData(form);
            showLoadingOverlay("Loading Transfer...");
            fetch("/admin/inventorytf/transfer", {
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
                    console.error("Error:", err);
                    hideLoadingOverlay();
                    alert("Terjadi error saat simpan data!");
                    btn.disabled = false;
                });

            return false; // Allow form submission
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

        // select wh
        $(document).ready(function() {
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


            const inputTanggal = document.getElementById('U_SI_HARI_TGL_KIRIM');
            const display = document.getElementById('tanggal_display');

            inputTanggal.addEventListener('change', function() {
                if (!this.value) return;
                const date = new Date(this.value);

                const hari = date.toLocaleDateString('id-ID', {
                    weekday: 'long'
                });
                const tanggal = date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });

                display.value = `${hari.charAt(0).toUpperCase() + hari.slice(1)}, ${tanggal}`;
            });

        });
    </script>
@endsection
