@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>List Prepare Good Receipt</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <section class="col col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Search List Prepare Good Receipt</h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">IO :</label>
                                            <input type="text" name="io" id="io" class="form-control"
                                                placeholder="Enter IO">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">PO Maklon :</label>
                                            <input type="text" name="po" id="po" class="form-control"
                                                placeholder="Enter PO Maklon">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">SO :</label>
                                            <input type="text" name="so" id="so" class="form-control"
                                                placeholder="Enter SO">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Internal No :</label>
                                            <input type="text" name="internal_no" id="internal_no" class="form-control" placeholder="Enter Internal No">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 30px;"><i
                                                    class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/transaction/listgoodreceipt') }}" class="btn btn-warning"
                                                style="margin-top: 30px"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List of All Prepare Good Receipt</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>IO</th>
                                                <th>PO Maklon</th>
                                                <th>Sales Order</th>
                                                <th>Internal No</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        @forelse ($getRecord as $listgr)
                                            <tbody>
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $listgr->io ?? '-'}}</td>
                                                    <td>{{ $listgr->po ?? '-' }}</td>
                                                    <td>{{ $listgr->so ?? '-' }}</td>
                                                    <td>{{ $listgr->internal_no }}</td>
                                                    <td>
                                                        <a href="{{ url('admin/transaction/postgoodreceipt/' . $listgr->doc_entry) }}"
                                                            class="btn btn-primary">
                                                            <div class="fa fa-eye"></div>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="100%">Not Record Found</td>
                                                </tr>
                                            </tbody>
                                        @endforelse
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-end px-2 py-2">
                                    <div style="overflow-x: auto; max-width:100%">
                                        {!! $getRecord->onEachSide(1)->appends(request()->except('page'))->links() !!}
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
