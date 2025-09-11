@extends("backend.layouts.app")
@section("content")
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Semi Finish Goods</h1>
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
                                    Search List Semi Finish Goods
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
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 20px"><i
                                                    class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/reports/semifg') }}" class="btn btn-warning"
                                                style="margin-top: 20px"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include("_message")
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List Semi Finish Goods</h3>
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
                                                <th>Complete Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($getProds as $prod)
                                                @php
                                                    $sap = $prod['sap'];
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $sap['U_MEB_NO_IO'] }}</td>
                                                    <td>{{ $sap['DocNum'] }}</td>
                                                    <td>{{ $sap['ItemCode'] }}</td>
                                                    <td>{{ $sap['ItemName'] }}</td>
                                                    <td>{{ formatDecimalsSAP($sap['CmpltQty']) }}</div>
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
@endsection