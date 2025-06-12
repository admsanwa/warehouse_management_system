@extends('backend.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-6">
                <div class="col col-sm-6">
                    <h1>Transactions</h1>
                </div>
                <div class="col col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <a href="{{ url('admin/transaction/stockout') }}" class="btn btn-primary">Stock Out</a>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                @include('_message')
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
                                    <input type="text" id="scannerInput" placeholder="Scan Barcode..." autofocus autocomplete="off" />
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
                            <select name="no_po" id="no_po" class="form-control mt-2" required>
                                <option value="">Select Nomer PO</option>
                                {{-- @foreach ($pos as $po)
                                    <option value="{{ $po->no_po }} {{ $po->no_po == $po->no_po ? "selected" : ""}}">
                                        {{ $po->no_po }}
                                    </option>
                                @endforeach --}}
                            </select>
                        </div>
                        <label for="" class="col-sm-4 col-form-lable">Good Receipt PO :</label>
                        <div class="col-sm-6">
                            <input type="number" name="grpo" id="grpo" value="{{ $grpo }}" class="form-control mt-2" readonly>
                        </div>
                    </div>
                </div>
                </form>
                <div class="p-0" id="scannedBarcodes"></div>
            </div>
        </div>
    </section>
</div>

<!-- Include barcode scanner JS -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const input = document.getElementById("scannerInput");

        input.focus();
        input.addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                const code = input.value.trim();
                if (code !== "") {
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
                // console.log("grpo", data.grpo);
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
                alert("Success Scan: " + data.name);
            } else {
                // console.log("grpo", data.grpo);
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Fetch error: ", error);
        })
    }

    function loadScannedBarcodes() {
        let xhr = new XMLHttpRequest();
        let container = document.getElementById("scannedBarcodes");
        const grpo = document.getElementById("grpo").value;

        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                container.innerHTML = xhr.responseText;
            }
        }

        xhr.open("GET", "/scanned-barcodes/" + grpo , true)
        xhr.send();
    }

    function AddStockupForm() {
        const noPo = document.getElementById("no_po").value;
        const grpo = document.getElementById("grpo").value;

        if (!noPo) {
            alert("Please select a valid Nomer PO before submitting.");
            return false; // Prevent form submission
        }

        document.getElementById("grpo_hidden").value = grpo;
        document.getElementById("no_po_hidden").value = noPo;
        return true; // Allow form submission
    }
</script>   
@endsection
