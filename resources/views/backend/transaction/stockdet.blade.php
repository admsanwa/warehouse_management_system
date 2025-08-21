@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <section class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Item Stock In Details</h1>
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
                                    <label for="" class="col-sm-2 col-form-lable">Nomer PO</label>
                                    <div class="col-sm-2">: {{ $getRecord->no_po ?? "-" }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Good Receipt PO</label>
                                    <div class="col-sm-2">: {{ $getRecord->grpo ?? "-" }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Scanned By</label>
                                    <div class="col-sm-2">: {{ $getRecord->user->fullname ?? "-" }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Vendor Code</label>
                                    <div class="col-sm-2">: {{ $getRecord->grpoData->vendor_code ?? "-" }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Vendor</label>
                                    <div class="col-sm-2">: {{ $getRecord->grpoData->vendor ?? "-" }}</div>  
                                    <label for="" class="col-sm-2 col-form-lable">Vendor Ref No</label>
                                    <div class="col-sm-2">: {{ $getRecord->grpoData->vendor_ref_no ?? "-" }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">No IO</label>
                                    <div class="col-sm-2">: {{ $getRecord->grpoData->io ?? "-" }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">No SO</label>
                                    <div class="col-sm-2">: {{ $getRecord->grpoData->so ?? "-" }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Internal No</label>
                                    <div class="col-sm-2">: {{ $getRecord->grpoData->internal_no ?? "-" }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Warehouse</label>
                                    <div class="col-sm-2">: {{ $getRecord->grpoData->whse ?? "-" }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Posting Date</label>
                                    <div class="col-sm-2">: {{ \Carbon\Carbon::parse($getRecord->created_at)->format('Y-m-d')}}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Remarks</label>
                                    <div class="col-sm-4">: {{ $getRecord->grpoData->remarks ?? "-" }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Note</label>
                                    <div class="col-sm-4">: {{ $getRecord->grpoData->note ?? "-" }}</div>

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
                                                    <th>Uom</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               @foreach ($getData as $stockin)
                                               <tr>
                                                   <td>{{ $loop->iteration }}</td>
                                                   <td>{{ $stockin->item->code }}</td>
                                                   <td>{{ $stockin->item->name }}</td>
                                                   <td>{{ $stockin->stock }}</td>
                                                   <td>{{ $stockin->qty }}</td>
                                                   <td>{{ $stockin->stock + $stockin->stock_in }}</td>
                                                   <td>{{ $stockin->item->uom }}</td>
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
                                <a href="{{ url('admin/transaction/stockin')}}" class="btn btn-primary">Next</a>
                                <a href="{{ url('admin/transaction/stockdel', $stockin->grpo) }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </section>
    </div>
@endsection