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
                            <a href="{{ url('admin/production/upload') }}" class="btn btn-primary btn-sm"><i
                                    class="fa fa-upload"></i> Upload Data</a>
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
                                            <label for="">Date</label>
                                            <input type="date" name="date" class="form-control">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">IO No</label>
                                            <input type="text" name="io_no" class="form-control"
                                                placeholder="Enter Nomor IO" value="{{ Request()->io_no }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Doc Number</label>
                                            <input type="number" name="doc_num" class="form-control"
                                                placeholder="Enter Doc Nomor" value="{{ Request()->doc_num }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Product No</label>
                                            <input type="text" name="prod_no" class="form-control"
                                                placeholder="Enter Product Nomor" value="{{ Request()->prod_no }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Product Desc</label>
                                            <input type="text" name="prod_desc" class="form-control"
                                                placeholder="Enter Product Description" value="{{ Request()->prod_desc }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="" disabled>-- Choose Status --</option>
                                                @foreach (['Released', 'Close', 'Cancelled', 'All'] as $status)
                                                    @php
                                                        $value = $status == 'Released' ? 'Released' : $status;
                                                    @endphp
                                                    <option value="{{ $value }}"
                                                        {{ Request()->status == $value ? 'selected' : '' }}>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="series">Series</label>
                                            <select name="series" class="form-control" id="seriesSelect">
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 20px"><i
                                                    class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/production/po') }}" class="btn btn-warning"
                                                style="margin-top: 20px"><i class="fa fa-eraser"></i> Reset</a>
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
                                        @forelse ($getProds as $production)
                                            <tbody>
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $production['ItemCode'] }}</td>
                                                    <td>{{ $production['ItemName'] }}</td>
                                                    <td>-</td>
                                                    <td>{{ $production['DocNum'] }}</td>
                                                    <td>{{ $production['U_MEB_NO_IO'] }}</td>
                                                    <td>{{ $production['DueDate'] }}</td>
                                                    <td>
                                                        @if ($production['Status'] == 'Released')
                                                            <a href="{{ url('admin/transaction/stockout?docNum=' . $production['DocNum'] . '&docEntry=' . $production['DocEntry']) }}"
                                                                class="btn btn-sm btn-outline-success"><i
                                                                    class="fa fa-arrow-right"></i> Released</a>
                                                        @else
                                                            {{ $production['Status'] }}
                                                        @endif
                                                    </td>

                                                    <td><a href="{{ url('admin/production/view?docEntry=' . $production['DocEntry'] . '&docNum=' . $production['DocNum']) }}"
                                                            class="btn btn-primary"><i class="fa fa-eye"></i></a></td>
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
                                            <a href="{{ url()->current() . '?' . http_build_query(array_merge($query, ['page' => 1, 'limit' => $limit])) }}"
                                                class="btn btn-outline-primary btn-sm" aria-label="First Page">First</a>

                                            <a href="{{ url()->current() . '?' . http_build_query(array_merge($query, ['page' => $page - 1, 'limit' => $limit])) }}"
                                                class="btn btn-outline-primary btn-sm"
                                                aria-label="Previous Page">Previous</a>
                                        @endif

                                        {{-- Current Page --}}
                                        <span class="btn btn-primary btn-sm disabled">
                                            {{ $page }}
                                        </span>

                                        {{-- Next + Last --}}
                                        @if ($page < $totalPages)
                                            <a href="{{ url()->current() . '?' . http_build_query(array_merge($query, ['page' => $page + 1, 'limit' => $limit])) }}"
                                                class="btn btn-outline-primary btn-sm" aria-label="Next Page">Next</a>

                                            <a href="{{ url()->current() . '?' . http_build_query(array_merge($query, ['page' => $totalPages, 'limit' => $limit])) }}"
                                                class="btn btn-outline-primary btn-sm" aria-label="Last Page">Last</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>
    <script>
        window.addEventListener("load", function() {
            const selectSeries = $("#seriesSelect");
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
                            ObjectCode: '202'
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
@endsection
