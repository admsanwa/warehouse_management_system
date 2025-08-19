@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>Items</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{url('admin/items/barcode')}}" class="btn btn-primary btn-sm">Barcode Print</a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <section class="col col-md-6">
                        <div class="card">
                            @include('_message')
                            <div class="card-header">
                                <h3 class="card-title">Add Items</h3>
                            </div>
                            <form action="{{ url('admin/items/additem')}}" class="form-horizontal" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-lable">System Date</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" name="system_date" value="{{ \Carbon\Carbon::now()->format('d/m/Y')}}" readonly>
                                        </div>
                                        {{-- <label class="col-sm-3 col-form-lable">System Date</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" name="system_date" value="">
                                        </div> --}}
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-lable">Posting Date <span style="color: red">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="date" class="form-control" name="posting_date">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-lable">Item Code <span style="color: red">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" name="code" placeholder="Enter Item Code" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-lable">Item Desc <span style="color: red">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" name="name" placeholder="Enter Item Desc" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-lable">Item Group</label>
                                        <div class="col-sm-6">
                                            <select class="form-control" name="group">
                                                <option value="">Select Item Group</option>
                                                <option value="1">Raw Meterial</option>
                                                <option value="2">Part Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-lable">Uom</label>
                                        <div class="col-sm-6">
                                            <select class="form-control" name="uom">
                                                <option value="">Select Unit of Material</option>
                                                <option value="1">Pcs</option>
                                                <option value="2">Unit</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-lable">Stock Min</label>
                                        <div class="col-sm-6">
                                            <input type="number" class="form-control" name="stock_min" placeholder="Enter Stock Min">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-lable">Stock Max</label>
                                        <div class="col-sm-6">
                                            <input type="number" class="form-control" name="stock_max" placeholder="Enter Stock Max">
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="card-footer">
                                    <div class="col-sm-9">
                                        <button class="btn btn-primary float-right">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </section>

                    <section class="col-12 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Upload Data Items (<span class="text-danger"> .csv</span>)
                                </h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ url('admin/items/upload')}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="csvFile">Choose CSV File</label>
                                        <input class="form-control" type="file" name="file" id="csvFile" required>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary w-100 w-md-auto" type="submit">
                                            <i class="fa fa-upload"></i> Upload
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>
@endsection