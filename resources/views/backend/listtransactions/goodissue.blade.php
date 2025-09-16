@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Good Issue</h1>
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
                                    Search Data Good Issue
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        {{-- <div class="form-group col-md-2">
                                            <label for="">IO</label>
                                            <input type="text" name="io" class="form-control" value="{{ Request()->io }}" placeholder="Enter IO">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Purchase Order</label>
                                            <input type="text" name="po" class="form-control" value="{{ Request()->po }}" placeholder="Enter Purchase Order">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Internal No</label>
                                            <input type="text" name="internal_no" class="form-control" value="{{ Request()->internal_no }}" placeholder="Enter Internal No">
                                        </div> --}}
                                        <div class="form-group col-md-2">
                                            <label for="">Item Code</label>
                                            <input type="text" name="code" class="form-control"
                                                value="{{ Request()->code }}" placeholder="Enter Item Code">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Item Desc</label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ Request()->name }}" placeholder="Enter Item Name">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 30px"><i
                                                    class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/listtransaction/stockin') }}" class="btn btn-warning"
                                                style="margin-top: 30px"><i class="fa fa-eraser"></i>Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    List of All Good Issue
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-stripped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>IO</th>
                                                <th>Purchase Order</th>
                                                <th>Internal No</th>
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                                <th>Good Issue</th>
                                                <th>Cretaed At</th>
                                                <th>User</th>
                                            </tr>
                                        </thead>

                                        @forelse ($getRecord as $gi)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $gi->io ?? 'N/A' }}</td>
                                                <td>{{ $gi->po ?? '-' }}</td>
                                                <td>{{ $gi->internal_no ?? 'N/A' }}</td>
                                                <td>{{ $gi->item_code }}</td>
                                                <td>{{ $gi->item_desc }}</td>
                                                <td>{{ formatDecimalsSAP($gi->qty) }}</td>
                                                <td>{{ $gi->created_at->format('d-m-Y H:i') }}</td>
                                                <td>{{ $gi->fullname }}</td>
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
