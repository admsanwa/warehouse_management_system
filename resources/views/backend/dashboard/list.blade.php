@extends('backend.layouts.app')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">List</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        @php
            $authDept = Auth::user()->department;
        @endphp

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    @if ($authDept == 'PPIC' || $authDept == 'IT' || $authDept == 'Purchasing' || $authDept == 'Production and Warehouse')
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $needBuy }}</h3>
                                    <p>Items to Purchase</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <a href="{{ url('admin/dashboard/minstock') }}" class="small-box-footer">
                                    More info <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                    @if ($authDept == 'PPIC' || $authDept == 'IT' || $authDept == 'Production and Warehouse' || $authDept == 'Production')
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $prodRelease }}</h3>

                                    <p>Production Release</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-ios-cog-outline"></i>
                                </div>
                                <a href="{{ url('admin/dashboard/prodrelease') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @endif
                    <!-- ./col -->
                    @if ($authDept == 'IT' || $authDept == 'Quality Control')
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $afterCheck }}</h3>

                                    <p>After Check</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-android-checkmark-circle"></i>
                                </div>
                                <a href="{{ url('admin/dashboard/aftercheck') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @endif
                    <!-- ./col -->
                    @if ($authDept == 'IT' || $authDept == 'Procurement, Installation and Delivery')
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $deliveryStatus }}</h3>

                                    <p>Delivery Status</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-ios-pricetags-outline"></i>
                                </div>
                                <a href="{{ url('admin/dashboard/delivstatus') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>

                            </div>
                        </div>
                    @endif
                    @if ($authDept == 'IT' || $authDept == 'Production and Warehouse')
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $purchaseOrder }}</h3>

                                    <p>Good Receipt PO</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-clipboard"></i>
                                </div>
                                <a href="{{ url('admin/dashboard/goodreceiptpo') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @endif
                    @if ($authDept == 'IT' || $authDept == 'Production and Warehouse')
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $goodIssued }}</h3>

                                    <p>Good Issued</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-log-out"></i>
                                </div>
                                <a href="{{ url('admin/dashboard/goodissued') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @endif
                    @if ($authDept == 'IT' || $authDept == 'Production and Warehouse')
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $goodReceipt }}</h3>

                                    <p>Good Receipt</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-android-checkbox-outline"></i>
                                </div>
                                <a href="{{ url('admin/dashboard/goodreceipt') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @endif
                    @if ($authDept == 'IT' || $authDept == 'Production and Warehouse')
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $rfp }}</h3>

                                    <p>Receipt from Production</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-checkmark-circled"></i>
                                </div>
                                <a href="{{ url('admin/dashboard/rfp') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @endif
                    <!-- ./col -->
                </div>
                <!-- /.row -->
                <!-- Main row -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Dashboard Inventory Transfer
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>IO</th>
                                        <th>Customer Name</th>
                                        <th>Project</th>
                                        <th>Prod Order</th>
                                        <th>Delivery</th>
                                        {{-- <th>Doc Date</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invtf as $inv)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $inv['U_MEB_NO_IO'] ?? '-' }}</td>
                                            <td>{{ $inv['CardName'] ?? '-' }}</td>
                                            <td>{{ $inv['PrjName'] ?? '-' }}</td>
                                            <td>{{ $inv['U_MEB_No_Prod_Order'] ?? '-' }}</td>
                                            <td>{{ $inv['U_SI_HARI_TGL_KIRIM'] ?? '-' }}</td>
                                            {{-- <td>{{ $inv['DocDate'] ?? '-' }}</td> --}}
                                        </tr>
                                    @endforeach
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
                                        class="btn btn-outline-primary btn-sm" aria-label="Previous Page">Previous</a>
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
                <!-- /.row (main row) -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection
