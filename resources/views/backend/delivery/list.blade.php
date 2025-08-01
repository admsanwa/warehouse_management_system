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
                            <a href="{{ url("admin/delivery/history") }}" class="btn btn-primary btn-sm">Riwayat Delivery</a>
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
                                    Search Delivery List
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
                                            <input type="text" name="io_no" id="io_no" class="form-control" value="{{ Request()->io_no }}" placeholder="Enter IO">
                                        </div> 
                                        <div class="form-group col-md-2">
                                            <label for="">Status</label>   
                                            <input type="text" name="status" id="status" class="form-control" value="{{ Request()->status }}" placeholder="Enter Status Tracker">
                                        </div> 
                                        <div class="form-group col-md-2">
                                            <button type="submit" class="btn btn-success" style="margin-top: 30px"><i class="fa fa-search"></i> Search</button>
                                            <a href="{{ url("admin/delivery") }}" class="btn btn-warning" style="margin-top: 30px"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>    
                                </div>    
                            </form> 
                        </div>

                        @include("_message")
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List Delivery</h3>
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
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Remarks</th>
                                                <th>Estimate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($getRecord as $delivery)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        @if ($delivery->prod_no)
                                                           <a style="background-color: #e9e9ff" href="{{ url("admin/production/" . $delivery->prod_no)}}">{{ $delivery->prod_no }}</a> 
                                                        @else
                                                            {{ "-" }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $delivery->prod_desc ?? "-"}}</td>
                                                    <td>{{ $delivery->io_no }}</td>
                                                    <td>{{ $delivery->delivery->status ?? "-" }}</td>
                                                    <td>{{ $delivery->delivery->date ?? "-" }}</td>
                                                    <td>{{ $delivery->delivery->remark ?? "-" }}</td>
                                                    <td><a href="#" data-bs-toggle="modal" data-bs-target="#modal_{{ $delivery->id }}"><i class="fa fa-eye"></i> Estimate</a></td>
                                                </tr>

                                                @include('partials.modal.tracking', ['delivery' => $delivery])
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
                                        {{-- {!! $getRecord->onEachSide(1)->appends(request()->except('page'))->links() !!} --}}
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