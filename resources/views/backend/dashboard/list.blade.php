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
            $authLevel = Auth::user()->level;
            $authEmail = Auth::user()->email;
        @endphp

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    {{-- Item to Purchase --}}
                    @if (
                        $authDept == 'PPIC' ||
                            $authDept == 'IT' ||
                            $authDept == 'Purchasing' ||
                            $authDept == 'Fabrication' ||
                            (($authDept == 'Production and Warehouse' && $authLevel == 'Manager') ||
                                ($authLevel == 'Supervisor' && $authEmail != 'warehouse_sby@sanwamas.co.id')))
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $needBuy > 0 ? $needBuy . '+' : 0 }}</h3>
                                    <p>Items to Purchase</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <a href="{{ url('admin/items/list?item_code=&item_desc=&stockNotes=1&warehouse=BK001') }}"
                                    class="small-box-footer">
                                    More info <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                    {{-- Issue for Prod --}}
                    @if (
                            $authDept == 'IT' ||
                            ($authDept == 'Production and Warehouse' && ($authLevel == 'Manager' || $authEmail == 'warehouse_sby@sanwamas.co.id')) ||
                            $authDept == 'Production')
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $prodRelease > 0 ? $prodRelease . '+' : 0 }}</h3>

                                    <p>Issue For Production</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-ios-cog-outline"></i>
                                </div>
                                <a href="{{ url('admin/dashboard/ifp') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @endif
                    {{-- after check --}}
                    @if (
                        $authDept == 'IT' ||
                            $authDept == 'Quality Control' ||
                            (($authDept == 'Production and Warehouse' && $authLevel == 'Manager') ||
                                ($authLevel == 'Supervisor' && $authEmail != 'warehouse_sby@sanwamas.co.id')) ||
                            $authDept == 'Production' ||
                            ($authDept == 'Quality Control' && $authLevel == 'Manager'))
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
                                <a href="{{ url('admin/quality/history') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @endif
                    {{-- delivery status --}}
                    @if (
                        $authDept == 'IT' ||
                        $authDept == 'Procurement, Installation and Delivery' ||
                        $authDept == 'Production and Warehouse' ||
                        $authDept == 'Production' ||
                        ($authDept == 'Quality Control' && ($authLevel == 'Manager' || $authLevel == 'Staff')) ||
                        $authDept == 'Sales' ||
                        ($authDept == 'Accounting and Finance' && $authLevel == 'Supervisor'))
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
                                <a href="{{ url('admin/delivery/history') }}" class="small-box-footer">More info <i
                                        class="fas fa-arrow-circle-right"></i></a>

                            </div>
                        </div>
                    @endif
                    {{-- good receipt po --}}
                    @if (
                        $authDept == 'IT' || ($authDept == 'Production and Warehouse' && $authEmail != 'warehouse_sby@sanwamas.co.id')
                        )
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $grpo > 0 ? $grpo . '+' : 0 }}</h3>

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
                    {{-- good issued --}}
                    @if (
                        $authDept == 'IT' || ($authDept == 'Production and Warehouse' && $authEmail != 'warehouse_sby@sanwamas.co.id'))
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $goodIssued > 0 ? $goodIssued . '+' : 0 }}</h3>
                                    <p>Good Issued</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-log-out"></i>
                                </div>
                                <a href="{{ url('admin/dashboard/goodissued') }}" class="small-box-footer">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @endif
                    {{-- good receipt --}}
                    @if (
                        $authDept == 'IT' || ($authDept == 'Production and Warehouse' && $authEmail != 'warehouse_sby@sanwamas.co.id')
                        )
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $goodReceipt > 0 ? $goodReceipt . '+' : 0 }}</h3>
                                    <p>Good Receipt</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-android-checkbox-outline"></i>
                                </div>
                                <a href="{{ url('admin/dashboard/goodreceipt') }}" class="small-box-footer">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @endif
                    {{-- receipt from prod --}}
                    @if (
                        $authDept == 'IT' ||
                            ($authDept == 'Production and Warehouse' && $authLevel == 'Manager' || $authEmail == 'warehouse_sby@sanwamas.co.id') ||
                            $authDept == 'Production')
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $rfp > 0 ? $rfp . '+' : 0 }}</h3>

                                    <p>Receipt from Production</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-checkmark-circled"></i>
                                </div>
                                <a href="{{ url('admin/dashboard/rfp') }}" class="small-box-footer">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @endif
                    {{-- memo --}}
                    @if (
                        $authDept == 'IT' ||
                        $authDept == 'PPIC' ||
                        ($authDept == 'Procurement, Installation and Delivery' && $authLevel == 'Manager') ||
                        $authDept == 'Production' ||
                        ($authDept == 'Production and Warehouse' && $authLevel == 'Manager') ||
                        ($authDept == 'Production and Warehouse' && $authEmail != 'warehouse_sby@sanwamas.co.id') ||
                        ($authDept == 'Quality Control' && ($authLevel == 'Manager' || $authLevel == 'Staff')))
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $memos }}</h3>
                                    <p>Memo</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-checkmark"></i>
                                </div>
                                <a href="{{ url('admin/production/listmemo') }}" class="small-box-footer">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    @endif
                    {{-- Bon --}}
                    @if (
                        $authDept == 'IT' ||
                            $authDept == 'PPIC' ||
                            $authDept == 'Purchasing' ||
                            ($authDept == 'Procurement, Installation and Delivery' && $authLevel == 'Manager') ||
                            ($authDept == 'Production and Warehouse' && $authLevel == 'Manager'))
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <diiv class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $bons }}</h3>
                                    <p>Bon Pembelian Barang</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-checkmark"></i>
                                </div>
                                <a href="{{ url('admin/production/listbon') }}" class="small-box-footer">More info
                                    <i class="fas fa-arrow-circle-right"></i></a>
                            </diiv>
                        </div>
                    @endif
                    <!-- ./col -->
                </div>
                <!-- /.row -->
                <!-- Main row -->
                <!-- /.row (main row) -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection
