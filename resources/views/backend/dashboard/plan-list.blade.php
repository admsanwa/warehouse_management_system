@extends('backend.layouts.app')

@section('content')
    <style>
        .maximized-card {
            position: relative;
            z-index: 10;
            width: 100%;
            height: 80vh;
            /* biar muat di layar */
            overflow-y: auto;
        }

        /* 👇 shrink font size to 10px */
        .small-text {
            font-size: 10px;
        }

        .small-text .form-control form-control-sm,
        .small-text .btn,
        .small-text .table {
            font-size: 10px;
        }

        .small-text .table th,
        .small-text .table td {
            padding: 4px 6px;
            /* biar tetap rapih */
        }
    </style>
    <div class="content-wrapper small-text">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard Plan</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active"></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Dashboard Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Instruction Order List</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" id="btnMaximize">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-body">
                            <!-- === Search Form pindah ke sini === -->
                            <form action="" method="get" class="mb-3">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="U_MEB_NO_IO">IO</label>
                                            <input type="text" id="U_MEB_NO_IO" name="U_MEB_NO_IO"
                                                class="form-control form-control-sm" placeholder="Enter IO Number"
                                                value="{{ request()->U_MEB_NO_IO }}">
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="series">Series</label>
                                            <select name="series" class="form-control form-control-sm"
                                                id="seriesSelect"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control form-control-sm">
                                                <option value="">-- All Status --</option>
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status }}"
                                                        {{ request()->status == $status ? 'selected' : '' }}>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-primary me-2">
                                                    <i class="fa fa-search"></i> Search
                                                </button>
                                                <a href="{{ url('admin/dashboard-list') }}" class="btn btn-warning">
                                                    <i class="fa fa-eraser"></i> Reset
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Series Name</th>
                                            <th>No IO</th>
                                            <th>No SO</th>
                                            <th>Customer Name</th>
                                            <th>Project Name</th>
                                            <th>From → To</th>
                                            <th>Progress</th>
                                            <th>Current Status</th>
                                            <th>Due Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($purchase_orders as $item)
                                            <tr>
                                                <td>{{ $item['SeriesName'] ?? '-' }}</td>
                                                <td>{{ $item['U_MEB_NO_IO'] ?? '-' }}</td>
                                                <td>{{ $item['DocNum'] ?? '-' }}</td>
                                                <td>{{ $item['CardName'] ?? '-' }}</td>
                                                <td>{{ $item['ProjectName'] ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $item['FromWhsCode'] ?? '-' }}
                                                    </span>
                                                    →
                                                    <span class="badge bg-success">
                                                        {{ $item['ToWhsCode'] ?? '-' }}
                                                    </span>
                                                </td>
                                                <td style="min-width:180px;">
                                                    <div class="progress progress-sm" style="height: 20px;">
                                                        <div class="progress-bar 
                                                            @if ($item['ProgressPercent'] < 30) bg-danger 
                                                            @elseif($item['ProgressPercent'] < 70) bg-warning 
                                                            @else bg-success @endif"
                                                            role="progressbar"
                                                            style="width: {{ $item['ProgressPercent'] ?? 0 }}%;">
                                                            {{ $item['ProgressPercent'] ?? 0 }}%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $item['CurrentStatus'] ?? '-' }}</td>
                                                <td>{{ $item['DocDueDate'] ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted">
                                                    No data available
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination info --}}
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        Showing page <b class="text-primary">{{ $page }}</b> of
                                        {{ $totalPages }} (Total {{ $total }} records)
                                    </span>
                                    <div class="btn-group">
                                        @php $query = request()->all(); @endphp

                                        {{-- Previous --}}
                                        @if ($page > 1)
                                            <a href="{{ url()->current() . '?' . http_build_query(array_merge($query, ['page' => $page - 1, 'limit' => $limit])) }}"
                                                class="btn btn-outline-primary btn-sm">Previous</a>
                                        @endif

                                        {{-- Current --}}
                                        <span class="btn btn-primary btn-sm disabled">{{ $page }}</span>

                                        {{-- Next --}}
                                        @if ($page < $totalPages)
                                            <a href="{{ url()->current() . '?' . http_build_query(array_merge($query, ['page' => $page + 1, 'limit' => $limit])) }}"
                                                class="btn btn-outline-primary btn-sm">Next</a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
        </section>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const btn = document.getElementById("btnMaximize");
                const card = btn.closest(".card"); // target card Instruction Order List
                const icon = btn.querySelector("i");

                // === Load state dari localStorage ===
                if (localStorage.getItem("instructionCardMaximized") === "true") {
                    card.classList.add("maximized-card");
                    icon.classList.remove("fa-expand");
                    icon.classList.add("fa-compress");
                }

                // === Toggle maximize ===
                btn.addEventListener("click", function() {
                    card.classList.toggle("maximized-card");

                    if (card.classList.contains("maximized-card")) {
                        localStorage.setItem("instructionCardMaximized", "true");
                        icon.classList.remove("fa-expand");
                        icon.classList.add("fa-compress");
                    } else {
                        localStorage.setItem("instructionCardMaximized", "false");
                        icon.classList.remove("fa-compress");
                        icon.classList.add("fa-expand");
                    }
                });
            });
        </script>

        <script>
            window.addEventListener("load", function() {
                const selectSeries = $("#seriesSelect");
                let selectedSeries = "{{ request()->series }}";
                console.log(selectedSeries);
                if (selectedSeries) {
                    $.ajax({
                        url: "/purchasing/seriesSearch",
                        data: {
                            Series: selectedSeries,
                            ObjectCode: "17"
                        },
                        dataType: "json"
                    }).then(function(data) {
                        if (data.results && data.results.length > 0) {
                            let item = data.results[0]; // ambil hasil pertama
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
                        inputTooShort: function() {
                            return "Ketik kode series untuk mencari...";
                        },
                        noResults: function() {
                            return "Tidak ada data ditemukan";
                        },
                        searching: function() {
                            return "Sedang mencari...";
                        },
                    },
                    ajax: {
                        url: "/purchasing/seriesSearch",
                        dataType: "json",
                        delay: 250,
                        data: function(params) {
                            if (!params) {
                                return;
                            }
                            return {
                                q: params.term,
                                ObjectCode: '17'
                            };
                        },
                        processResults: function(data) {
                            console.log("Response dari server:", data); // cek di console
                            return {
                                results: (data.results || []).map(item => ({
                                    id: item.id,
                                    text: item.text
                                }))
                            };
                        }
                    }
                });
            });
        </script>
    </div>
@endsection
