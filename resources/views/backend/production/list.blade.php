@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-12">
                    <div class="col col-sm-6">
                        <h1>Production Order</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/production/upload')}}" class="btn btn-primary btn-sm"><i class="fa fa-upload"></i> Upload</a>
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
                                    Search Production Order List
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">Number</label>
                                            <input type="number" name="number" class="form-control" placeholder="Enter Number Inventory Tf">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Date</label>
                                            <input type="date" name="DocDate" class="form-control">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">IO</label>
                                            <input type="text" name="U_MEB_NO_IO" class="form-control" placeholder="Enter IO Nomor">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Product Desc</label>
                                            <input type="text" name="prod_desc" class="form-control" placeholder="Enter Product Description">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="">Select Status</option>
                                                <option value="Released">Released</option>
                                                <option value="Closed">Closed</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">No Series</label>
                                            <select name="no_series" id="no_series" class="form-control">
                                                <option value="">Select No Series</option>
                                                @foreach ($getSeries->unique('no_series') as $series)
                                                    <option value="{{ $series->no_series }}"
                                                        {{ Request()->no_series == $series->no_series ? 'selected' : ''}}>
                                                        {{ $series->no_series }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 30px"><i class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/production/po')}}" class="btn btn-warning" style="margin-top: 30px"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    List of All Production
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Product No</th>
                                                <th>Prod Desc</th>
                                                <th>Remain</th>
                                                <th>Doc Number</th>
                                                <th>IO</th>
                                                <th>Due Date</th>
                                                <th>No Series</th>
                                                <th>Status</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        @forelse ($getSeries as $production )
                                            @php
                                                $po = $production->doc_num;
                                                $result = $productionSummary[$po] ?? ['remain' => 0];
                                            @endphp
                                            <tbody>
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $production->prod_no }}</td>
                                                    <td>{{ $production->prod_desc }}</td>
                                                    <td>{{ $result['remain'] }}</td>
                                                    <td>{{ $production->doc_num }}</td>
                                                    <td>{{ $production->io_no }}</td>
                                                    <td>{{ $production->due_date }}</td>
                                                    <td>{{ $production->no_series }}</td>
                                                    <td>
                                                        @if (($user->department == 'Production and Warehouse' && $user->level == 'Manager' || $user->department == 'Production and Warehouse' && $user->level == 'Supervisor') || 
                                                            $user->department == 'Procurement, Installation and Delivery' && $user->level == 'Manager' || $user->department == 'PPIC')
                                                            @if ($production->status == "Released")
                                                                Released
                                                            @else 
                                                                Closed
                                                            @endif
                                                        @else
                                                            @if ($production->status == "Released")
                                                                <a href="{{ url("admin/transaction/stockout", $production->doc_num)}}" class="btn btn-sm btn-outline-success"><i class="fa fa-arrow-right"></i> Released</a>
                                                            @else 
                                                                Closed
                                                            @endif
                                                        @endif
                                                    </td>
                                                    
                                                    <td><a href="{{ url('admin/production/view/' . $production->id) }}" class="btn btn-primary"><i class="fa fa-eye"></i></a></td>
                                                </tr>
                                        @empty
                                                <tr>
                                                    <td colspan="100%">No Record Found</td>
                                                </tr>
                                            </tbody>
                                        @endforelse
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-end px-2 py-2">
                                    <div style="overflow-x:auto; max-width:100%">
                                        {!! $getData->onEachSide(1)->appends(request()->except('page'))->links() !!}
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