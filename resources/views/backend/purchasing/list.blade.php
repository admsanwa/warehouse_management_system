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
                        <a href="{{ url('admin/purchasing/upload')}}" class="btn btn-primary btn-sm"><i class="fa fa-upload"> Upload Data</i></a>
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
                                            <label for="">Delivery Date</label>
                                            <input type="date" name="posting_date" class="form-control" value="{{ Request()->posting_date }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">No PO</label>
                                            <input type="text" name="no_po" class="form-control" value="{{ Request()->no_po }}" placeholder="Enter Number PO">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Vendor</label>
                                            <input type="text" name="vendor" class="form-control" value="{{ Request()->vendor }}" placeholder="Enter Vendor Name">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Contact Person</label>
                                            <input type="text" name="contact_person" class="form-control" value="{{ Request()->contact_person }}" placeholder="Enter Contact Person">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 30px"><i class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/purchasing')}}" class="btn btn-warning" style="margin-top: 30px"><i class="fa fa-eraser"></i> Reset</a>
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
                                                <th>Status</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        @forelse ($getPagination as $purchasing)
                                            @php
                                                $po         = $purchasing->no_po;
                                                $result     = $purchasingSummary[$po] ?? ['remain' => 0];
                                                $resultTwo  = $purchasingSummaryTwo[$po] ?? ['remain' => 0];
                                            @endphp
                                            <tbody>
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $purchasing->no_po }}</td>
                                                    <td>{{ $purchasing->vendor }}</td>
                                                    <td>
                                                        @if ($purchasing->status == "Open" && stripos($purchasing->po_details->item_code, "Maklon") !== false)
                                                            {{ $resultTwo["remain"] }}
                                                        @else
                                                            {{ $result["remain"] }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $purchasing->posting_date }}</td>
                                                    <td>
                                                        @if ($purchasing->status == "Open" && stripos($purchasing->po_details->item_code, "Maklon") !== false)
                                                            <a href="{{ url("admin/transaction/goodissued")}}" class="btn btn-outline-success"><i class="fa fa-arrow-right"></i> Open GI</a>
                                                        @elseif($purchasing->status == "Open")
                                                            <a href="{{ url("admin/transaction/stockin/" . $purchasing->no_po)}}" class="btn btn-outline-success"><i class="fa fa-arrow-right"></i> Open GRPO</a>
                                                        @elseif($purchasing->status == "GR")
                                                            <a href="{{ url("admin/transaction/goodreceipt")}}" class="btn btn-outline-success"><i class="fa fa-arrow-right"></i> Open GR</a>
                                                        @else
                                                            Closed
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('admin/purchasing/view/' . $purchasing->id)}}" class="btn btn-primary"><i class="fa fa-eye"></i></a>
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
                                    <div class="d-flex justify-content-end px-2 py-2">
                                        <div class="overflow-x: auto; max-width: 100%">
                                            {!! $getPagination->onEachSide(1)->appends(request()->except('page'))->links() !!}
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