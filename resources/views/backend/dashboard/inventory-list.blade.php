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
                            <div class="row g-3">
                                {{-- Input IO --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="U_MEB_NO_IO">IO</label>
                                        <input type="text" id="U_MEB_NO_IO" name="U_MEB_NO_IO" class="form-control"
                                            placeholder="Enter IO Number" value="{{ request()->U_MEB_NO_IO }}">
                                    </div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="series">Series</label>
                                    <select name="series" class="form-control" id="seriesSelect">
                                    </select>
                                </div>
                                {{-- Tombol --}}
                                <div class="col-md-6 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary">
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
                        <h3 class="card-title">Work Order Dashboard</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="fullscreen">
                                <i class="fas fa-expand"></i> Enable Fullscreen
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Row -->
                        <div class="row mb-3">
                            <div class="col-md-3 d-flex align-items-center">
                                <button type="button" id="refreshBtn" class="btn btn-primary btn-block">
                                    <i class="fas fa-sync-alt"></i> Syncron With DB
                                </button>

                            </div>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Series Name</th>
                                        <th>No IO</th>
                                        <th>Customer Name</th>
                                        <th>Project Name</th>
                                        {{-- <th>From Warehouse</th>
                                        <th>To Warehouse</th> --}}
                                        <th>Recent Transfer</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invtf as $item)
                                        <tr>
                                            <td>{{ $item['SeriesName'] ?? '-' }}</td>
                                            <td>{{ $item['U_MEB_NO_IO'] ?? '-' }}</td>
                                            <td>{{ $item['CardName'] ?? '-' }}</td>
                                            <td>{{ $item['PrjName'] ?? '-' }}</td>
                                            {{-- <td>{{ $item['FromWhsCode'] ?? '-' }}</td>
                                            <td>{{ $item['ToWhsCode'] ?? '-' }}</td> --}}
                                            <td>{{ $item['RecentTransfer'] ?? '-' }}</td>
                                            <td>{{ $item['progress']['progress_percent'] ?? 0 }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        window.addEventListener("load", function() {
            const selectSeries = $("#seriesSelect");
            const selectedSeries = "{{ request()->get('series') ?? '' }}"; // nilai default

            $('#refreshBtn').on('click', function() {
                const series = selectSeries.val() || "";
                const noIO = $("#U_MEB_NO_IO").val() || "";
                const page = "{{ $page ?? 1 }}";
                const limit = "{{ $limit ?? 10 }}";

                $.ajax({
                    url: "/admin/sync-inventory-progress",
                    type: "GET",
                    data: {
                        series,
                        no_io: noIO,
                        page,
                        limit
                    },
                    beforeSend: function() {
                        $('#refreshBtn').prop('disabled', true)
                            .html('<i class="fas fa-spinner fa-spin"></i> Syncing...');
                    },
                    success: function(res) {
                        alert(res.message);
                        // location.reload();
                    },
                    error: function(xhr) {
                        alert("Error: " + xhr.responseText);
                    },
                    complete: function() {
                        $('#refreshBtn').prop('disabled', false)
                            .html('<i class="fas fa-sync-alt"></i> Syncron With DB');
                    }
                });
            });

            if (selectedSeries) {
                $.ajax({
                    url: "/purchasing/seriesSearch",
                    data: {
                        Series: selectedSeries,
                        ObjectCode: "67"
                    },
                    dataType: "json"
                }).then(function(data) {
                    if (data.results && data.results.length > 0) {
                        let item = data.results[0];
                        let option = new Option(item.text, item.id, true, true);
                        selectSeries.append(option).trigger("change");
                    }
                });
            }

            selectSeries.select2({
                placeholder: "Ketik kode series...",
                allowClear: true,
                width: "100%",
                language: {
                    inputTooShort: () => "Ketik kode series untuk mencari...",
                    noResults: () => "Tidak ada data ditemukan",
                    searching: () => "Sedang mencari..."
                },
                ajax: {
                    url: "/purchasing/seriesSearch",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            ObjectCode: '67'
                        };
                    },
                    processResults: function(data) {
                        console.log("Response dari server:", data);
                        return {
                            results: (data.results || []).map(item => ({
                                id: item.id,
                                text: item.text
                            }))
                        };
                    }
                }
            });
            setDefaultSeries("#seriesSelect", '67');
        });
    </script>
@endsection
