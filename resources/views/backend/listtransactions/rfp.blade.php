@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Receipt From Production</h1>
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
                                    Search Data Receipt From Production
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">IO</label>
                                            <input type="text" name="io" class="form-control" value="{{ Request()->io }}" placeholder="Enter Item Code">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Production Order</label>
                                            <input type="text" name="prod_order" class="form-control" value="{{ Request()->prod_order }}" placeholder="Enter Item Code">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Production Nomor</label>
                                            <input type="text" name="prod_no" class="form-control" value="{{ Request()->prod_no }}" placeholder="Enter Item Code">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Production Description</label>
                                            <input type="text" name="prod_desc" class="form-control" value="{{ Request()->prod_desc }}" placeholder="Enter Item Desc">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 30px"><i class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/listtransaction/rfp') }}" class="btn btn-warning" style="margin-top: 30px"><i class="fa fa-eraser"></i>Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    List of All Receipt From Production
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-stripped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>IO</th>
                                                <th>Production Order</th>
                                                <th>Production Nomor</th>
                                                <th>Production Description</th>
                                                <th>Qty</th>
                                                <th>Update</th>
                                            </tr>
                                        </thead>

                                       @forelse ($getRecord as $rfp )                                           
                                           <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $rfp->io ?? 'N/A'}}</td>
                                            <td>{{ $rfp->prod_order ?? 'N/A'}}</td>
                                            <td>{{ $rfp->prod_no }}</td>
                                            <td>{{ $rfp->prod_desc }}</td>
                                            <td>{{ $rfp->qty }}</td>
                                            <td>{{ $rfp->updated_at }}</td>
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
                                            {!! $getRecord->onEachSide(1)->appends(request()->except('page'))->links() !!}
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