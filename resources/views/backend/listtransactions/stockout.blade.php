@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Stock Out</h1>
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
                                    Search Data Stock Out
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row align-items-end">
                                        <!-- Production Order -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_po">No Production Order</label>
                                                <input type="text" name="no_po" id="no_po" class="form-control"
                                                    value="{{ request()->no_po }}" placeholder="Enter Production Order">
                                            </div>
                                        </div>

                                        <!-- Item Code -->
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="item_code">Item Code</label>
                                                <input type="text" name="item_code" id="item_code" class="form-control"
                                                    value="{{ request()->item_code }}" placeholder="Enter Item Code">
                                            </div>
                                        </div>

                                        <!-- Item Desc -->
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="item_desc">Item Description</label>
                                                <input type="text" name="item_desc" id="item_desc" class="form-control"
                                                    value="{{ request()->item_desc }}" placeholder="Enter Item Desc">
                                            </div>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="col-md-4">
                                            <div class="form-group d-flex" style="gap:10px; margin-top:32px;">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-search"></i> Search
                                                </button>
                                                <a href="{{ url('admin/listtransaction/stockout') }}"
                                                    class="btn btn-warning">
                                                    <i class="fas fa-eraser"></i> Reset
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    List of All Stock Out
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-stripped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Production Order</th>
                                                <th>IO</th>
                                                <th>Item Code</th>
                                                <th>Item Desc</th>
                                                <th>Stock Out</th>
                                                <th>Created At</th>
                                                <th>User</th>
                                            </tr>
                                        </thead>

                                        @forelse ($getRecord as $ifp)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $ifp->no_po ?? '-' }}</td>
                                                <td>{{ $ifp->io ?? '-' }}</td>
                                                <td>{{ $ifp->item_code ?? 'N/A' }}</td>
                                                <td>{{ $ifp->item_desc ?? 'N/A' }}</td>
                                                <td>{{ formatDecimalsSAP($ifp->qty) }}</td>
                                                <td>{{ $ifp->created_at ? $ifp->created_at->format('d-m-Y H:i') : 'N/A' }}
                                                </td>
                                                <td>{{ $ifp->fullname ?? 'N/A' }}</td>
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
