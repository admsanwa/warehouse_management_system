@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>Production List Details</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/purchasing') }}" class="btn btn-primary btn-sm">Production List</a>
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
                                <h3 class="card-title">View Production Order</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Status</label>
                                    <div class="col-sm-4">: {{ $getRecord['Status'] }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">IO No</label>
                                    <div class="col-sm-4">: {{ $getRecord['U_MEB_NO_IO'] }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Doc Number</label>
                                    <div class="col-sm-4">: {{ $getRecord['DocNum'] }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Product Type</label>
                                    <div class="col-sm-4">: {{ $getRecord['U_MEB_PROD_TYPE'] }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Product No</label>
                                    <div class="col-sm-4">: {{ $getRecord['ItemCode'] }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Project Code</label>
                                    <div class="col-sm-4">: {{ $getRecord['U_MEB_Project_Code'] }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Product Desc</label>
                                    <div class="col-sm-4">: {{ $getRecord['ItemName'] }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Contract</label>
                                    <div class="col-sm-4">: {{ $getRecord['U_MEB_Contract'] }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Sales Order</label>
                                    <div class="col-sm-4">: {{ $getRecord['OriginNum'] }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Contract Adendum</label>
                                    <div class="col-sm-4">: {{ $getRecord['U_MEB_ProjectDetail'] }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Distr. Rule</label>
                                    <div class="col-sm-4">: {{ $getRecord['OcrCode'] }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">No Internal</label>
                                    <div class="col-sm-4">: {{ $getRecord['U_MEB_Internal_Prod'] }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Due Date</label>
                                    <div class="col-sm-4">: {{ $getRecord['DueDate'] }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Remarks</label>
                                    <div class="col-sm-10">: {{ $getRecord['Comments'] }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="table table-responsive">
                                    <table class="table table-stripped">
                                        @if (count($lines) > 0)
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Item Code</th>
                                                    <th>Item Type</th>
                                                    <th>Qty</th>
                                                    <th>Uom</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($lines as $line)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $line['ItemCode'] ?? '-' }}</td>
                                                        <td>{{ $line['ItemName'] ?? '-' }}</td>
                                                        <td>{{ $line['PlannedQty'] }}</td>
                                                        <td>{{ $line['InvntryUoM'] ?? '-' }}</td>
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
                                @if ($getRecord['Status'] == 'Released')
                                    <a href="{{ url('admin/transaction/stockout', $getRecord['DocNum']) }}"
                                        class="btn btn-success"><i class="fa fa-arrow-right"></i> Released</a>
                                @endif
                                <button onclick="history.back()" class="btn btn-default">Back</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
<div>
    <!-- Breathing in, I calm body and mind. Breathing out, I smile. - Thich Nhat Hanh -->
</div>
