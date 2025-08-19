@extends('backend.layouts.app')

@section('content')
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
    <button id="print" onclick="printBarcodes()" class="btn btn-primary"><i class="fa fa-print"></i> Print Barcodes</button>
</div>

<div class="mt-2">
    <div class="container"> 
        <div class="row justify-content-start">
            @foreach($addedBarcodes as $barcode)
                @for ($i = 0; $i < $barcode->qty; $i++)
                    <div class="barcode-wrapper d-flex align-items-center mb-4">
            
                        <div class="barcode-image">
                            <img 
                                src="data:image/png;base64,{{ DNS2D::getBarcodePNG($barcode->code, 'QRCODE', 5, 5) }}" 
                                alt="barcode"
                                style="height: 100px; width: 100px;"
                            />
                        </div>
                        <div class="barcode-text me-4 text-end">
                            <div class="item-code">{{ $barcode->code }}</div>
                            <div class="item-name">{{ $barcode->name }}</div>
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


