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
                    <form action="{{ url('admin/transaction/goodreceipt')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-lable">Scan Barcode :</label>
                                <div class="col-sm-8">
                                    <span class="badge bg-info text-dark mb-2">
                                        <i class="fas fa-info-circle"> Untuk Scan Item/Barang Masuk dr Vendor</i>
                                    </span>
                                    <div class="mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary mr-1" onclick="startCamera()">Use Camera</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showFileInput()">Upload Image</button>
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
                                    <input type="text" name="item_code" id="item_code" class="form-control mt-2" readonly required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-lable">Item Description :</label>
                                <div class="col-sm-6">
                                    <input type="text" name="item_desc" id="item_desc" class="form-control mt-2" readonly required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-lable">On Hand :</label>
                                <div class="col-sm-6">
                                    <input type="number" name="on_hand" id="on_hand" class="form-control mt-2" readonly required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recently Scanned</h3>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                            <label for="" class="col-sm-4 col-form-lable">IO :</label>
                            <div class="col-sm-6">
                                <select name="io" id="io" class="form-control mt-2" required>
                                    <option value="">Select Nomer IO</option>
                                </select>
                            </div>
                            <label for="" class="col-sm-4 col-form-lable">PO Maklon : </label>
                            <div class="col-sm-6">
                                <select name="pom" id="pom" class="form-control mt-2" required>
                                    <option value="">Select Nomer Purchase Order Maklon</option>
                                </select>
                            </div>
                            <label for="" class="col-sm-4 col-form-lable">Internal No : </label>
                            <div class="col-sm-6">
                                <select name="internal_no" id="internal_no" class="form-control mt-2" required>
                                    <option value="">Select Internal Nomer</option>
                                </select>
                            </div>
                            <label for="" class="col-sm-4 col-form-lable">Good Receipt :</label>
                            <div class="col-sm-6">
                                <input type="number" name="gr" id="gr" value="{{ $gr }}" class="form-control mt-2" readonly required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-0" id="scannedBarcodes"></div>
                <div class="card">
                   <div class="card-footer">
                        <button onclick="history.back()" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</button>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script> --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            loadScannedBarcodes();
            const input = document.getElementById("scannerInput");

            input.focus();
            input.addEventListener("keypress", function(e) {
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
                        cameraId,
                        {
                            fps: 15,                  // Higher FPS for faster detection (try 10–30)
                            qrbox: { width: 300, height: 300 }, // Larger box can help accuracy
                            aspectRatio: 1.7777778,   // 16:9 ratio for widescreen cams
                            disableFlip: true         // Prevent flip issues on mirrored webcams
                        },
                        decodedText => {
                            document.getElementById('item_code').value = decodedText;
                            html5QrCode.stop();       // Stop scanning after successful read
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
            const html5Qr = new Html5Qrcode(/* element id */ "reader");

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
            // console.log("code", code);
            const greceipt = document.getElementById("gr").value;
            // console.log("greceipt", greceipt);
            fetch("/good-receipt", {
                method: "POST",
                headers: {
                    "Content-Type"  : "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN"  :   document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ item_code: code, gr: greceipt})
            })
            .then(res => res.json())
            .then(data => {
                // console.log("data", data);
                const ioSelect  = document.getElementById("io");
                const pomSelect = document.getElementById("pom");
                const inoSelect = document.getElementById("internal_no");
                ioSelect.innerHTML = '<option value="">Select Nomer IO</option>';
                pomSelect.innerHTML = '<option value="">Select Nomer Purchase Order Maklon</option>';
                inoSelect.innerHTML = '<option value="">Select Internal Nomer</option>'
        
                document.getElementById("on_hand").value = data.on_hand;
                document.getElementById("item_desc").value = data.name;

                data.pos.forEach(pom => {
                    const option = document.createElement("option");
                    option.value = pom;
                    option.textContent = pom;
                    pomSelect.appendChild(option);
                });

                data.io.forEach(ios => {
                    const option = document.createElement("option");
                    option.value = ios;
                    option.textContent = ios;
                    ioSelect.appendChild(option);
                });


                data.internal_no.forEach(ino => {
                    const option = document.createElement("option");
                    option.value = ino;
                    option.textContent  = ino;
                    inoSelect.appendChild(option);
                })

                // console.log("po", data.no_po, "io", data.io_no, "prod", data.doc_num);
                loadScannedBarcodes();
                showToast("✅ Success Scan: " + data.name, 'success');
            })
            .catch(error => {
                console.error("Fetch error: ", error);
                showToast("❌ Error: " + data.message, 'error')                    
                document.getElementById("scannerInput").focus();
            })
        }

        function loadScannedBarcodes() {
            let xhr = new XMLHttpRequest();
            let container = document.getElementById("scannedBarcodes");
            let gr = document.getElementById("gr").value;

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    container.innerHTML = xhr.responseText;

                    document.getElementById("scannerInput").focus();
                    document.getElementById("fileInput").style.display = "none";
                    const fileInput = document.querySelector('#fileInput input[type="file"]');
                    if (fileInput) {
                        fileInput.value = "";
                    }
                }
            }

            xhr.open("GET", "/scanned-barcodes-gr/" + gr , true)
            xhr.send();
        }

        function AddGoodReceiptForm() {
            const po = document.getElementById("pom").value;
            const io = document.getElementById("io").value;
            const internal_no   = document.getElementById("internal_no").value;
            const gr = document.getElementById("gr").value;
            // console.log("io", io, "po", po, "gr", gr);

            if (!po || !gr) {
                alert("Pastikan Nomer Purchase Order atau Nomer IO di isi sebelum submit.");
                return false; // Prevent form submission
            }

            document.getElementById("io_hidden").value = io;
            document.getElementById("po_hidden").value = po;
            document.getElementById("gr_hidden").value = gr;
            document.getElementById("internal_no_hidden").value = internal_no;
            return true; // Allow form submission
        }

        function deleteItem(id) {
            if (!confirm("Yakin ingin menghapus item ini?")) return;

            fetch("/admin/transaction/grdelone/" + id, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").getAttribute("content"),
                },
            })
            .then((response) => {
                if (!response.ok) throw new Error("Gagal menghapus item");
                return response.json();
            })
            .then((data) => {
                // console.log("Deleted:", data);
                loadScannedBarcodes();
                showToast("✅ Item berhasil dihapus", "success");
            })
            .catch((err) => {
                console.error(err);
                alert("Gagal menghapus data.");
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

    </script>   
@endsection