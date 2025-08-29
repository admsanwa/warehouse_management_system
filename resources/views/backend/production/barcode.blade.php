@extends("backend.layouts.app")
@section("content")
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>Print Barcode</h1>
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
                                <h3 class="card-title">Search Barcode List</h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">Product Nomer</label>
                                            <input type="text" name="prod_no" id="prod_no" class="form-control" placeholder="Enter Product Nomer">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Product Desc</label>
                                            <input type="text" name="prod_desc" id="prod_desc" class="form-control" placeholder="Enter Product Description">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">IO</label>
                                            <input type="text" name="io" id="io" class="form-control" placeholder="Enter IO">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-success" style="margin-top: 30px;"><i class="fa fa-search"></i> Search</button>
                                            <a href="{{ url("admin/quality/barcode")}}" class="btn btn-warning" style="margin-top: 30px;"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include("_message")
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List Barcode</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Product Nomer</th>
                                                <th>Description</th>
                                                <th>IO</th>
                                                <th>Qty</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        @forelse ($getRecord as $barcode)
                                            <form method="get" action="{{ url('admin/production/add') }}" enctype="multipart/form-data">
                                                @csrf
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            {{ $barcode->prod_no }}
                                                            <input type="hidden" name="prod_no" value="{{ $barcode->prod_no }}">
                                                        </td>
                                                        <td>
                                                            {{ $barcode->prod_desc }}
                                                            <input type="hidden" name="prod_desc" value="{{ $barcode->prod_desc }}">
                                                        </td>
                                                        <td>{{ $barcode->io_no }}</td>
                                                        <td>
                                                            <input type="number" name="qty" style="width: 70px;" class="form-control" required>
                                                        </td>
                                                        <td>
                                                            <button type="submit" class="btn btn-primary btn-sm">
                                                                <i class="fa fa-plus-circle"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </form>
                                        @empty
                                            <tr>
                                                <td colspan="100%">No Record Found</td>
                                            </tr>
                                        @endforelse
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-end px-2 py-2">
                                    <div class="overflow-x:auto max-width:100px">
                                         {!! $getRecord->onEachSide(1)->appends(request()->except('page'))->links() !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Recently Added</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        @if($addedBarcodes->count())
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Item Code</th>
                                                    <th>Item Desc</th>
                                                    {{-- <th>Barcode</th> --}}
                                                    <th>Qty</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($addedBarcodes as $barcode)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $barcode->prod_no }}</td>
                                                        <td>{{ $barcode->prod_desc }}</td>
                                                        {{-- <td>{!! DNS1D::getBarcodeHTML($barcode->code, 'C128', 1.0, 30) !!}</td> --}}
                                                        <td>{{ $barcode->qty }}</td>
                                                        <td><a href="{{ url('admin/production/delete/'. $barcode->id)}}" onclick="return confirm('Are you sure you want to delete?')" 
                                                            class="btn btn-danger btn-sm"><i class="fa fa-minus"></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>    
                                            <tbody>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>
                                                            <div class="d-flex flex-wrap">
                                                                <a href="{{ url('admin/production/deleteall') }}" class="btn btn-danger mr-2 mb-2"><i class="fa fa-trash"></i> All</a>
                                                                <a href="{{ url('admin/production/print') }}" class="btn btn-success mb-2">
                                                                    <i class="fa fa-arrow-right"></i> Print 
                                                                </a>
                                                                <a href="{{ url('admin/production/printppic') }}" class="btn btn-success mb-2">PPIC Format</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                            </tbody>   
                                        @endif
                                    </table>
                        </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection