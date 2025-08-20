@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Stock</h1>
                    </div>
                </div>
            </div>
        </div>
        
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <section class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Search Data Stock
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">Item Code</label>
                                            <input type="text" name="item_code" class="form-control" value="{{ Request()->item_code }}" placeholder="Enter Item Code">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Item</label>
                                            <input type="text" name="item" class="form-control" value="{{ Request()->item}}" placeholder="Enter Item Name">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Item Description</label>
                                            <input type="text" name="item_desc" class="form-control" value="{{ Request()->item_desc }}" placeholder="Enter Item Desc">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 30px"><i class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/stock') }}" class="btn btn-warning" style="margin-top: 30px"><i class="fa fa-eraser"></i>Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    List of All Stock
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-stripped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Item Code</th>
                                                <th>Item Desc</th>
                                                <th>Stock SAP</th>
                                                <th>Stock In</th>
                                                <th>Stock Out</th>
                                                <th>Available</th>
                                                <th>Uom</th>
                                            </tr>
                                        </thead>

                                       @forelse ($stocks as $stock )                                           
                                           <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $stock->item->code ?? 'N/A'}}</td>
                                            <td>{{ $stock->item->name ?? 'N/A'}}</td>
                                            <td>{{ $stock->stock }}</td>
                                            <td>{{ $stock->stock_in }}</td>
                                            <td>{{ $stock->stock_out }}</td>
                                            <td>{{ ($stock->stock + $stock->stock_in) - $stock->stock_out }}</td>
                                            <td>{{ $stock->item->uom }}</td>
                                           </tr>
                                       @empty
                                           <tr>
                                            <td colspan="100%">No Record Found</td>
                                           </tr>
                                       @endforelse
                                    </table>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-end px-2 py-2">
                                        <div class="overflow-x: auto; max-width: 100%">
                                            {!! $stocks->onEachSide(1)->appends(request()->except('page'))->links() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>
@endsection