@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-12">
                    <div class="col col-sm-6">
                        <h1>Production</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/production/upload')}}" class="btn btn-primary btn-sm"><i class="fa fa-upload"></i> Upload Data</a>
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
                                    Search Production List
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">IO No</label>
                                            <input type="text" name="io_no" class="form-control" placeholder="Enter Nomor IO">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Doc Number</label>
                                            <input type="number" name="doc_num" class="form-control" placeholder="Enter Doc Nomor">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Product No</label>
                                            <input type="text" name="prod_no" class="form-control" placeholder="Enter Product Nomor">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Product Desc</label>
                                            <input type="text" name="prod_desc" class="form-control" placeholder="Enter Product Description">
                                        </div>
                                         <div class="form-group col-md-2">
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
                                                <th>Status</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        @forelse ($getData as $production )
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
                                                    <td>
                                                        @if ($production->status == "Released")
                                                            <a href="{{ url("admin/transaction/stockout", $production->doc_num)}}" class="btn btn-sm btn-outline-success"><i class="fa fa-arrow-right"></i> Released</a>
                                                        @else 
                                                            Closed
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