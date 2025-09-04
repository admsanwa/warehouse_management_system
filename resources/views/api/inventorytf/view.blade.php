@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>Inventory Transfer Details</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/inventorytf/create') }}" class="btn btn-primary btn-sm">Create Inventory
                                TF</a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col col-md-12">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">View Inventory Transfer</h3>
                            </div>
                            <div class="card-body">
                                {{-- ✅ Bagian field yang ada datanya --}}
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Status</label>
                                    <div class="col-sm-4">: {{ $invtf['DocStatus'] === 'O' ? 'Open' : 'Close' }}</div>

                                    <label class="col-sm-2 col-form-label">Doc Number / No Inventory Transfer</label>
                                    <div class="col-sm-4">: {{ $invtf['DocNum'] }}</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Posting Date</label>
                                    <div class="col-sm-4">: {{ $invtf['DocDate'] }}</div>

                                    <label class="col-sm-2 col-form-label">Series</label>
                                    <div class="col-sm-4">
                                        :
                                        @if (!empty($series) && isset($series['ObjectCode'], $series['SeriesName']))
                                            {{ $series['SeriesName'] }}
                                        @else
                                            <span class="text-danger">⚠️ Series tidak ditemukan:
                                                {{ $po['Series'] ?? '' }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">From Warehouse</label>
                                    <div class="col-sm-4">: {{ $invtf['FromWhsCode'] }}</div>

                                    <label class="col-sm-2 col-form-label">To Warehouse</label>
                                    <div class="col-sm-4">: {{ $invtf['ToWhsCode'] }}</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Remarks</label>
                                    <div class="col-sm-10">: {{ $invtf['Comments'] }}</div>
                                </div>

                                <hr>

                                {{-- ❌ Bagian field yang kosong --}}
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Vendor Maklon List</label>
                                    <div class="col-sm-4">: Tidak Ada</div>

                                    <label class="col-sm-2 col-form-label">PO Maklon</label>
                                    <div class="col-sm-4">: Tidak Ada</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Bussiness Partner</label>
                                    <div class="col-sm-4">: Tidak Ada</div>

                                    <label class="col-sm-2 col-form-label">Default Warehouse</label>
                                    <div class="col-sm-4">: Tidak Ada</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Vendor</label>
                                    <div class="col-sm-4">: Tidak Ada</div>

                                    <label class="col-sm-2 col-form-label">Default Project Code</label>
                                    <div class="col-sm-4">: Tidak Ada</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Type Inventory Transaction</label>
                                    <div class="col-sm-4">: Tidak Ada</div>

                                    <label class="col-sm-2 col-form-label">SO</label>
                                    <div class="col-sm-4">: Tidak Ada</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">IO</label>
                                    <div class="col-sm-4">: Tidak Ada</div>

                                    <label class="col-sm-2 col-form-label">Internal No</label>
                                    <div class="col-sm-4">: Tidak Ada</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Sales Employee</label>
                                    <div class="col-sm-4">: Tidak Ada</div>

                                    <label class="col-sm-2 col-form-label">Default Distr Rule</label>
                                    <div class="col-sm-4">: Tidak Ada</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">No Surat Jalan</label>
                                    <div class="col-sm-4">: Tidak Ada</div>

                                    <label class="col-sm-2 col-form-label">Lokasi</label>
                                    <div class="col-sm-4">: Tidak Ada</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">No Produksi</label>
                                    <div class="col-sm-4">: Tidak Ada</div>

                                    <label class="col-sm-2 col-form-label">Ref 2 (No SJ barang datang)</label>
                                    <div class="col-sm-4">: Tidak Ada</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">No Production Order</label>
                                    <div class="col-sm-4">: Tidak Ada</div>

                                    <label class="col-sm-2 col-form-label">Refer No. Good Issue</label>
                                    <div class="col-sm-4">: Tidak Ada</div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Hari & Tanggal Kirim</label>
                                    <div class="col-sm-4">: Tidak Ada</div>

                                    <label class="col-sm-2 col-form-label">Komponen Tambahan</label>
                                    <div class="col-sm-4">: Tidak Ada</div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="table table-responsive">
                                    <table class="table table-stripped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Item Code</th>
                                                <th>Description</th>
                                                <th>Open Qty</th>
                                                <th>Qty</th>
                                                <th>Uom</th>
                                                <th>From Whse</th>
                                                <th>To Whse</th>
                                                <th>Acct Code</th>
                                                <th>Jumlah Kemasan</th>
                                                <th>Keterangan</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lines as $line)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $line['ItemCode'] ?? '-' }}</td>
                                                    <td>{{ $line['ItemName'] ?? '-' }}</td>
                                                    <td>{{ formatDecimalsSAP($line['OpenQty']) }}</td>
                                                    <td>{{ formatDecimalsSAP($line['Quantity']) }}</td>
                                                    <td>{{ $line['UomName'] ?? '-' }}</td>
                                                    <td>{{ $line['FromWhsCode'] }}</td>
                                                    <td>{{ $line['ToWhsCode'] }}</td>
                                                    <td>Tidak Ada</td>
                                                    <td>Tidak Ada</td>
                                                    <td>Tidak Ada</td>
                                                    <td>Tidak Ada</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button onclick="history.back()" class="btn btn-default">Back</button>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection
