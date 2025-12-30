@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>History Delivery</h1>
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
                                    Search History Delivery List
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">IO No</label>
                                            <input type="text" name="io" class="form-control"
                                                value="{{ request('io') }}" placeholder="Enter Nomor IO">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Inventory Transfer</label>
                                            <input type="text" name="inv_transfer" class="form-control"
                                                value="{{ request('inv_transfer') }}" placeholder="Enter Inventory Transfer">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Product No</label>
                                            <input type="text" name="prod_no" id="prod_no" class="form-control"
                                                value="{{ request('prod_no') }}" placeholder="Enter Product Nomor">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Product Desc</label>
                                            <input type="text" name="prod_desc" class="form-control"
                                                value="{{ request('prod_desc') }}" placeholder="Enter Product Description">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="status">Status Tracker</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="">Select Status Tracker</option>
                                                <option value="Pick Up"
                                                    {{ request('status') == 'Pick Up' ? 'selected' : '' }}>Pick Up</option>
                                                <option value="On Delivery"
                                                    {{ request('status') == 'On Delivery' ? 'selected' : '' }}>On Delivery
                                                </option>
                                                <option value="Done" {{ request('status') == 'Done' ? 'selected' : '' }}>
                                                    Done</option>
                                            </select>
                                        </div>
                                        {{-- <div class="form-group col-md-2">
                                            <label for="series">Series</label>
                                            <select name="series" class="form-control" id="seriesSelect">
                                            </select>
                                        </div> --}}
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 20px"><i
                                                    class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/delivery/list') }}" class="btn btn-warning"
                                                style="margin-top: 20px"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List History Delivery</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Doc Entry</th>
                                                <th>IO</th>
                                                <th>Inventory Transfer</th>
                                                <th>Prod Nomor</th>
                                                <th>Prod Description</th>
                                                <th>Status</th>
                                                <th>Status Date</th>
                                                <th>Process By</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($getRecord as $delivery)
                                                <tr>
                                                    <td>{{ $delivery->doc_entry }}</td>
                                                    <td>{{ $delivery->io }}</td>
                                                    <td>{{ $delivery->inv_transfer }}</td>
                                                    <td><a
                                                            href="{{ url('admin/inventorytf/view?docEntry=' . $delivery->doc_entry . '&docNum=' . $delivery->prod_order) }}">{{ $delivery->prod_no }}</a>
                                                    <td>{{ $delivery->prod_desc }}</td>
                                                    <td>{{ $delivery->status ?? '-' }}</td>
                                                    <td>{{ $delivery->date ?? '-' }}</td>
                                                    <td>{{ $delivery->tracker_by }}</td>
                                                    <td>{{ $delivery->remark ?? '-' }}</td>
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
                                <div class="d-flex justify-content-between align-items-center">
                                    {!! $getRecord->onEachSide(1)->appends(request()->except('page'))->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
