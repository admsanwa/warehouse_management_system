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
                                    <label for="" class="col-sm-3 col-form-lable">Nomer IO :</label>
                                    <div class="col-sm-3">{{ $getRecord->io }}</div>
                                    <label for="" class="col-sm-3 col-form-lable">Purchase Order :</label>
                                    <div class="col-sm-3">{{ $getRecord->po }}</div>
                                    <label for="" class="col-sm-3 col-form-lable">Internal No :</label>
                                    <div class="col-sm-3">{{ $getRecord->internal_no }}</div>
                                    <label for="" class="col-sm-3 col-form-lable">Scanned By :</label>
                                    <div class="col-sm-3">{{ $getRecord->user->fullname }}</div>
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