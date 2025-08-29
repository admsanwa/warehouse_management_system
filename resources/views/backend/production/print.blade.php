@extends("backend.layouts.app")
@section("content")
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    .barcode-wrapper {
        width: 13cm;
        height: 5cm;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        padding-left: 0.2cm;
        padding-top: 0.9cm;
        overflow: hidden;
    }

    .barcode-image img {
        width: 3.5cm;
        height: 3.5cm;
        display: block;
    }

    .barcode-text {
        margin-left: 0.3cm;
        overflow: hidden;
        text-align: center;
        width:auto;
    }

    .item-code {
        font-weight: bold;
        font-size: 0.70cm;
        text-decoration: underline;
        margin: 0;
        line-height: 1.5;
    }

    .item-name {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-word;
        text-align: center;
        font-size: 0.60cm;
        line-height: 1.1;
    }

    .item-date {
        font-size: 13pt;
        text-decoration: underline;
        margin-top: 3px;
    }

    .item-po {
        margin-top: -5px !important;
        font-size: 13pt;
        margin: 2;
    }

    .row-wrapper {
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
        width: 70%;
        height: 4.4cm; /* atau sesuaikan */å
        page-break-inside: avoid;
    }

    @media print {
        @page {
                size: landscape; /* Jika printer thermal mendukung */
                margin: 0px !important; /* Ubah margin atas dari 11px → 20px */
            }

            body {
                margin: 0 !important; /* Tambahan jika perlu untuk dorong konten ke bawah */
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

<div class="mt-2">
    <div class="container">
        <div class="row">
            @foreach ($addedBarcodes as $barcodes)
                @for ($i = 0; $i < $barcodes->qty; $i++)
                    <div class="row-wrapper">
                        <div class="barcode-wrapper">
                            <div class="barcode-image">
                                <img src="data:image/png;base64, {{ DNS2D::getBarcodePNG($barcodes->prod_no, 'QRCODE', 5, 5) }}" alt="barcode">
                            </div>
                            <div class="barcode-text">
                                <div class="item-code">{{ $barcodes->prod_no }}</div>
                                <div class="item-name">{{ $barcodes->prod_desc }}</div>
                                <div class="item-date ">{{ \Carbon\Carbon::parse($barcodes->po->updated_at)->format('d/m/Y') }}</div>
                                <div class="item-po ">{{ $barcodes->po->doc_num }}</div>
                            </div>
                        </div>
                    </div>
                @endfor
            @endforeach
        </div>
    </div>
</div>

<div class="text-center mt-3">
    <button onclick="history.back()" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back</button>
    <button onclick="printBarcodes()" class="btn btn-primary"><i class="fa fa-print"></i> Print Barcodes</button>
</div><br>

<script>
    function printBarcodes() {
        window.print();
    }
</script>
@endsection
