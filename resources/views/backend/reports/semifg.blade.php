@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Semi Finish Goods</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Search List Semi Finish Goods</h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">IO</label>
                                            <input type="text" name="io" id="io" class="form-control" value="" placeholder="Enter IO">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Production Order</label>
                                            <input type="text" name="prod_order" id="prod_order" class="form-control" value="" placeholder="Enter Production Order">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Production Nomor</label>
                                            <input type="text" name="prod_no" id="prod_no" class="form-control" value="" placeholder="Enter Production Nomer">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Production Desc</label>
                                            <input type="text" name="prod_desc" id="prod_desc" class="form-control" value="" placeholder="Enter Product Description">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-success" style="margin-top:30px"><i class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/reports/finishgoods')}}" style="margin-top: 30px" class="btn btn-warning"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List Semi Finish Goods</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>No Receipt</th>
                                                <th>IO</th>
                                                <th>Production Order</th>
                                                <th>Production Nomor</th>
                                                <th>Production Description</th>
                                                <th>Qty</th>
                                            </tr>   
                                        </thead>
                                        <tbody>
                                             @forelse ($getRecord as $sfg)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $sfg->number}}</td>
                                                    <td>{{ $sfg->io}}</td>
                                                    <td>{{ $sfg->prod_order }}</td>
                                                    <td>{{ $sfg->prod_no}} </td>
                                                    <td>{{ $sfg->prod_desc }}</td>
                                                    <td>{{ $sfg->qty }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="100%">No Record Found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button onclick="history.back()" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection