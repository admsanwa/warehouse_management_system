@extends("backend.layouts.app")
@section("content")
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>History Quality Control</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"> 
                                    Search Quality Control List
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">IO No</label>
                                            <input type="text" name="io_no" class="form-control"
                                                placeholder="Enter Nomor IO" value="{{ Request()->io_no }}">
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
                                            <label for="status">Status QC</label>
                                            <select name="qc_status" id="qc_status" class="form-control">
                                                <option value="">Select Status QC</option>
                                                <option value="1" {{ request('qc_status') == '1' ? 'selected' : '' }}>OK</option>
                                                <option value="2" {{ request('qc_status') == '2' ? 'selected' : '' }}>NG</option>
                                                <option value="3" {{ request('qc_status') == '3' ? 'selected' : '' }}>Need Approval</option>
                                                <option value="4" {{ request('qc_status') == '4' ? 'selected' : '' }}>Need Paint</option>
                                                <option value="5" {{ request('qc_status') == '5' ? 'selected' : '' }}>Painting by Inhouse</option>
                                                <option value="6" {{ request('qc_status') == '6' ? 'selected' : '' }}>Painting by Maklon</option>
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
                                            <a href="{{ url('admin/quality/history') }}" class="btn btn-warning"
                                                style="margin-top: 20px"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include("_message")
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">History Quality Control</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Product Nomer</th>
                                                <th>Description</th>
                                                <th>IO</th>
                                                <th>Due Date</th>
                                                <th>QC</th>
                                                <th>Result By</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($mergedData as $prod)
                                                @php
                                                    $sap = $prod['sap'];
                                                    $quality = $prod['quality'];
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        @if ($sap)
                                                            <a href="{{ url('admin/production/view?docEntry=' . $sap['DocEntry'] . '&docNum=' . $sap['DocNum']) }}">
                                                                {{ $sap['ItemCode'] }}
                                                            </a>
                                                        @else
                                                            {{ $quality->item_code }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $sap['ItemName'] ?? '-' }}</td>
                                                    <td>{{ $sap['U_MEB_NO_IO'] ?? '-' }}</td>
                                                    <td>{{ $sap['DueDate'] ?? '-' }}</td>
                                                    <td>
                                                        @php
                                                            $statusMap = [
                                                                1 => 'OK',
                                                                2 => 'NG',
                                                                3 => 'Need Approval',
                                                                4 => 'Need Paint',
                                                                5 => 'Painting by Inhouse',
                                                                6 => 'Painting by Makloon'
                                                            ];
                                                        @endphp

                                                        @if ($quality && $quality->result != null)
                                                            {{ $statusMap[$quality->result] ?? '-' }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($quality && $quality->result_by != null)
                                                            {{ $quality->result_by}}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>{{ $quality->remark ?? '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="100%">No Record Found</td>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                        ObjectCode: "202"
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