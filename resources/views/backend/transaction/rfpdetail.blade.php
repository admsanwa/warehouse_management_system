@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <section class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Receipt From Production Details</h1>
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
                                    <label for="" class="col-sm-2 col-form-lable">No Prod Order</label>
                                    <div class="col-sm-2">: {{ $getRecord->prod_order }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Number</label>
                                    <div class="col-sm-2">: {{ $getRecord->number }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Scanned By</label>
                                    <div class="col-sm-2">: {{ $getRecord->user->fullname }}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">No IO</label>
                                    <div class="col-sm-2">: {{ $getRecord->io }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">No SO</label>
                                    <div class="col-sm-2">: {{ $getRecord->so }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Posting Date</label>
                                    <div class="col-sm-2">: {{ \Carbon\Carbon::parse($getRecord->created_at)->format('Y-m-d')}}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">Project Code</label>
                                    <div class="col-sm-2">: {{ $getRecord->project_code }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Warehouse</label>
                                    <div class="col-sm-2">: {{ $getRecord->whse }}</div>  
                                    <label for="" class="col-sm-2 col-form-lable">Reason</label>
                                    <div class="col-sm-2">: {{ $getRecord->reason}}</div>
                                </div>
                                <div class="form-group row">
                                    <label for="" class="col-sm-2 col-form-lable">No Series</label>
                                    <div class="col-sm-2">: {{ $getRecord->no_series }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Remarks</label>
                                    <div class="col-sm-6">: {{ $getRecord->remarks }}</div>
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
                                                    <th>Product Nomer</th>
                                                    <th>Product Description</th>
                                                    <th>Qty</th>
                                                    <th>Uom</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               @foreach ($getData as $rfp)
                                               <tr>
                                                   <td>{{ $loop->iteration }}</td>
                                                   <td>{{ $rfp->production->prod_no }}</td>
                                                   <td>{{ $rfp->production->prod_desc }}</td>
                                                   <td>{{ $rfp->qty }}</td>
                                                   <td>{{ $rfp->items->uom ?? "-" }}</td>
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
                                <a href="{{ url('admin/transaction/rfp')}}" class="btn btn-primary">Next</a>
                                <a href="{{ url('admin/transaction/rfpdelete', $rfp->number) }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </section>
    </div>
@endsection