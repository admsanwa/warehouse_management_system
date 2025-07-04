@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <section class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Item Stock Out Details</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <a href="{{ url("admin/transaction/stockout") }}" class="btn btn-primary">Stock Out</a>
                        </ol>
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
                                    <label for="" class="col-sm-2 col-form-lable">Nomer IO :</label>
                                    <div class="col-sm-2">{{ $getProd->io_no }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Production Order :</label>
                                    <div class="col-sm-2">{{ $getRecord->prod_order }}</div>
                                    <label for="" class="col-sm-2 col-form-lable">Scanned By :</label>
                                    <div class="col-sm-2">{{ $getRecord->user->fullname }}</div>
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
                                               @foreach ($getData as $stockout)
                                               <tr>
                                                   <td>{{ $loop->iteration }}</td>
                                                   <td>{{ $stockout->item->code }}</td>
                                                   <td>{{ $stockout->item->name }}</td>
                                                   <td>{{ $stockout->on_hand + $stockout->qty}}</td>
                                                   <td>{{ $stockout->qty }}</td>
                                                   <td>{{ $stockout->on_hand }}</td>
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
                                <a href="{{ url('admin/transaction/stockout')}}" class="btn btn-primary">Next</a>
                                <a href="{{ url('admin/transaction/stockoutdel', $stockout->isp) }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </section>
    </div>
@endsection