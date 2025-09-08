@extends('backend.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Stock</h1>
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
                                    Search Data Stock
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">Item Code</label>
                                            <input type="text" name="item_code" class="form-control"
                                                value="{{ Request()->item_code }}" placeholder="Enter Item Code">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Item Desc</label>
                                            <input type="text" name="item_desc" class="form-control"
                                                value="{{ Request()->item_desc }}" placeholder="Enter Item Name">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="stockNotes">Status Notes</label>
                                            <select name="stockNotes" id="stockNotes" class="form-control" disabled>
                                                @foreach ($stockStatus as $value => $label)
                                                    <option value="{{ $value }}"
                                                        {{ (string) request()->stockNotes === (string) $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="warehouse">Warehouse</label>
                                            <select name="warehouse" id="warehouse" class="form-control">
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 30px"><i
                                                    class="fa fa-search"></i> Search</button>
                                            <a href="{{ url()->current() }}" class="btn btn-warning"
                                                style="margin-top: 30px"><i class="fa fa-eraser"></i>Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    List of All Stock
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-stripped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Warehouse</th>
                                                <th>Item Code</th>
                                                <th>Item Desc</th>
                                                <th>Stock SAP</th>
                                                <th>Stock Min</th>
                                                <th>Available</th>
                                                <th>Notes</th>
                                                <th>Uom</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($getRecord as $stock)
                                                @php
                                                    $warehouseStock = collect($stock['warehouses'])->firstWhere(
                                                        'WhsCode',
                                                        $defaultWh,
                                                    );
                                                    $filter = (string) request()->stockNotes;
                                                @endphp
                                                {{-- @if ($filter === '' || ($filter === '1' && (string) $warehouseStock['Status'] === '1') || ($filter === '0' && (string) $warehouseStock['Status'] === '0')) --}}
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $warehouseStock['WhsCode'] ?? 'N/A' }}</td>
                                                    <td>{{ $stock['ItemCode'] ?? 'N/A' }}</td>
                                                    <td>{{ $stock['ItemName'] ?? 'N/A' }}</td>
                                                    <td>{{ formatDecimalsSAP($warehouseStock['OnHand']) ?? 'N/A' }}</td>
                                                    <td>{{ formatDecimalsSAP($warehouseStock['MinStock']) ?? 'N/A' }}</td>
                                                    <td>{{ formatDecimalsSAP($warehouseStock['Available']) ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        {{ $warehouseStock['Status'] === 1 ? 'Stock harus dibeli' : '' }}
                                                    </td>
                                                    <td>{{ $stock['InvntryUom'] ?? 'N/A' }}</td>
                                                </tr>
                                                {{-- @endif --}}
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

            const whSelect = $("#warehouse");
            if (whSelect.length) {
                let selectedSeries = "{{ request()->warehouse ?? 'BK001' }}";
                let option = new Option(selectedSeries, selectedSeries, true, true);
                whSelect.append(option).trigger("change");
                whSelect.select2({
                    placeholder: "Pilih Kode Warehouse",
                    allowClear: true,
                    width: "100%",
                    language: {
                        inputTooShort: function() {
                            return "Ketik untuk mencari...";
                        },
                        noResults: function() {
                            return "Tidak ada data ditemukan";
                        },
                        searching: function() {
                            return "Sedang mencari...";
                        }
                    },
                    ajax: {
                        url: "/warehouseSearch",
                        dataType: "json",
                        delay: 250,
                        data: function(params) {
                            let searchData = {
                                q: params.term,
                                limit: 10,
                            }
                            return searchData;
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
            }
        });
    </script>
@endsection
