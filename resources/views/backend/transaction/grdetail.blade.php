@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <section class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Good Receipt Details</h1>
                    </div>
                </section>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
               <div class="row">
                    <div class="col col-md-12">
                        <div class="card card-info">
                            @include('_message')
                            <div class="card-header">
                                <h3 class="card-title">
                                    Confirmation
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Number</label>
                                    <div class="col-sm-2">: {{ $getRecord->gr }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Warehouse</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->whse }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Project Code</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->project_code }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Vendor Code</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->vendor_code }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Distr. Rule</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->distr_rule }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Internal No</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->internal_no }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Posting Date</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->updated_at }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">No PO</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->po }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">IO</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->io }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">SO</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->so }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">No Inv Transfer</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->no_inventory_tf }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Type Inv Transaction</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->type_inv_transaction }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Surat Jalan</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->no_surat_jalan }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">SJ Barang Datang</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->ref_surat_jalan }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Refer No Good Issue</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->no_gi }}</div>
                                </div>
                                  <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Account Code</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->acct_code }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Reason</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->reason }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Remarks</label>
                                    <div class="col-sm-2">: {{ $getRecord->good_receipt->remarks }}</div>
                                </div>
                            </div>
                        </div>        

                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        @if ($getData->count())
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Item Code</th>
                                                    <th>Item Description</th>
                                                    <th>In Stock</th>
                                                    <th>Qty</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               @foreach ($getData as $goodreceipt)
                                               <tr>
                                                   <td>{{ $loop->iteration }}</td>
                                                   <td>{{ $goodreceipt->code }}</td>
                                                   <td>{{ $goodreceipt->name }}</td>
                                                   <td>{{ $goodreceipt->in_stock - $goodreceipt->qty}}</td>
                                                   <td>{{ $goodreceipt->qty }}</td>
                                                   <td>{{ $goodreceipt->in_stock }}</td>
                                                </tr>
                                               @endforeach
                                            </tbody>
                                        @else
                                            <tbody>
                                                <tr>
                                                    <td colspan="100%">No Record Found</td>
                                                </tr>
                                            </tbody>
                                        @endif
                                    </table>
                                </div>
                            </div>   
                            <div class="card-footer">
                                <a href="{{ url('admin/transaction/goodreceipt')}}" class="btn btn-primary">Next</a>
                                <a href="{{ url('admin/transaction/grdelete', $goodreceipt->gr) }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </section>
    </div>
@endsection