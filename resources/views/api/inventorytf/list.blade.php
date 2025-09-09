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
                            <a href="{{ url('admin/inventorytf/create') }}" class="btn btn-primary btn-sm"> Create Inventory
                                TF</a>
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
                                            <input type="text" name="number" class="form-control"
                                                placeholder="Enter Number Inventory Transfer"
                                                value="{{ Request()->number }}">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Posting Date</label>
                                            <input type="date" name="date" class="form-control"
                                                value="{{ Request()->date }}">
                                        </div>
                                        {{-- <div class="form-group col-md-2">
                                            <label for="">IO</label>
                                            <input type="text" name="U_MEB_NO_IO" class="form-control"
                                                placeholder="Enter IO Nomor" value="{{ Request()->io_no }}">
                                        </div> --}}
                                        {{-- <div class="form-group col-md-2">
                                            <label for="">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                @foreach (['O' => 'Open', 'C' => 'Close'] as $value => $label)
                                                    <option value="{{ $value }}"
                                                        {{ request()->status == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div> --}}
                                        <div class="form-group col-md-2">
                                            <label for="">Series</label>
                                            <select name="series" id="seriesSelect" class="form-control">
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 30px"><i
                                                    class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/inventorytf/list') }}" class="btn btn-warning"
                                                style="margin-top: 30px"><i class="fa fa-eraser"></i> Reset</a>
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
                                                <th>No</th>
                                                <th>Doc Number</th>
                                                <th>Posting Date</th>
                                                <th>IO</th>
                                                <th>From Warehouse</th>
                                                <th>To Warehouse</th>
                                                <th>Status</th>
                                                <th>Remarks</th>
                                                <th>View Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($getInvtf as $inv)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $inv['DocNum'] }}</td>
                                                    <td>{{ $inv['DocDate'] }}</td>
                                                    <td>Tidak Ada</td>
                                                    <td>{{ $inv['FromWhsCode'] }}</td>
                                                    <td>{{ $inv['ToWhsCode'] }}</td>
                                                    <td>{{ $inv['DocStatus'] === 'O' ? 'Open' : 'Close' }}</td>
                                                    <td>{{ $inv['Comments'] }}</td>
                                                    <td>
                                                        <a href="{{ url('admin/inventorytf/view?docEntry=' . $inv['DocEntry'] . '&docNum=' . $inv['DocNum']) }}"
                                                            class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                    </td>
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
                                            {{-- 
                                            <a href="{{ url()->current() . '?' . http_build_query(array_merge($query, ['page' => $totalPages, 'limit' => $limit])) }}"
                                                class="btn btn-outline-primary btn-sm" aria-label="Last Page">Last</a> --}}
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
            let selectedSeries = "{{ request()->series }}";
            console.log(selectedSeries);
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
                            ObjectCode: '67'
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
