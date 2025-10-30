@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col col-sm-6">
                        <h1>Semi Finish Goods</h1>
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
                                    Search List Semi Finish Goods
                                </h3>
                            </div>
                            <form action="" method="get">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-2">
                                            <label for="">IO No</label>
                                            <input type="text" name="io" class="form-control"
                                                value="{{ request('io') }}" placeholder="Enter Nomor IO">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Product Order</label>
                                            <input type="text" name="prod_order" class="form-control"
                                                value="{{ request('prod_order') }}" placeholder="Enter Product Order">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Product No</label>
                                            <input type="text" name="prod_no" id="prod_no" class="form-control"
                                                value="{{ request('prod_no') }}" placeholder="Enter Product Nomor">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="">Product Desc</label>
                                            <input type="text" name="prod_desc" class="form-control"
                                                value="{{ request('prod_desc') }}" placeholder="Enter Product Description">
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="series">Series</label>
                                            <select name="series" class="form-control" id="seriesSelect">
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn btn-primary" style="margin-top: 20px"><i
                                                    class="fa fa-search"></i> Search</button>
                                            <a href="{{ url('admin/reports/semifg') }}" class="btn btn-warning"
                                                style="margin-top: 20px"><i class="fa fa-eraser"></i> Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('_message')
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List Semi Finish Goods</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>IO</th>
                                                <th>Prod Order</th>
                                                <th>Prod Nomor</th>
                                                <th>Prod Description</th>
                                                <th>Series</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($getRecord as $semifg)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $semifg->io }}</td>
                                                    <td>{{ $semifg->prod_order }}</td>
                                                    <td><a
                                                            href="{{ url('admin/production/view?docEntry=' . $semifg->doc_entry . '&docNum=' . $semifg->prod_order) }}">{{ $semifg->prod_no }}</a>
                                                    <td>{{ $semifg->prod_desc }}</td>
                                                    <td>
                                                        @if (!empty($series) && isset($series['ObjectCode'], $series['SeriesName']))
                                                            {{ $series['SeriesName'] }}
                                                        @else
                                                            <span class="text-danger">⚠️ Series tidak ditemukan:
                                                                {{ $getRecord['series'] }}</span>
                                                        @endif
                                                    </td>
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
                            <div class="card-footer">
                                <div class="d-flex justify-content-end px-2 py-2">
                                    <div style="overflow-x: auto; max-width:100%">
                                        {!! $getRecord->onEachSide(1)->appends(request()->except('page'))->links() !!}
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
                        ObjectCode: "22"
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
                            ObjectCode: '22'
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
