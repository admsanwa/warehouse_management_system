@extends("backend.layouts.app")
@section("content")
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Quality Control</h1>
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
                                    Search Quality Control List
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
                                            <input type="text" name="no_io" id="no_io" class="form-control" value="{{ Request()->no_io }}" placeholder="Enter IO">
                                        </div> 
                                        <div class="form-group col-md-2">
                                            <label for="">Result QC</label>
                                            <select name="result" id="result" class="form-control">
                                                <option value="">Select Result</option>
                                                <option value="1" {{ request('result') == 1 ? 'selected' : ''}}>OK</option>
                                                <option value="2" {{ request('result') == 2 ? 'selected' : ''}}>NG</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <button type="submit" class="btn btn-success" style="margin-top: 30px"><i class="fa fa-search"></i> Search</button>
                                            <a href="{{ url("admin/quality/list") }}" class="btn btn-warning" style="margin-top: 30px"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>    
                                </div>    
                            </form> 
                        </div>

                        @include("_message")
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List Quality Control</h3>
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
                                                <th>QC</th>
                                                <th>Status</th>
                                                <th>Remarks</th>
                                                <th>Check</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($getRecord as $quality)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        @if ($quality->prod_no)
                                                           <a style="background-color: #e9e9ff" href="{{ url("admin/production/" . $quality->prod_no)}}">{{ $quality->prod_no }}</a> 
                                                        @else
                                                            {{ "-" }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $quality->prod_desc ?? "-"}}</td>
                                                    <td>{{ $quality->io_no }}</td>
                                                    <td>
                                                        @if ($quality->qualityTwo && $quality->qualityTwo->result !== null)
                                                            {{ $quality->qualityTwo->result === 1 ? "OK" : ($quality->qualityTwo->result === 2 ? "NG" : ($quality->qualityTwo->result === 3 ? "Need Approval" : "-" ))}}
                                                        @else
                                                           -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ( $quality->qualityTwo && $quality->qualityTwo->result_by === "delvi" )
                                                            {{ "Approve by " . $quality->qualityTwo->user->fullname}}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ $quality->qualityTwo->remark ?? "-"}}</td>
                                                    <td><a href="#" data-bs-toggle="modal" data-bs-target="#modal_{{ $quality->id }}"><i class="fa fa-eye"></i> Check</a></td>
                                                </tr>

                                                @include('partials.modal.assessment', ['quality' => $quality])
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