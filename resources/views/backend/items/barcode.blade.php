@extends('backend.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col col-sm-6">
                    <h1>Barcode Print</h1>
                </div><!-- /.col -->
                <div class="col col-sm-6">
                    <ol class="breadcrumb justify-content-end">
                        <a href="{{ url('admin/items/additem')}}" class="btn btn-primary btn-sm">Add Item</a>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <section class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Search Barcode</h3>
                        </div>
                        <form action="" method="get">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label for="">Item Code</label>
                                        <input type="text" name="code" value="{{ Request()->code}}" class="form-control" placeholder="Enter Item Code">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="">Item Desc</label>
                                        <input type="text" name="name" value="{{ Request()->name}}" class="form-control" placeholder="Enter Item Name">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <button class="btn btn-primary" type="submit" style="margin-top: 30px"><i class="fa fa-search"></i> Search</button>
                                        <a href="{{ url('admin/items/barcode')}}" class="btn btn-warning" style="margin-top: 30px"><i class="fa fa-eraser"></i> Reset</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    @include('_message')
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
                                            <th>Item Code</th>
                                            <th>Item Desc</th>
                                            {{-- <th>Image</th> --}}
                                            <th>Qty</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    @forelse ($getRecord as $key => $value)
                                        <tbody>
                                            <tr>
                                                <td>{{ $loop->iteration}}</td>
                                                <td>{{ $value->code}}</td>
                                                <td>{{ $value->name}}</td>
                                                {{-- <td>{!! DNS1D::getBarcodeHTML($value->code, 'C128', 1.0, 30) !!}</td> --}}
                                                <td>
                                                    <form action="{{ url('admin/items/add') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="code" value="{{ $value->code }}">
                                                        <input type="hidden" name="name" value="{{ $value->name }}">
                                                        <input type="number" class="form-control form-control-sm w-100" name="qty" required>
                                                </td>
                                                <td>
                                                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-plus-circle"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="100%">No Record Found</td>
                                            </tr>
                                        </tbody>
                                    @endforelse
                                </table>
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-end px-2 py-2">
                                <div style="overflow-x: auto; max-width:100%">
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
                                                        <td>{{ $barcode->code }}</td>
                                                        <td>{{ $barcode->name }}</td>
                                                        {{-- <td>{!! DNS1D::getBarcodeHTML($barcode->code, 'C128', 1.0, 30) !!}</td> --}}
                                                        <td>{{ $barcode->qty }}</td>
                                                        <td><a href="{{ url('admin/items/delete/'. $barcode->id)}}" onclick="return confirm('Are you sure you want to delete?')" 
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
                                                                <a href="{{ url('admin/items/deleteall')}}" class="btn btn-danger mr-2 mb-2"><i class="fa fa-trash"></i> All</a>
                                                                <a href="{{ url('admin/items/print')}}" class="btn btn-success mb-2">
                                                                    <i class="fa fa-arrow-right"></i> Print
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                            </tbody>   
                                        @endif
                                    </table>
                            </div>
                        </div>
                </section>
            </div>
        </div>
    </section>
</div>
@endsection