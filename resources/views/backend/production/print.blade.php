@extends("backend.layouts.app")
@section("content")
<style>
    .barcode-wrapper {
        width: 270px; /* fixed width for consistency */
        margin: 15px;
        padding: 10px;
        border: 1px solid #ccc;
        text-align: center;
        display: inline-block;
        vertical-align: top;
        page-break-inside: avoid;
    }

    .barcode-text {
        margin-left: 20px;
        text-align: left;
    }

    .item-code {
        font-weight: bold;
        font-size: 1rem;
        margin: 5px 0;
        color: #333;
    }

    .item-name {
        margin: 5px 0;
        text-align: left;
        white-space: normal; /* allow text to wrap */
        word-wrap: break-word; /* break long words if needed */
        width: 100%;
        font-size: 0.8rem;
        color: #666;
    }

    .barcode img {
        width: 100%; /* barcode matches wrapper width */
        height: auto;
    }

    .barcode {
        margin: 10px 0;
    }

    @media print {
        button {
            display: none !important;
        }
    }
</style>

<div class="text-center mt-3">
    <button onclick="history.back()" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back</button>
    <button onclick="printBarcodes()" class="btn btn-primary"><i class="fa fa-print"></i> Print Barcodes</button>
</div>

<div class="mt-2">
    <div class="container">
        <div class="row justify-content-start">
            @foreach ($addedBarcodes as $barcodes)
                @for ($i = 0; $i < $barcodes->qty; $i++)
                    <div class="barcode-wrapper d-flex align-items-center mb-4">
                        <div class="barcode-image">
                            <img src="data:image/png;base64, {{ DNS2D::getBarcodePNG($barcodes->prod_no . ' ' . $barcodes->prod_desc, "QRCODE", 5, 5) }}" alt="barcodes" 
                            style="height: 100%; width:100%;">
                        </div>
                        <div class="barcode-text me-4 text-end">
                            <div class="item-code">{{ $barcodes->prod_no }}</div>
                            <div class="item-name">{{ $barcodes->prod_desc }}</div>
                            <div class="item-name">NO IO : {{ $barcodes->io }}</div>
                            <div class="item-name">Tanggal produksi : {{ \Carbon\Carbon::parse($barcodes->created_at)->format('d/m/Y') }}</div>
                        </div>
                    </div>
                @endfor
            @endforeach
        </div>
    </div>
</div>

<script>
    function printBarcodes() {
        window.print();
    }
</script>
@endsection