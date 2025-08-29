@extends('backend.layouts.app')

@section('content')
<style>
    /* 90 width 38 */
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    .print-area {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* 2 barcode per baris */
        grid-auto-rows: auto;
        gap: 1cm 0cm; /* jarak antar kolom & baris */
        justify-items: center; /* isi kolom rata tengah */
        align-items: center;   /* isi baris rata tengah */
        padding: 1cm 2cm;  /* atas-bawah 0.5cm, kiri-kanan 1cm */
    }

    .barcode-wrapper {
        width: 100%;   /* sesuaikan dengan ukuran label */
        height: auto;   /* tinggi label */
        display: flex;
        align-items: center;
        justify-content: center; /* isi di tengah wrapper */
        text-align: left;
        padding: 0.3cm 0.8cm;  /* atas-bawah 0.5cm, kiri-kanan 1cm */

    }

    .barcode-image img {
        width: 3.1cm;
        height: 3.1cm;
        display: block;
        margin-right: 0.4cm;
    }

    .barcode-text {
        width: 70%;
        text-align: center;
    }

    .item-code {
        font-weight: bold;
        font-size: 0.60cm;
        text-decoration: underline;
        margin: 0;
        line-height: 1.5;
    }

    .item-name {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-word;
        text-align: center;
        font-size: 0.50cm;
        line-height: 1.1;
        width: 
    }


    .item-date {
        font-size: 12pt;
        margin: 5px 0;
    }

    @media print {
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .btn, 
            button {
            display: none !important;
        }
    }
</style>

<div class="print-area">
    @foreach($addedBarcodes as $barcode)
        @for ($i = 0; $i < $barcode->qty; $i++)
            <div class="barcode-wrapper">
                <div class="barcode-image">
                    <img 
                        src="data:image/png;base64,{{ DNS2D::getBarcodePNG($barcode->code, 'QRCODE', 5, 5) }}" 
                        alt="barcode"
                    />
                </div>
                <div class="barcode-text">
                    <div class="item-code">{{ $barcode->code }}</div>
                    <div class="item-name">{{ $barcode->name }}</div>
                </div>
            </div>
        @endfor
    @endforeach
</div>



<div class="text-center mt-3">
    <button onclick="history.back()" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back</button>
    <button id="print" onclick="printBarcodes()" class="btn btn-primary"><i class="fa fa-print"></i> Print Barcodes</button>
</div><br>

<script>
    function printBarcodes() {
        window.print();
    }
</script>
@endsection



