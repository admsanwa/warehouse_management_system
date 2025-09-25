@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Stock In</h1>
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
                                    Search Data Stock In
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row align-items-end">
                                        <!-- Nomor PO -->
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="no_po">Nomor PO</label>
                                                <input type="text" name="no_po" id="no_po" class="form-control"
                                                    value="{{ Request()->no_po }}" placeholder="Enter PO">
                                            </div>
                                        </div>

                                        <!-- Item Code -->
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="item_code">Item Code</label>
                                                <input type="text" name="item_code" id="item_code" class="form-control"
                                                    value="{{ Request()->item_code }}" placeholder="Enter Item Code">
                                            </div>
                                        </div>

                                        <!-- Item Description -->
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="item_desc">Item Description</label>
                                                <input type="text" name="item_desc" id="item_desc" class="form-control"
                                                    value="{{ Request()->item_desc }}" placeholder="Enter Item Desc">
                                            </div>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="col-md-3">
                                            <div class="form-group d-flex" style="gap:10px;">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa fa-search"></i> Search
                                                </button>
                                                <a href="{{ url('admin/listtransaction/stockin') }}"
                                                    class="btn btn-warning">
                                                    <i class="fa fa-eraser"></i> Reset
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
                                    List of All Stock In
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nomor Po</th>
                                                <th>Item Code</th>
                                                <th>Item Desc</th>
                                                <th>Stock In</th>
                                                <th>Created At</th>
                                                <th>User</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($getRecord as $grpo)
                                                <tr>
                                                    <td>{{ $loop->iteration + ($getRecord->currentPage() - 1) * $getRecord->perPage() }}
                                                    </td>
                                                    <td>{{ $grpo->no_po ?? 'N/A' }}</td>
                                                    <td>{{ $grpo->item_code ?? 'N/A' }}</td>
                                                    <td>{{ $grpo->item_desc ?? 'N/A' }}</td>
                                                    <td>{{ formatDecimalsSAP($grpo->qty) }}</td>
                                                    <td>{{ $grpo->created_at ? $grpo->created_at->format('d-m-Y H:i') : 'N/A' }}
                                                    </td>
                                                    <td>{{ $grpo->fullname ?? 'N/A' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="100%">No Record Found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
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
