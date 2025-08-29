@extends("backend.layouts.app")
@section("content")
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Quality Control</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url("admin/quality/list") }}" class="btn btn-primary btn-sm">List QC</a>
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
                                            <input type="text" name="io" id="io" class="form-control" value="{{ Request()->io }}" placeholder="Enter IO">
                                        </div> 
                                       <div class="form-group col-md-2">
                                            <label for="">Result QC</label>
                                            <select name="result" id="result" class="form-control">
                                                <option value="">Select Result</option>
                                                <option value="1" {{ Request('result') == 1 ? 'selected' : ''}}>OK</option>
                                                <option value="2" {{ Request('result') == 2 ? 'selected' : ''}}>NG</option>
                                                <option value="3" {{ Request('result') == 3 ? 'selected' : ''}}>Need Approval</option>
                                                <option value="5" {{ request('result') == 5 ? 'selected' : ''}}>Painting by Inhouse</option>
                                                <option value="6" {{ request('result') == 6 ? 'selected' : ''}}>Painting by Makoon</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <button type="submit" class="btn btn-success" style="margin-top: 30px"><i class="fa fa-search"></i> Search</button>
                                            <a href="{{ url("admin/quality/history") }}" class="btn btn-warning" style="margin-top: 30px"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>    
                                </div>    
                            </form> 
                        </div>

                        @include("_message")
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">History Quality Control</h3>
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
                                                <th>Result By</th>
                                                <th>Date</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($getRecord as $quality)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        @if ($quality->production->prod_no)
                                                           <a style="background-color: #e9e9ff" href="{{ url("admin/production/" . $quality->production->prod_no)}}">{{ $quality->production->prod_no }}</a> 
                                                        @else
                                                            {{ "-" }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $quality->production->prod_desc ?? "-"}}</td>
                                                    <td>{{ $quality->io }}</td>
                                                    <td>
                                                         @php
                                                            $statusMap = [
                                                                1 => 'OK',
                                                                2 => 'NG',
                                                                3 => 'Need Approval',
                                                                4 => 'Need Paint',
                                                                5 => 'Painting by Inhouse',
                                                                6 => 'Painting by Makloon'
                                                            ];
                                                        @endphp

                                                        {{ $quality->result !== null 
                                                            ? ($statusMap[$quality->result] ?? '-') 
                                                            : '-' }}
                                                    </td>
                                                    <td>{{ $quality->result_by }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($quality->updated_at)->format('Y-m-d') }}</td>
                                                    <td>{{ $quality->remark }}</td>
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