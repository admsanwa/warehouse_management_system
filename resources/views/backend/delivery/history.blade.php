@extends("backend.layouts.app")
@section("content")
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Delivery</h1>
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
                                            <label for="">IO No</label>
                                            <input type="text" name="io_no" class="form-control"
                                                placeholder="Enter Nomor IO" value="{{ Request()->io_no }}">
                                        </div>
                                         <div class="form-group col-md-2">
                                            <label for="">Product Order</label>
                                            <input type="text" name="prod_order" class="form-control"
                                                placeholder="Enter Product Order" value="{{ Request()->prod_order }}">
                                        </div>
                                      <div class="form-group col-md-2">
                                            <label for="">Product No</label>
                                            <input type="text" name="prod_no" id="prod_no" class="form-control"
                                                placeholder="Enter Product Nomor" value="{{ Request()->prod_no }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Product Desc</label>
                                            <input type="text" name="prod_desc" class="form-control"
                                                placeholder="Enter Product Description" value="{{ Request()->prod_desc }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="status">Status Tracker</label>
                                            <select name="delivery_status" id="delivery_status" class="form-control">
                                                <option value="">Select Status Tracker</option>
                                                <option value="Pick Up" {{ request('delivery_status') == 'Pick Up' ? 'selected' : '' }}>Pick Up</option>
                                                <option value="On Delivery" {{ request('delivery_status') == 'On Delivery' ? 'selected' : '' }}>On Delivery</option>
                                                <option value="Done" {{ request('delivery_status') == 'Done' ? 'selected' : '' }}>Done</option>
                                            </select>
                                        </div>
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
                                                <th>IO</th>
                                                <th>Prod Order</th>
                                                <th>Prod Nomor</th>
                                                <th>Prod Description</th>
                                                <th>Status</th>
                                                <th>Status Date</th>
                                                <th>Process By</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($mergedData as $row)
                                                @php
                                                    $sap = $row['sap'];
                                                    $delivery = $row['delivery'];
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $sap['U_MEB_NO_IO'] }}</td>
                                                    <td>{{ $sap['DocNum'] }}</td>
                                                    <td>{{ $sap['ItemCode'] }}</td>
                                                    <td>{{ $sap['ItemName'] }}</td>
                                                    <td>{{ $delivery->status ?? "-" }}</td>
                                                    <td>{{ $delivery->date ?? "-" }}</td>
                                                    <td>{{ $delivery->tracker_by ?? "-" }}</td>
                                                    <td>{{ $delivery->remark ?? "-" }}</td>
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
                            @php
                                $query = request()->all();
                            @endphp
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        Showing page <b class="text-primary">{{ $page }}</b> of
                                        {{ $totalPages }} (Total {{ $total }} records)
                                    </span>

                                    <div class="btn-group">
                                        {{-- First + Previous --}}
                                        @if ($page > 1)
                                            {{-- <a href="{{ url()->current() . '?' . http_build_query(array_merge($query, ['page' => 1, 'limit' => $limit])) }}"
                                                class="btn btn-outline-primary btn-sm" aria-label="First Page">First</a> --}}

                                            <a href="{{ url()->current() . '?' . http_build_query(array_merge($query, ['page' => $page - 1, 'limit' => $limit])) }}"
                                                class="btn btn-outline-primary btn-sm"
                                                aria-label="Previous Page">Previous</a>
                                        @endif

                                        {{-- Current Page --}}
                                        <span class="btn btn-primary btn-sm disabled">
                                            {{ $page }}
                                        </span>

                                        {{-- Next + Last --}}
                                        @if ($page < $totalPages)
                                            <a href="{{ url()->current() . '?' . http_build_query(array_merge($query, ['page' => $page + 1, 'limit' => $limit])) }}"
                                                class="btn btn-outline-primary btn-sm" aria-label="Next Page">Next</a>
                                            {{-- 
                                            <a href="{{ url()->current() . '?' . http_build_query(array_merge($query, ['page' => $totalPages, 'limit' => $limit])) }}"
                                                class="btn btn-outline-primary btn-sm" aria-label="Last Page">Last</a> --}}
                                        @endif
                                    </div>
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