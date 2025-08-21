@extends('backend.layouts.app')

@section('content')
<style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    .container {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        align-items: flex-start;
    }

    .barcode-wrapper {
        width: 7.5cm;
        height: 2.5cm;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        padding: 0.2cm 0.3cm;
        overflow: hidden;
    }

    .barcode-image img {
        width: 2.0cm;
        height: 2.0cm;
        display: block;
    }

    .barcode-text {
        margin-left: 0.4cm;
        max-width: 4cm;
        overflow: hidden;
    }

    .item-code {
        font-weight: bold;
        font-size: 0.4cm;
        margin: 0;
        line-height: 1.1;
    }

    .item-name {
        font-size: 0.35cm;
        margin: 0;
        line-height: 1.1;
    }

    .item-date {
        font-size: 0.35cm;
        margin: 0;
        line-height: 1.1;
    }

    .row-wrapper {
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
        width: 70%;
        height: 2.2cm; /* atau sesuaikan */å
        page-break-inside: avoid;
    }

    @media print {
        @page {
                size: landscape; /* Jika printer thermal mendukung */
                margin: 10px 10px 0px 3px; /* Ubah margin atas dari 11px → 20px */
            }

            body {
                margin-top: 0; /* Tambahan jika perlu untuk dorong konten ke bawah */
            }
        .container, 
        .barcode-wrapper {
            page-break-inside: avoid;
        }

        .btn, 
            button {
            display: none !important;
        }
    }
</style>

<div class="text-center mt-3">
    <button onclick="history.back()" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back</button>
    <button id="print" onclick="printBarcodes()" class="btn btn-primary"><i class="fa fa-print"></i> Print Barcodes</button>
</div>

<div class="mt-2">
    <div class="container"> 
        <div class="row justify-content-start">
            @foreach($addedBarcodes as $barcode)
                @for ($i = 0; $i < $barcode->qty; $i++)
                    <div class="row-wrapper">
                        <div class="barcode-wrapper">
                            <!-- QR Code -->
                            <div class="barcode-image">
                                <img 
                                    src="data:image/png;base64,{{ DNS2D::getBarcodePNG($barcode->code, 'QRCODE', 5, 5) }}" 
                                    alt="barcode"
                                />
                            </div>
                            
                            <!-- Text -->
                            <div class="barcode-text">
                                <div class="item-code">{{ $barcode->code }}</div>
                                <div class="item-name">{{ $barcode->name }}</div>
                                <div class="item-date">{{ now()->format('d/m/Y') }}</div>
                            </div>
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



