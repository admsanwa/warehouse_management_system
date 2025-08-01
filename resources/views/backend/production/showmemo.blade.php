@extends('backend.layouts.app')

@section('content')
<style>
   body {
        font-family: Arial, sans-serif;
        color: #000;
        margin: 0;
        padding: 0;
    }

    .memo-container {
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto;
        padding: 20mm;
        background: #fff;
        box-shadow: 0 0 5px 5px rgba(0,0,0,0.1);
        border: 1px solid #ddd;
    }

    @media print {
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            margin: 0;
        }

        #no-print {
            display: none;
        }

        .memo-container {
            margin: 0;
            width: 95%;
            padding: 10mm 10mm; /* inner padding (print safe zone) */
            box-sizing: border-box;
        }

        footer,
        #main-footer,
        .footer {
            display: none !important;
        }
    }

    .memo-header {
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .memo-header img {
        max-height: 60px;
    }

    .company-address {
        font-size: 12px;
        color: #555;
    }

    h4.memo-title {
        text-align: center;
        text-decoration: underline;
        font-weight: bold;
        margin: 20px 0;
    }

    .table-borderless td {
        padding: 5px 10px;
    }

    .details-table th {
        background-color: #f2f2f2;
        text-align: center;
    }

    .details-table td {
        vertical-align: top;
    }

    .signature-block {
        margin-top: 50px;
    }

    .signature-name {
        margin-top: 80px;
        font-weight: bold;
        text-decoration: underline;
    }

    .btn-print {
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .signature-name {
        font-size: 14px;
        margin-top: 4px;
    }

    .signature-dept {
        font-size: 13px;
        color: #6c757d;
    }

    .img-sign img {
        border-bottom: 1px solid #ccc;
        padding-bottom: 4px;
    }

</style>

<div class="memo-container">
    <div class="memo-header d-flex justify-content-between">
        <div class="text-left">
            <img src="{{ asset('assets/images/logo/logo-sanwamas.png') }}" alt="Logo-Sanwa">
            <p class="company-address mb-0">Jl. Raya Bekasi KM. 27, K.A Bungur Pondok Ungu, Bekasi</p>
            <p class="company-address mb-0">Phone: (021) 888 0338 &nbsp; Fax: (021) 888 0340</p>
        </div>
        <div class="text-right text-end">
            <h6 class="font-weight-bold mb-0">NO. {{ $memo->no }}</h6>
            <input type="hidden" id="no_memo" value="{{ $memo->no }}">
        </div>
    </div>

    <h4 class="memo-title">MEMO</h4>

    <table class="table table-borderless">
        <tr>
            <td width="30%">Kepada Yth</td>
            <td>: PRODUCTION / QC / GUDANG / PURCHASING</td>
        </tr>
        <tr>
            <td>Dari</td>
            <td>: PPIC</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>: {{ \Carbon\Carbon::parse($memo->date)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Hal</td>
            <td>: {{ $memo->description }}</td>
        </tr>
        <tr>
            <td>Proyek / Order</td>
            <td>: {{ $memo->project }}</td>
        </tr>
        <tr>
            <td>IO</td>
            <td>: {{ $memo->io }}</td>
        </tr>
        <tr>
            <td>Due Date</td>
            <td>: {{ \Carbon\Carbon::parse($memo->due_date)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <p>Dengan hormat,</p>
    <p>Mohon untuk dapat diproses <strong>{{ $memo->description }}</strong> untuk proyek <strong>{{ $memo->project }}</strong> dengan kriteria sebagai berikut :</p>

    <table class="table table-bordered details-table">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Kebutuhan</th>
                <th style="width: 100px;">Qty</th>
                <th style="width: 100px;">Uom</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($memo->details as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $detail->unit . ($detail->width ? (" Width " . $detail->width) : "") . ($detail->height ? (" Height " . $detail->height) : "") }}</td>
                    <td class="text-center">{{ $detail->qty ?? "-" }}</td>
                    <td class="text-center">{{ $detail->uom }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Demikian hal ini kami sampaikan, atas perhatiannya kami ucapkan terima kasih.</p>

    <div class="signature-block row mt-5">
        <div class="col md-4 text-center">
            <div class="fw-bold mb-1">Hormat Kami,</div>
            <div class="img-sign mb-2"><img src="{{ asset('assets/images/sign/delvi.png')}}" width="100" alt="sign"></div>
            <div class="signature-name fw-semibold">( DELVI WINOSRI )</div>
            <div class="signature-dept text-muted small">Procurement, Installation and Delivery</div>
        </div>
        <div class="col md-4 text-center col-sign" style="display: {{ $getSign === 1 ? 'block' : 'none'}}">
            <div class="fw-bold mb-1">Mengetahui,</div>
            <div class="img-sign mb-2"><img src="{{ asset('assets/images/sign/benny.png')}}" width="100" alt="sign"></div>
            <div class="signature-name fw-semibold">( BENNY THIOWIJAYA )</div>
            <div class="signature-dept text-muted small">West Production and Warehouse Team</div>
        </div>
        <div class="col md-4 text-center col-backup" style="display: {{ $getSign === 1 ? 'none' : 'block'}}">
        </div>
    </div>

    <div id="no-print">
        <button class="btn btn-primary btn-print" onclick="window.print()">
            <i class="fa fa-print"></i> Print Memo
        </button>
        <button class="btn btn-success" onclick="approve()" style="display: {{ $user === '05993' ? 'block' : 'none'}}">
            <i class="fa fa-check-circle">Approve</i>
        </button>
    </div>
</div>
<script>
    function approve() {
        const no_memo = document.getElementById("no_memo").value;
        if(!confirm("Are you sure ask approve to production?")) return;

        fetch("/approve-memo", {
            method: "POST",
            headers: {
                "Content-Type" : "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ no_memo: no_memo })
        })
        .then(res => res.json())
        .then(data => {
            console.log(data);
            alert(data.message);
        })
        .catch(error => {
            console.error("Fetch Error: ", error);
            alert("An error occurred. Check console for details.");
        });
    }
</script>
@endsection
