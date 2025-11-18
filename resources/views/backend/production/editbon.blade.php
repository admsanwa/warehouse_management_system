@extends('backend.layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col col-sm-6">
                        <h1>BON Pembelian Barang</h1>
                    </div>
                    <div class="col col-sm-6">
                        <ol class="breadcrumb justify-content-end">
                            <a href="{{ url('admin/production/listbon') }}" class="btn btn-primary btn-sm">List Bon</a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <section class="col col-md-12">
                        <div class="card">
                            @include('_message')
                            <div class="card-header">
                                <h3 class="card-title">Create BON</h3>
                            </div>
                            <form action="{{ url('admin/production/updatebon/' . $getRecord->id) }}" class="form-horizontal"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Tipe Bon :</label>
                                        <div class="col-sm-6">
                                            <select name="type" id="type" class="form-control">
                                                <option value="">Select tipe bon Lokal/Import</option>
                                                <option value="Lokal" {{ $getRecord->type == 'Lokal' ? 'selected' : '' }}>
                                                    Lokal</option>
                                                <option value="Import" {{ $getRecord->type == 'Import' ? 'selected' : '' }}>
                                                    Import</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Nomer :</label>
                                        <div class="col-sm-6">
                                            <input name="no" id="no" value="{{ $getRecord->no }}"
                                                class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Tanggal :</label>
                                        <div class="col-sm-6">
                                            <input type="date" name="date" value="{{ $getRecord->date }}"
                                                id="date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Bagian :</label>
                                        <div class="col-sm-6">
                                            <input type="text" name="section" id="section" class="form-control"
                                                value="PPIC" placeholder="Masukkan Bagian" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Pemesan :</label>
                                        <div class="col-sm-6">
                                            <input type="text" name="created_by" id="created_by" class="form-control"
                                                value="{{ $user->fullname }}" placeholder="Masukkan Pemasan" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-label">IO :</label>
                                        <div class="col-sm-6">
                                            <input type="text" name="io" id="io" class="form-control"
                                                value="{{ $getRecord->io }}" placeholder="Masukkan io">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-label">Project :</label>
                                        <div class="col-sm-6">
                                            <input type="text" name="project" id="project" class="form-control"
                                                value="{{ $getRecord->project }}" placeholder="Masukkan Project">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-3 col-form-lable">Make to Order/Stock :</label>
                                        <div class="col-sm-6">
                                            <select name="make_to" id="make_to" class="form-control">
                                                <option value="">Make to Order/Stock</option>
                                                <option value="Stock"
                                                    {{ $getRecord->make_to == 'Stock' ? 'selected' : '' }}>Stock</option>
                                                <option value="Order"
                                                    {{ $getRecord->make_to == 'Order' ? 'selected' : '' }}>Order</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="unitContainer">
                                        @foreach ($getRecord->details as $detail)
                                            <div class="unit-block mb-3 p-3 border rounded bg-light">
                                                <div class="form-row align-items-end">
                                                    <!-- Nama Barang -->
                                                    <div class="form-group col-md-4">
                                                        <label for="item_code">Nama Barang</label>
                                                        <select name="item_code[]" class="select2 form-control"
                                                            style="width: 100%">
                                                            <option value="{{ $detail->item_code }}">
                                                                {{ $detail->item_name }} </option>
                                                        </select>
                                                        <input type="hidden" name="item_desc[]" class="form-control"
                                                            value="{{ $detail->item_name }}" placeholder="Masukkan Uom"
                                                            readonly>

                                                    </div>

                                                    <!-- UOM -->
                                                    <div class="form-group col-md-2">
                                                        <label for="uom">UOM</label>
                                                        <input type="text" name="uom[]" class="form-control"
                                                            value="{{ $detail->uom }}" placeholder="Masukkan Uom"
                                                            readonly>
                                                    </div>

                                                    <!-- Qty -->
                                                    <div class="form-group col-md-2">
                                                        <label for="qty">Qty</label>
                                                        <input type="number" name="qty[]" class="form-control"
                                                            value="{{ $detail->qty }}" placeholder="Masukkan Qty">
                                                    </div>

                                                    <!-- Keterangan -->
                                                    <div class="form-group col-md-3">
                                                        <label for="remark">Keterangan</label>
                                                        <input type="text" name="remark[]" class="form-control"
                                                            value="{{ $detail->remark }}"
                                                            placeholder="Masukkan Keterangan">
                                                    </div>

                                                    <!-- Action buttons -->
                                                    <div class="form-group col-md-1 text-center">
                                                        <button type="button"
                                                            class="btn btn-sm btn-success btn-add-unit">
                                                            <i class="fa fa-plus-circle"></i>
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger btn-remove-unit">
                                                            <i class="fa fa-minus-circle"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Hidden clean template --}}
                                    <template id="unitTemplate">
                                        <div class="unit-block mb-3 p-3 border rounded bg-light">
                                            <div class="form-row align-items-end">
                                                <!-- Nama Barang -->
                                                <div class="form-group col-md-4">
                                                    <label>Nama Barang</label>
                                                    <select name="item_code[]" class="select2 form-control"
                                                        style="width: 100%">
                                                    </select>
                                                    <input type="hidden" name="item_desc[]" class="form-control"
                                                        placeholder="Masukkan Uom" readonly>
                                                </div>

                                                <!-- UOM -->
                                                <div class="form-group col-md-2">
                                                    <label>Uom</label>
                                                    <input type="text" name="uom[]" class="form-control"
                                                        placeholder="Masukkan Uom" readonly>
                                                </div>

                                                <!-- Qty -->
                                                <div class="form-group col-md-2">
                                                    <label>Qty</label>
                                                    <input type="number" name="qty[]" class="form-control"
                                                        placeholder="Masukkan Qty">
                                                </div>

                                                <!-- Keterangan -->
                                                <div class="form-group col-md-3">
                                                    <label>Keterangan</label>
                                                    <input type="text" name="remark[]" class="form-control"
                                                        placeholder="Masukkan Keterangan">
                                                </div>

                                                <!-- Action buttons -->
                                                <div class="form-group col-md-1 text-center">
                                                    <button type="button" class="btn btn-sm btn-success btn-add-unit">
                                                        <i class="fa fa-plus-circle"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger btn-remove-unit">
                                                        <i class="fa fa-minus-circle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i>
                                        Update
                                    </button>
                                    <button onclick="history.back()" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            function initSelect2($el) {
                $el.select2({
                    placeholder: "Ketik kode barang...",
                    allowClear: true,
                    width: "100%",
                    language: {
                        inputTooShort: function() {
                            return "Ketik kode barang untuk mencari...";
                        },
                        noResults: function() {
                            return "Tidak ada data ditemukan";
                        },
                        searching: function() {
                            return "Sedang mencari...";
                        },
                    },
                    ajax: {
                        url: "/onhandSearch",
                        dataType: "json",
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: (data.results || []).map((item) => ({
                                    id: item.id,
                                    text: item.text,
                                    uom: item.uom,
                                    item_desc: item.item_desc,
                                })),
                            };
                        },
                    },
                });

                // Event ketika pilih barang
                $el.on("select2:select", function(e) {
                    let data = e.params.data;
                    let $block = $(this).closest(".unit-block");
                    $block.find('input[name="uom[]"]').val(data.uom || "");
                    $block.find('input[name="item_desc[]"]').val(data.item_desc || "");
                });
            }

            initSelect2($(".select2"));

            $(document).on("click", ".btn-add-unit", function() {
                let clone = $($("#unitTemplate").html());
                $("#unitContainer").append(clone);

                initSelect2(clone.find(".select2"));
            });

            $(document).on("click", ".btn-remove-unit", function() {
                $(this).closest(".unit-block").remove();
            });

            // select series
            const currentYear = new Date().getFullYear().toString().slice(-2);
            const defaultText = `BKS-${currentYear}`;

            $("#IOSeriesSelect").select2({
                placeholder: "Choose Series",
                allowClear: true,
                width: "100%",
                language: {
                    inputTooShort: function() {
                        return "Type series code for search...";
                    },
                    noResults: function() {
                        return "Data not found";
                    },
                    searching: function() {
                        return "Still Loading...";
                    }
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

            // After Select2 init, load and set default BKS-YY
            $.ajax({
                url: "/purchasing/seriesSearch",
                data: {
                    q: '',
                    ObjectCode: '202'
                },
                dataType: "json",
                success: function(data) {
                    if (data.results && data.results.length > 0) {
                        // Find the default one based on text like "BKS-25"
                        const found = data.results.find(
                            item => item.text.toUpperCase() === defaultText.toUpperCase()
                        );

                        if (found) {
                            // Append and trigger change to show selected value
                            const option = new Option(found.text, found.id, true, true);
                            $("#IOSeriesSelect").append(option).trigger('change');
                        }
                    }
                }
            });

            const ioSelect = $("#U_MEB_NO_IO");
            // ioSelect.on("change", function(e) {
            //     clearIOData();
            //     const selectedData = $(this).select2('data')[];

            //     if (!selectedData) {
            //         clearIOData();
            //         return;
            //     }
            // }); 

            $("#IOSeriesSelect").on("change", function() {
                $("#U_MEB_NO_IO").val(null).trigger("change"); // clear IO select2
            });

            const series = $("#IOSeriesSelect").val();


            console.log("📤 Sending request with series:", series);


            ioSelect.select2({
                placeholder: "Select IO Nomor",
                allowClear: true,
                width: "100%",
                minimumInputLength: 0,
                language: {
                    inputTooShort: function() {
                        return "Type 3 character or more";
                    },
                    noResults: () => "Data Not found",
                    searching: () => "Still searching...",
                },
                ajax: {
                    url: "/salesOrderSearch",
                    dataType: "json",
                    delay: 600,
                    data: function(params) {
                        const seriesData = $("#IOSeriesSelect").select2('data');
                        const series = seriesData && seriesData.length > 0 ? seriesData[0].id : null;

                        if (!series) {
                            console.warn("No series selected. Blocking search request.");
                            return false; // ❗ prevent request if no series
                        }
                        return {
                            q: params.term || '',
                            series: series,
                            limit: 5,
                        }
                    },
                    processResults: function(data) {
                        tempSalesOrderData = data.prods || [];

                        return {
                            results: (data.results || []).map(item => ({
                                id: item.id,
                                text: item.text,
                                CardName: item.CardName,
                            }))
                        };
                    },
                    cache: true
                }
            });

            ioSelect.on("select2:open", function() {
                let searchField = document.querySelector(".select2-container .select2-search_field");
                if (searchField) {
                    searchField.placeholder = "Type here for searching io nomor";
                }
            })

            function clearIOData() {
                $("#U_MEB_NO_IO").val("");
                $("#project").val("");
                $("#make_to").val("");

                const $projSelect = $("#make_to");
                if ($projSelect.length && $projSelect.is("select")) {
                    $projSelect.val(null).trigger("change"); // reset value
                    $projSelect.find("option").remove();
                }
            }
        });
    </script>
@endsection
