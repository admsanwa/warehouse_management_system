@extends('backend.layouts.app')
@section('content')
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
                                            <select name="deliv_status" id="deliv_status" class="form-control">
                                                <option value="">Select Status Tracker</option>
                                                <option value="Pick Up"
                                                    {{ request('deliv_status') == 'Pick Up' ? 'selected' : '' }}>Pick Up</option>
                                                <option value="On Delivery"
                                                    {{ request('deliv_status') == 'On Delivery' ? 'selected' : '' }}>On Delivery
                                                </option>
                                                <option value="Done" {{ request('deliv_status') == 'Done' ? 'selected' : '' }}>
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
                                <h3 class="card-title">List Delivery</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Doc Entry</th>
                                                <th>IO</th>
                                                <th>Inventory Transfer</th>
                                                <th>Production Nomor</th>
                                                <th>Production Description</th>
                                                <th>Status</th>
                                                <th>Status Date</th>
                                                <th>Remarks</th>
                                                <th>Estimate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($mergedData as $row)
                                                @php
                                                    $sap = $row['sap'];
                                                    $delivery = $row['delivery'];
                                                @endphp
                                                @foreach ($sap["Lines"] as $line)
                                                <tr>
                                                        <td>{{ $sap['DocEntry'] }}</td>
                                                        <td>{{ $sap['U_MEB_NO_IO'] }}</td>
                                                        <td>{{ $sap['DocNum'] }}</td>
                                                        <td><a href="{{ url('admin/inventorytf/view?docEntry=' . $sap['DocEntry'] . '&docNum=' . $sap['DocNum']) }}">{{ $line['ItemCode'] }}</a>
                                                        <td>{{ $line['ItemName'] }}</td>
                                                    <td>{{ $delivery->status ?? '-' }}</td>
                                                    <td>{{ $delivery->date ?? '-' }}</td>
                                                    <td>{{ $delivery->remark ?? '-' }}</td>
                                                    <td><a href="#" data-bs-toggle="modal" data-bs-target="#modal_{{ $sap['DocEntry'] }}"><i class="fa fa-eye"></i> Estimate</a></td>
                                                </tr>

                                                @include('partials.modal.tracking', [
                                                    'sap' => $sap,
                                                    'sapline' => $line,
                                                    'delivery' => $delivery
                                                ])
                                                @endforeach
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
    <script>
        fetch('/update-delivery-temp', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
    </script>
@endsection
