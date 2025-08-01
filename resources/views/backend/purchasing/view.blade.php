@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>Purchasing List Details</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/purchasing') }}" class="btn btn-primary btn-sm">Purchasing List</a>
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
                                <h3 class="card-title">View Purchase Order</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Nomor PO</label>
                                    <div class="col-sm-2">: {{$getRecord->no_po }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Buyer</label>
                                    <div class="col-sm-2">: {{ $getRecord->buyer }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">IO</label>
                                    <div class="col-sm-2">: {{ $getRecord->io ?? "-" }}</div>

                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Vendor</label>
                                    <div class="col-sm-2">: {{ $getRecord->vendor }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Delivery Date</label>
                                    <div class="col-sm-2">: {{ $getRecord->delivery_date }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Internal No</label>
                                    <div class="col-sm-2">: {{ $getRecord->internal_no ?? "-"}}</div>

                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Contact Person</label>
                                    <div class="col-sm-2">: {{ $getRecord->contact_person }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Status</label>
                                    <div class="col-sm-2">: {{ $getRecord->status == "Open" ? 'Open' : 'Closed' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="table table-responsive">
                                    <table class="table table-stripped">
                                        @if ($getData->count())
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Item Code</th>
                                                    <th>Item Type</th>
                                                    <th>Item Desc</th>
                                                    <th>Qty</th>
                                                    <th>Uom</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($getData as $pos )
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $pos->item_code }}</td>
                                                    <td>{{ $pos->item_type }}</td>
                                                    <td>{{ $pos->item_desc }}</td>
                                                    <td>{{ $pos->qty }}</td>
                                                    <td>{{ $pos->uom }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        @else
                                            <tbody>
                                                <tr>
                                                    <td colspan="100%">Tidak ada data yang ditemukan</td>
                                                </tr>
                                            </tbody>
                                        @endif
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="{{ url('admin/purchasing') }}" class="btn btn-default">Back</a>
                                @if (stripos($pos->item_code, "Maklon") !== false)
                                    <a href="{{ url("admin/transaction/goodissued")}}" class="btn btn-success">Good Issue</a>
                                @else
                                    <a href="{{ url("admin/transaction/stockin/" . $getPO)}}" class="btn btn-success">Scan Barcode</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection