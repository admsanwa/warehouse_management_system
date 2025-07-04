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
                                    <label for="" class="col-sm-3 col-form-lable">Number :</label>
                                    <div class="col-sm-3">{{ $getRecord->number}}</div>
                                    <label for="" class="col-sm-3 col-form-lable">Nomer IO :</label>
                                    <div class="col-sm-3">{{ $getRecord->io }}</div>
                                    <label for="" class="col-sm-3 col-form-lable">Production Order :</label>
                                    <div class="col-sm-3">{{ $getRecord->prod_order }}</div>
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
                                                    <th>Product Nomer</th>
                                                    <th>Product Description</th>
                                                    <th>Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               @foreach ($getData as $rfp)
                                               <tr>
                                                   <td>{{ $loop->iteration }}</td>
                                                   <td>{{ $rfp->production->prod_no }}</td>
                                                   <td>{{ $rfp->production->prod_desc }}</td>
                                                   <td>{{ $rfp->qty }}</td>
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