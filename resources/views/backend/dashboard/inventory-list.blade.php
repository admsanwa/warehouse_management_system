@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">List</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <!-- Search Card -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Search Inventory Transfer List</h3>
                    </div>
                    <form action="" method="get">
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="U_MEB_NO_IO">IO</label>
                                    <input type="text" id="U_MEB_NO_IO" name="U_MEB_NO_IO" class="form-control"
                                        placeholder="Enter IO Number" value="{{ request()->U_MEB_NO_IO }}">
                                </div>
                                <div class="form-group col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                    <a href="{{ url('admin/dashboard-list') }}" class="btn btn-warning">
                                        <i class="fa fa-eraser"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Dashboard Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dashboard Inventory Transfer</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>IO</th>
                                        <th>Customer Name</th>
                                        <th>Project</th>
                                        <th>Delivery</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($invtf as $inv)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $inv['U_MEB_NO_IO'] ?? '-' }}</td>
                                            <td>{{ $inv['CardName'] ?? '-' }}</td>
                                            <td>{{ $inv['PrjName'] ?? '-' }}</td>
                                            <td>{{ $inv['U_SI_HARI_TGL_KIRIM'] ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No records found</td>
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
                                @if ($page > 1)
                                    <a href="{{ url()->current() . '?' . http_build_query(array_merge($query, ['page' => $page - 1, 'limit' => $limit])) }}"
                                        class="btn btn-outline-primary btn-sm">Previous</a>
                                @endif
                                <span class="btn btn-primary btn-sm disabled">{{ $page }}</span>
                                @if ($page < $totalPages)
                                    <a href="{{ url()->current() . '?' . http_build_query(array_merge($query, ['page' => $page + 1, 'limit' => $limit])) }}"
                                        class="btn btn-outline-primary btn-sm">Next</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
