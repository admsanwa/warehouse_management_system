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
                    <h3 class="card-title">Receipt from Production</h3>
                </div>
                <form action="{{ url('admin/transaction/rfp') }}" method="post" enctype="multipart/form-data">
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
                                    <button type="button" class="btn btn-sm btn-outline-primary mr-1" onclick="startCamera()">Use Camera</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showFileInput()">Upload Image</button>
                                </div>
                                <div id="reader" style="width:300px; display: none;"></div>
                                <div id="fileInput" style="display: none;">
                                    <input type="file" accept="image/*" onchange="scanImage(this)" class="form-control">
                                </div>
                            </div>
                            <label for="" class="col-sm-4 col-form-lable">Product Nomer :</label>
                            <div class="col-sm-6">
                                <input type="number" name="id" id="id" class="form-control mt-2" hidden>
                                <input type="text" name="prod_no" id="prod_no" class="form-control mt-2" readonly>
                            </div>
                            <label for="" class="col-sm-4 col-form-lable">Product Description :</label>
                            <div class="col-sm-6">
                                <input type="text" name="prod_desc" id="prod_desc" class="form-control mt-2" readonly>
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
                        <label for="" class="col-sm-4 col-form-lable">Production Order :</label>
                        <div class="col-sm-6">
                            @if ($getPos) 
                                <input type="number" name="po" id="po"  class="form-control mt-2" readonly required>
                            @else 
                                <select name="po" id="po" class="form-control mt-2" required>
                                    <option value="">Select Nomer PO</option>
                                </select>
                            @endif
                        </div>
                        <label for="" class="col-sm-4 col-form-lable">Number :</label>
                        <div class="col-sm-6">
                            <input type="number" name="number" id="number" value="{{ $number }}" class="form-control mt-2" readonly required>
                        </div>
                        <label for="" class="col-sm-4 col-form-lable">Alasan Receipt Production :</label>
                        <div class="col-sm-6">
                            <input type="text" name="reason" id="reason" class="form-control mt-2" placeholder="Masukkan Alasan Receipt Production">
                        </div>
                        <label for="" class="col-sm-4 col-form-lable">Remarks :</label>
                        <div class="col-sm-6">
                            <input type="text" name="remarks" id="remarks" class="form-control mt-2" placeholder="Masukkan Keterangan">
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
        </div>
    </section>
</div>

<!-- Include barcode scanner JS -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
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
                    const parts = code.trim().split(/\s+/);
                    const prod_no = parts[0];
                    const prod_desc = parts.slice(1).join(" ");
                    document.getElementById('prod_no').value = prod_no;
                    document.getElementById("prod_desc").value = prod_desc;

                    sendScannedCode(prod_no);
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
                        const parts = decodedText.trim().split(/\s+/);
                        const prod_no = parts[0];
                        const prod_desc = parts.slice(1).join(" ");
                        document.getElementById('prod_no').value = prod_no;
                        document.getElementById('prod_desc').value = prod_desc;
                        html5QrCode.stop();       // Stop scanning after successful read
                        sendScannedCode(prod_no);
                    },
                    error => {
                        document.getElementById('prod_no').value = decodedText;
                        html5QrCode.stop();
                        sendScannedCode(prod_no);

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
                try {
                    const parts = decodedText.trim().split(/\s+/);
                    const prod_no = parts[0];
                    const prod_desc = parts.slice(1).join(" ");

                    document.getElementById("prod_no").value = prod_no;
                    document.getElementById("prod_desc").value = prod_desc;
                    sendScannedCode(prod_no);
                } catch (err) {
                    console.error("Error handling scan result:", err);
                    alert("Error handling scan result: " + err.message);
                }
            })
            .catch(err => {
                alert("Failed to scan image: " + err);
            });
    }

    function sendScannedCode(prod_no) {
        const number = document.getElementById("number").value;
        const prod_desc = document.getElementById("prod_desc").value;
        // console.log("code", code);
        fetch("/rfp-add", {
            method: "POST",
            headers: {
                "Content-Type"  : "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN"  :   document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ prod_no: prod_no, number: number, prod_desc: prod_desc })
        })
        .then(res => res.json())
        .then(data => {
            // console.log("data", data);
            const posSelect = document.getElementById("po");
            posSelect.innerHTML = '<option value="">Select Nomer PO</option>';
     
            if (data.success) {
                // console.log("data", data);
                document.getElementById("id").value = data.id;
    
                data.po.forEach(pos => {
                    const option = document.createElement("option");
                    option.value = pos.doc_num;
                    option.textContent = pos.doc_num;
                    posSelect.appendChild(option);
                })

                loadScannedBarcodes();
                showToast("✅ Success Scan: " + data.prod_no, 'success');
            } else {
                // console.log("prod_order", data.prod_order);
                showToast("❌ Error: " + data.message, 'error')                    
                document.getElementById("scannerInput").focus();
            }
        })
        .finally(() => {
            document.getElementById("scannerInput").focus();
        })
        .catch(error => {
            console.error("Fetch error: ", error);
            document.getElementById("scannerInput").focus();
        })
    }

    function loadScannedBarcodes() {
        let xhr = new XMLHttpRequest();
        let container = document.getElementById("scannedBarcodes");
        const number = document.getElementById("number").value;

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

        xhr.open("GET", "/scanned-barcodes-rfp/" + number , true)
        xhr.send();
    }

    function AddStockupForm() {
        const po = document.getElementById("po").value;
        const number = document.getElementById("number").value;
        const reason = document.getElementById("reason").value;
        const remarks = document.getElementById("remarks").value;
        console.log("po", po);

        if (!po ) {
            alert("Pastikan mengisi nomer Production Order di isi sebelum submit.");
            return false; // Prevent form submission
        }

        document.getElementById("po_hidden").value = po;
        document.getElementById("number_hidden").value = number;
        document.getElementById("reason_hidden").value = reason;
        document.getElementById("remarks_hidden").value = remarks;

        return true; // Allow form submission
    }

    function deleteItem(id) {
        if (!confirm("Yakin ingin menghapus item ini?")) return;

        fetch("/admin/transaction/rfpdelone/" + id, {
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
