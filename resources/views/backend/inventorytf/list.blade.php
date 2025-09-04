@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-12">
                    <div class="col col-sm-6">
                        <h1>List Inventory Transfer</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/inventorytf/create')}}" class="btn btn-primary btn-sm"> Create Inventory TF</a>
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
                                    Search Inventory Transfer List
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">Doc Number</label>
                                            <input type="text" name="number" class="form-control" placeholder="Enter Number Inventory Transfer">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Posting Date</label>
                                            <input type="date" name="DocDate" class="form-control">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">IO</label>
                                            <input type="text" name="U_MEB_NO_IO" class="form-control" placeholder="Enter IO Nomor">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="">Select Status</option>
                                                <option value="Open">Open</option>
                                                <option value="Closed">Closed</option>
                                            </select>
                                        </div>
                                         <div class="form-group col-md-2">
                                            <label for="">Series</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="">Select Series</option>
                                                <option value="BKS-25">BKS-25</option>
                                                <option value="JKT-25">JKT-25</option>
                                                <option value="SBY-25">SBY-25</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 30px"><i class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/inventorytf/list')}}" class="btn btn-warning" style="margin-top: 30px"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    List of All Inventory Transfer
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Doc Number</th>
                                                <th>Posting Date</th>
                                                <th>IO</th>
                                                <th>From Warehouse</th>
                                                <th>To Warehouse</th>
                                                <th>Status</th>
                                                <th>Details</th>
                                                <th>View Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>100001758</td>
                                                <td>2025/09/01</td>
                                                <td>328/10/1/VIII/24</td>
                                                <td>BK001</td>
                                                <td>JK002</td>
                                                <td>Open</td>
                                                <td>IT dari BK001 ke JK001 order JAYA OBAYASHI</td>
                                                <td><a href="{{ url('admin/inventorytf/view') }}" class="btn btn-primary"><i class="fa fa-eye"></i></a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-end px-2 py-2">
                                    <div style="overflow-x:auto; max-width:100%">
                                        {{-- {!! $getRecord->onEachSide(1)->appends(request()->except('page'))->links() !!} --}}
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