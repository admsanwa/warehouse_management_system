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

    .box {
        display: inline-block;
        min-width: 180px;
        text-align: left;
        font-weight: bold;
        font-size: 14px;
    }
    
    .box div {
        display: flex;
        justify-content: space-between;
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
            <div class="box">
                <div class="d-flex justify-content-between">
                    <span>No :</span>
                    <span>{{ $bon->no }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Date :</span>
                    <span>{{ \Carbon\Carbon::parse($bon->date)->translatedFormat('d F Y') }}</span>
                </div>
            </div>
        </div>
    </div>


    <h4 class="memo-title">BON PEMBELIAN BARANG</h4>

    <table class="table table-borderless">
        <tr>
            <td>Bagian</td>
            <td>: {{ $bon->section }}</td>
        </tr>
        <tr>
            <td>IO</td>
            <td>: {{ $bon->io }}</td>
        </tr>
        <tr>
            <td>Project</td>
            <td>: {{ $bon->project }}</td>
        </tr>
        <tr>
            <td>Make to</td>
            <td>: {{ $bon->make_to }}</td>
        </tr>
    </table>

    <table class="table table-bordered details-table">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th style="width: 100px;">Qty</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bon->details as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $detail->item_code }}</td>
                    <td>{{ $detail->item_name }}</td>
                    <td>{{ $detail->qty . ' ' . $detail->uom }}</td>
                    <td class="text-center">{{ $detail->remark }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


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
         <div class="col md-4 text-center col-sign" style="display: {{ $getSign === 1 ? 'block' : 'none'}}">
            <div class="fw-bold mb-1">Pemesan,</div>
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
