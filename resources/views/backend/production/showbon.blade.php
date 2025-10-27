@extends('backend.layouts.app')

@section('content')
    <style>
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
                        <input type="hidden" id="no_bon" value="{{ $bon->no }}">
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
            @if ($signApprove)
                <div class="col md-4 text-center col-sign"
                    style="display: {{ $signApprove->sign === 1 ? 'block' : 'none' }}">
                    <div class="fw-bold mb-1">Disetujui Oleh,</div>
                    <div class="img-sign mb-2"><img src="{{ asset('assets/images/sign/' . $signApprove->user->sign) }}"
                            width="100" height="65" alt="sign"></div>
                    <div class="signature-name fw-semibold">{{ $signApprove->user->fullname }}</div>
                    <div class="signature-dept text-muted small">{{ $signApprove->user->department }}</div>
                </div>
            @endif
            @if ($signBuyer)
                <div class="col md-4 text-center col-sign"
                    style="display: {{ $signBuyer->sign === 1 ? 'block' : 'none' }}">
                    <div class="fw-bold mb-1">Bagian Pembeli,</div>
                    <div class="img-sign mb-2"><img src="{{ asset('assets/images/sign/' . $signBuyer->user->sign) }}"
                            width="100" height="65" alt="sign"></div>
                    <div class="signature-name fw-semibold">{{ $signBuyer->user->fullname }}</div>
                    <div class="signature-dept text-muted small">{{ $signBuyer->user->department }}</div>
                </div>
            @endif
            <div class="col md-4 text-center col-sign">
                <div class="fw-bold mb-1">Pemesan,</div>
                <div class="img-sign mb-2"><img src="{{ asset('assets/images/sign/' . $bon->createdBy->sign) }}"
                        width="100" height="65" alt="sign"></div>
                <div class="signature-name fw-semibold">{{ $bon->createdBy->fullname }}</div>
                <div class="signature-dept text-muted small">{{ $bon->createdBy->department }}</div>
            </div>
        </div>
        <div id="no-print">
            <button class="btn btn-primary btn-print" onclick="window.print()">
                <i class="fa fa-print"></i> Print
            </button>
            <button class="btn btn-success" onclick="approve()"
                style="display: {{ $user->nik === '250071' || $user->nik === '08517' || $user->nik === '06067' ? 'block' : 'none' }}">
                <i class="fa fa-check-circle"> Approve</i>
            </button>
        </div>
    </div>
    <script>
        function approve() {
            const no_bon = document.getElementById("no_bon").value;
            // console.log("no", no_bon);
            if (!confirm("Are you sure approve to bon this?")) return;

            fetch("/approve-bon", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        no_bon: no_bon
                    })
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
