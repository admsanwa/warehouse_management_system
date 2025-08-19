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
                                    <button type="button" class="btn btn-sm btn-outline-primary mr-1" onclick="startCamera()">Use Camera</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showFileInput()">Upload Image</button>
                                </div>
                                <div id="reader" style="width:300px; display: none;"></div>
                                <div id="fileInput" style="display: none;">
                                    <input type="file" accept="image/*" onchange="scanImage(this)" class="form-control">
                                </div>
                            </div>
                            <label for="" class="col-sm-4 col-form-lable">Item Code :</label>
                            <div class="col-sm-6">
                                <input type="number" name="id" id="id" class="form-control mt-2" hidden>
                                <input type="text" name="item_code" id="item_code" class="form-control mt-2" readonly>
                            </div>
                           <label for="" class="col-sm-4 col-form-lable">Item Description :</label>
                            <div class="col-sm-6">
                                <input type="text" name="item_desc" id="item_desc" class="form-control mt-2" readonly>
                            </div>
                            <label for="" class="col-sm-4 col-form-lable">On Hand :</label>
                            <div class="col-sm-6">
                                <input type="number" name="on_hand" id="on_hand" class="form-control mt-2" readonly>
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
                        <label for="" class="col-sm-4 col-form-lable">Nomer PO :</label>
                        <div class="col-sm-6">
                            @if ($getPos)
                                <input type="number" name="no_po" id="no_po" value="{{ $getPos->no_po }}" class="form-control mt-2" readonly required>
                            @else
                                <select name="no_po" id="no_po" class="form-control mt-2" required>
                                    <option value="">Select Nomer PO</option>
                                </select>
                            @endif
                        </div>
                        <label for="" class="col-sm-4 col-form-lable">Good Receipt PO :</label>
                        <div class="col-sm-6">
                            <input type="number" name="grpo" id="grpo" value="{{ $grpo }}" class="form-control mt-2" readonly required>
                        </div>
                        <label for="" class="col-sm-4 col-form-lable">Remarks :</label>
                        <div class="col-sm-6">
                            <input type="text" name="remarks" id="remarks" class="form-control mt-2" placeholder="Masukkan keterangan proyek disini" required>
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        loadScannedBarcodes();
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
        const grpo = document.getElementById("grpo").value;
        // console.log("code", code);
        fetch("/stockin-add", {
            method: "POST",
            headers: {
                "Content-Type"  : "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN"  :   document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ item_code: code, grpo: grpo })
        })
        .then(res => res.json())
        .then(data => {
            const poSelect = document.getElementById('no_po');
            poSelect.innerHTML = '<option value="">Select Nomer PO</option>'; // Clear old options
     
            if (data.success) {
                // console.log("data", data);
                document.getElementById("id").value = data.id;
                document.getElementById("item_desc").value = data.name;  
                document.getElementById("on_hand").value = data.on_hand;
                
                data.no_po.forEach(po => {
                    const option = document.createElement('option');
                    option.value = po.no_po;
                    option.textContent = po.no_po;
                    poSelect.appendChild(option);
                });

                loadScannedBarcodes();
                showToast("✅ Success Scan: " + data.name, 'success');
            } else {
                // console.log("grpo", data.grpo);
                showToast("❌ Error: " + data.message, 'error');                    
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
        const grpo = document.getElementById("grpo").value;
        // console.log("grpo", grpo);

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
        
        xhr.open("GET", "/scanned-barcodes/" + grpo , true)
        xhr.send();
    }

    function AddStockupForm() {
        const noPo = document.getElementById("no_po").value;
        const grpo = document.getElementById("grpo").value;
        const remark = document.getElementById("remarks").value;

        if (!noPo || !grpo) {
            showToast("❌ Error: Pastikan Nomer Purchasing Order atau Nomer GRPO di isi sebelum submit!")
            return false; // Prevent form submission
        }

        document.getElementById("grpo_hidden").value = grpo;
        document.getElementById("no_po_hidden").value = noPo;
        document.getElementById("remark_hidden").value = remark;
        return true; // Allow form submission
    }

    function deleteItem(id) {
        if (!confirm("Yakin ingin menghapus item ini?")) return;

        fetch("/admin/transaction/stockindelone/" + id, {
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
        .finally(() => {
            document.getElementById("scannerInput").focus();
        })
        .catch((err) => {
            console.error(err);
            loadScannedBarcodes();
            showToast("❌ Error: gagal menghapus data!")
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
