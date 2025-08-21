@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Purchasing</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/purchasing/upload') }}" class="btn btn-primary btn-sm"><i
                                    class="fa fa-upload"> Upload Data</i></a>
                        </ol>
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
                                    Search Purchasing List
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">Posting Date</label>
                                            <input type="date" name="docDate" class="form-control"
                                                value="{{ Request()->docDate ? date('Y-m-d', strtotime(Request()->docDate)) : '' }}">
                                        </div>
                                        {{-- <div class="form-group col-md-2">
                                            <label for="">Delivery Date</label>
                                            <input type="date" name="docDueDate" class="form-control"
                                                value="{{ Request()->docDueDate ? date('Y-m-d', strtotime(Request()->docDueDate)) : '' }}">
                                        </div> --}}
                                        <div class="form-group col-md-2">
                                            <label for="">No PO</label>
                                            <input type="text" name="docNum" class="form-control"
                                                value="{{ Request()->docNum }}" placeholder="Enter Number PO">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Vendor</label>
                                            <input type="text" name="cardName" class="form-control"
                                                value="{{ Request()->cardName }}" placeholder="Enter Vendor Name">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="docStatus">Status</label>
                                            <select name="docStatus" id="docStatus" class="form-control">
                                                <option value="" disabled>-- Choose Status --</option>
                                                @foreach (['Open', 'Close', 'All'] as $status)
                                                    @php
                                                        $value = $status == 'All' ? '' : $status;
                                                    @endphp
                                                    <option value="{{ $value }}"
                                                        {{ Request()->docStatus == $value ? 'selected' : '' }}>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group d-flex align-items-end gap-2">
                                            <button type="submit" class="btn btn-primary mr-2">
                                                <i class="fa fa-search mr-1"></i> Search
                                            </button>
                                            <a href="{{ url('admin/purchasing') }}" class="btn btn-warning">
                                                <i class="fa fa-eraser mr-1"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    List of All Purchasing
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-stripped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>No PO</th>
                                                <th>Vendor</th>
                                                <th>Remain</th>
                                                <th>Posting Date</th>
                                                {{-- <th>Delivery Due Date</th> --}}
                                                <th>Status</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        @forelse ($orders as $order)
                                            <tbody>
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $order['DocNum'] }}</td>
                                                    <td>{{ $order['CardName'] }}</td>
                                                    <td>-</td>
                                                    {{-- <td>{{ date('Y-m-d', strtotime($order['DocDate'])) }}</td> --}}
                                                    <td>{{ $order['DocDate'] }}</td>
                                                    {{-- <td>{{ date('Y-m-d', strtotime($order['DocDueDate'])) }}</td> --}}
                                                    <td>
                                                        @php
                                                            $itemCode = $order['Lines'][0]['ItemCode'] ?? '';
                                                        @endphp
                                                        @if ($order['DocStatus'] == 'Open' && $itemCode)
                                                            @if (stripos($itemCode, 'Maklon') !== false)
                                                                <a href="{{ url('admin/transaction/goodissued') }}"
                                                                    class="btn btn-outline-success">
                                                                    <i class="fa fa-arrow-right"></i> Open GI
                                                                </a>
                                                            @elseif (strpos($itemCode, 'RM') === 0)
                                                                <a href="{{ url('admin/transaction/stockin?po=' . $order['DocNum'] . '&docEntry=' . $order['DocEntry']) }}"
                                                                    class="btn btn-outline-success">
                                                                    <i class="fa fa-arrow-right"></i> Open GRPO
                                                                </a>
                                                            @elseif (strpos($itemCode, 'SF') === 0)
                                                                <a href="{{ url('admin/transaction/goodreceipt') }}"
                                                                    class="btn btn-outline-success">
                                                                    <i class="fa fa-arrow-right"></i> Open GR
                                                                </a>
                                                            @else
                                                                <a href="{{ url('admin/transaction/stockin/' . $order['DocNum']) }}"
                                                                    class="btn btn-outline-success">
                                                                    <i class="fa fa-arrow-right"></i> Open GRPO
                                                                </a>
                                                            @endif
                                                        @elseif($order['DocStatus'] == 'Open')
                                                            Open
                                                        @else
                                                            Close
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('admin/purchasing/view?docNum=' . $order['DocNum'] . '&docEntry=' . $order['DocEntry']) }}"
                                                            class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                    </td>
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
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>
                                            Showing page <b class="text-primary">{{ $page }}</b> of
                                            {{ $totalPages }}
                                            (Total {{ $total }} records)
                                        </span>

                                        <div class="btn-group">
                                            {{-- First + Previous --}}
                                            @if ($page > 1)
                                                <a href="{{ url('/admin/purchasing?page=1&limit=' . $limit) }}"
                                                    class="btn btn-outline-primary btn-sm" aria-label="First Page">First</a>

                                                <a href="{{ url('/admin/purchasing?page=' . ($page - 1) . '&limit=' . $limit) }}"
                                                    class="btn btn-outline-primary btn-sm"
                                                    aria-label="Previous Page">Previous</a>
                                            @endif

                                            {{-- Current Page --}}
                                            <span class="btn btn-primary btn-sm disabled">
                                                {{ $page }}
                                            </span>

                                            {{-- Next + Last --}}
                                            @if ($page < $totalPages)
                                                <a href="{{ url('/admin/purchasing?page=' . ($page + 1) . '&limit=' . $limit) }}"
                                                    class="btn btn-outline-primary btn-sm" aria-label="Next Page">Next</a>

                                                <a href="{{ url('/admin/purchasing?page=' . $totalPages . '&limit=' . $limit) }}"
                                                    class="btn btn-outline-primary btn-sm" aria-label="Last Page">Last</a>
                                            @endif
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
