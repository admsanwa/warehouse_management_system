@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-sm-6">
                        <h1>List Items</h1>
                        <form action="{{ url('/api/v1/wms/items/sync') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Sync
                            </button>
                        </form>
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
                                    Search Items
                                </h3>
                            </div>
                            <form action="" method="GET">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label for="">Item Code</label>
                                            <input type="text" class="form-control" value="{{ Request()->code }}" name="code" placeholder="Enter Items Code">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="">Item Name</label>
                                            <input type="text" class="form-control" value="{{ Request()->name }}" name="name" placeholder="Enter Items Name">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 30px;"><i class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/items/list')}}" class="btn btn-warning" style="margin-top: 30px;"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List of All Items</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-stripped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Item Code</th>
                                                <th>Item Description</th>
                                                <th>Uom</th>
                                                {{-- <th>On Hands</th> --}}
                                                <th>Stock Min</th>
                                                <th>Stock SAP</th>
                                                <th>Available</th>
                                                <th>Note</th>
                                                <th>Update</th>
                                            </tr>
                                        </thead>
                                        @forelse ($getRecord as $items)
                                            <tbody>
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $items->code }}</td>
                                                    <td>{{ $items->name }}</td>
                                                    {{-- <td>{{ $items->group == 1 ? 'Raw Material' : ($items->group == 2 ? 'Part Other' : ($items->group == 3 ? 'Unknown' : 'Null')) }}</td> --}}
                                                    <td>{{ $items->uom ? $items->uom : '-' }}</td>
                                                    <td>{{ $items->stock_min }}</td>
                                                    <td>{{ $items->in_stock }}</td>
                                                    <td>{{ ($items->in_stock + ($items->stocks->stock_in ?? 0) - ($items->stocks->stock_out ?? 0)) ?? 0 }}</td>
                                                    <td>{{ $items->stock_min >= ($items->in_stock + ($items->stocks->stock_in ?? 0) - ($items->stocks->stock_out ?? 0)) ?? 0  ? "Stock harus dibeli" : "-" }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($items->updated_at)->format('Y-m-d') }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="100%">No Record Found</td>
                                                </tr>
                                            </tbody>
                                        @endforelse
                                    </table>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-end px-2 py-2">
                                        <div style="overflow-x:auto; max-width:100%">
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