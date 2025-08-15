@extends('backend.layouts.app')

@section('content')

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
