@extends("backend.layouts.app")
@section("content")
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Delivery</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url("admin/delivery/list") }}" class="btn btn-primary btn-sm">List Delivery</a>
                        </ol>
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
                                <h3 class="card-title"> 
                                    Search Tracker Delivery List
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">Product No</label>
                                            <input type="text" name="prod_no" id="prod_no" class="form-control" value="{{ Request()->prod_no }}" placeholder="Enter Product Nomer">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Product Desc</label>  
                                            <input type="text" name="prod_desc" id="prod_desc" class="form-control" value="{{ Request()->prod_desc }}" placeholder="Enter Product Desc">  
                                        </div>  
                                        <div class="form-group col-md-2">
                                            <label for="">IO</label>   
                                            <input type="text" name="io" id="io" class="form-control" value="{{ Request()->io }}" placeholder="Enter IO">
                                        </div> 
                                        <div class="form-group col-md-2">
                                            <label for="">Status Tracker</label>   
                                            <select name="status" id="status" class="form-control">
                                                <option value="">Select Status Tracker</option>
                                                <option value="Pick Up">Pick Up</option>
                                                <option value="On Delivery">On Delivery</option> 
                                                <option value="Done">Done</option>
                                            </select>
                                        </div> 
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-success" style="margin-top: 30px"><i class="fa fa-search"></i> Search</button>
                                            <a href="{{ url("admin/delivery/history") }}" class="btn btn-warning" style="margin-top: 30px"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>    
                                </div>    
                            </form> 
                        </div>

                        @include("_message")
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">History Tracker Delivery</h3>
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
                                                <th>Status Date</th>
                                                <th>Date</th>
                                                <th>Process By</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($getRecord as $delivery)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        @if ($delivery->production->prod_no)
                                                           <a style="background-color: #e9e9ff" href="{{ url("admin/production/" . $delivery->production->prod_no)}}">{{ $delivery->production->prod_no }}</a> 
                                                        @else
                                                            {{ "-" }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $delivery->production->prod_desc ?? "-"}}</td>
                                                    <td>{{ $delivery->io }}</td>
                                                    <td>{{ $delivery->status }} </td>
                                                    <td>{{ $delivery->date }}</td>
                                                    <td>{{ $delivery->tracker_by }}</td>
                                                    <td>{{ $delivery->remark }}</td>
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
                                <div class="d-flex justify-content-end px-2 py-2">
                                    <div class="overflow-x:auto; max-width:100px">
                                        {!! $getRecord->onEachSide(1)->appends(request()->except('page'))->links() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection