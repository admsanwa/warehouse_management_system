@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <section class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Good Issue Details</h1>
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
                                               @foreach ($getData as $goodissue)
                                               <tr>
                                                   <td>{{ $loop->iteration }}</td>
                                                   <td>{{ $goodissue->code }}</td>
                                                   <td>{{ $goodissue->name }}</td>
                                                   <td>{{ $goodissue->in_stock}}</td>
                                                   <td>{{ $goodissue->qty }}</td>
                                                   <td>{{ $goodissue->in_stock -  $goodissue->qty }}</td>
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
                                <a href="{{ url('admin/transaction/goodissued')}}" class="btn btn-primary">Next</a>
                                <a href="{{ url('admin/transaction/gidelete', $goodissue->gi) }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </section>
    </div>
@endsection